<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FillSvalyavaTable extends Command
{
    protected $signature = 'svalyava:fill {street?}';
    protected $description = 'Fill svalyava table with data from residence_data and personal_data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 3600);

        $street = $this->argument('street') ?? '%';
        $this->info('Starting to fill svalyava table for street: ' . ($street === '%' ? 'all streets' : $street));

        if ($street === '%') {
            DB::table('svalyava')->truncate();
            $this->info('Table svalyava truncated.');
        }

        $query = DB::table('residence_data')
            ->select(
                'residence_data.residence_locality',
                'residence_data.residence_street',
                'residence_data.house_number',
                'residence_data.house_letter',
                'residence_data.apartment_number',
                'residence_data.drfo_new',
                'residence_data.region_id',
                'personal_data.surname',
                'personal_data.first_name',
                'personal_data.patronymic',
                DB::raw("CASE WHEN personal_data.birth_date = '1900-00-00' THEN NULL ELSE personal_data.birth_date END as birth_date")
            )
            ->join('personal_data', 'residence_data.drfo_new', '=', 'personal_data.drfo_new')
            ->where('residence_data.region_id', 26)
            ->whereRaw('LOWER(TRIM(residence_data.residence_locality)) = ?', [mb_strtolower('М.СВАЛЯВА', 'UTF-8')])
            ->orderBy('residence_data.drfo_new');

        if ($street !== '%') {
            $query->whereRaw('LOWER(TRIM(residence_data.residence_street)) = ?', [mb_strtolower($street, 'UTF-8')]);
        }

        $chunkSize = 1000;
        $totalRows = 0;
        $errorRows = 0;

        $query->chunk($chunkSize, function ($rows) use (&$totalRows, &$errorRows) {
            $insertData = $rows->map(function ($row) use (&$errorRows) {
                if (empty($row->drfo_new)) {
                    Log::warning('Skipping row with empty drfo_new', (array) $row);
                    $errorRows++;
                    return null;
                }

                return [
                    'residence_locality' => $row->residence_locality,
                    'residence_street' => $row->residence_street,
                    'house_number' => $row->house_number,
                    'house_letter' => $row->house_letter,
                    'apartment_number' => $row->apartment_number,
                    'drfo_new' => $row->drfo_new,
                    'region_id' => $row->region_id,
                    'surname' => $row->surname,
                    'first_name' => $row->first_name,
                    'patronymic' => $row->patronymic,
                    'birth_date' => $row->birth_date,
                ];
            })->filter()->toArray();

            if (!empty($insertData)) {
                try {
                    DB::table('svalyava')->insert($insertData);
                    $totalRows += count($insertData);
                    $this->info("Inserted $totalRows rows so far...");
                } catch (\Exception $e) {
                    Log::error('Error inserting chunk: ' . $e->getMessage(), ['chunk' => $insertData]);
                    $errorRows += count($insertData);
                }
            }
        });

        if ($totalRows === 0) {
            $this->error('No valid data found for region_id=26 and residence_locality=М.СВАЛЯВА' . ($street !== '%' ? " and street=$street" : ''));
            return;
        }

        $this->info('Svalyava table filled successfully with ' . $totalRows . ' rows.');
        if ($errorRows > 0) {
            $this->warn("Skipped $errorRows rows due to invalid data.");
        }
    }
}