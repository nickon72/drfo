<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExportKirovograd extends Command
{
    protected $signature = 'kirovograd:export {residence_locality=КІРОВОГРАД} {region_id=25} {residence_street=%}';
    protected $description = 'Export kirovograd table to CSV';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 7200);

        $residence_locality = trim($this->argument('residence_locality'));
        $region_id = (int) $this->argument('region_id');
        $residence_street = trim($this->argument('residence_street'));

        $this->info("Starting export for Kirovograd: locality=$residence_locality, region_id=$region_id, street=$residence_street");

        $query = DB::table('kirovograd')
            ->select(
                'residence_locality',
                'residence_street',
                'house_number',
                'house_letter',
                'apartment_number',
                'drfo_new',
                'surname',
                'first_name',
                'patronymic',
                'birth_date'
            )
            ->where('region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')]);

        if ($residence_street !== '%') {
            $query->whereRaw('LOWER(TRIM(residence_street)) LIKE ?', ['%' . mb_strtolower($residence_street, 'UTF-8') . '%']);
        }

        $chunkSize = 10000;
        $totalRows = 0;
        $filePath = storage_path('app/public/kirovograd.csv');

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Открываем файл с BOM для UTF-8
        $file = fopen($filePath, 'w');
        fwrite($file, "\xEF\xBB\xBF"); // Добавляем BOM
        $headers = [
            'Город', 'Улица', 'Дом', 'Буква дома', 'Квартира',
            'ИД Код', 'Фамилия', 'Имя', 'Отчество', 'Дата рождения'
        ];
        fputcsv($file, $headers);
        $this->info("Added headers to $filePath");

        $query->orderBy('apartment_number')->chunk($chunkSize, function ($rows) use (&$totalRows, $file) {
            foreach ($rows as $row) {
                $csvRow = [
                    $row->residence_locality ?? '',
                    $row->residence_street ?? '',
                    $row->house_number ?? '',
                    $row->house_letter ?? '',
                    $row->apartment_number ?? '',
                    $row->drfo_new ?? '',
                    $row->surname ?? '',
                    $row->first_name ?? '',
                    $row->patronymic ?? '',
                    $row->birth_date ?? '',
                ];
                fputcsv($file, $csvRow);
            }
            $totalRows += count($rows);
            $this->info("Processed $totalRows rows...");
        });

        fclose($file);

        if ($totalRows === 0) {
            $this->error('No data found for export.');
            Log::warning('No data found for Kirovograd export', compact('residence_locality', 'region_id', 'residence_street'));
            return;
        }

        $this->info("Export completed: $totalRows rows written to $filePath");
        Log::info('Kirovograd export completed', ['rows' => $totalRows, 'file' => $filePath]);
    }
}