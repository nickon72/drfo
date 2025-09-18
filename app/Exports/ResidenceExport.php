<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;

class ResidenceExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithChunkReading
{
    protected $data;

    public function __construct($data)
    {
        // Приводим данные к коллекции, если это не коллекция
        $this->data = $data instanceof Collection ? $data : collect($data);
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Город',
            'Улица',
            'Дом',
            'Буква дома',
            'Квартира',
            'ИД Код',
            'Фамилия',
            'Имя',
            'Отчество',
            'Дата рождения',
        ];
    }

    public function map($row): array
    {
        // Защита от null или некорректных данных
        return [
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
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Город
            'B' => 40, // Улица
            'C' => 10, // Дом
            'D' => 10, // Буква дома
            'E' => 10, // Квартира
            'F' => 15, // ИД Код
            'G' => 40, // Фамилия
            'H' => 40, // Имя
            'I' => 40, // Отчество
            'J' => 15, // Дата рождения
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}