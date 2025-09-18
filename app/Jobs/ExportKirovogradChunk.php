<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExportKirovogradChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $data;
    protected $filePath;

    public function __construct($data, $filePath)
    {
        $this->data = $data instanceof Collection ? $data : collect($data);
        $this->filePath = $filePath;
    }

    public function handle()
    {
        try {
            $csvContent = '';
            foreach ($this->data as $row) {
                $csvRow = [
                    addslashes($row->residence_locality ?? ''),
                    addslashes($row->residence_street ?? ''),
                    addslashes($row->house_number ?? ''),
                    addslashes($row->house_letter ?? ''),
                    addslashes($row->apartment_number ?? ''),
                    addslashes($row->drfo_new ?? ''),
                    addslashes($row->surname ?? ''),
                    addslashes($row->first_name ?? ''),
                    addslashes($row->patronymic ?? ''),
                    addslashes($row->birth_date ?? ''),
                ];
                $csvContent .= implode(',', $csvRow) . PHP_EOL;
            }

            Storage::disk('local')->put($this->filePath, $csvContent);
            Log::info('Chunk written to file', ['file' => $this->filePath, 'rows' => $this->data->count()]);
        } catch (\Exception $e) {
            Log::error('Error writing chunk', ['file' => $this->filePath, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}