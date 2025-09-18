<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class Kirovograd extends Model
{
    protected $table = 'kirovograd';
    protected $fillable = [
        'residence_locality',
        'residence_street',
        'house_number',
        'house_letter',
        'apartment_number',
        'drfo_new',
        'region_id',
        'surname',
        'first_name',
        'patronymic',
        'birth_date',
    ];

    public static function get_kirovograd_data($residence_locality, $region_id, $residence_street, $house_number = '%')
    {
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);
        $region_id = (int) $region_id;

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

        if ($house_number !== '%') {
            $query->whereRaw('LOWER(TRIM(house_number)) = ?', [mb_strtolower($house_number, 'UTF-8')]);
        }

        return $query->orderBy('house_number')->get();
    }

    public static function get_kirovograd()
    {

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
            );

        return $query->orderBy('residence_street')->get();
    }

    //Выбирает уникальные улицы из запроса по конкретному городу и региону
//    public static function get_gorod_residence()
//    {
//        $kirovograd = DB::table('kirovograd')
//            ->select(
//                DB::raw('MAX(residence_locality) as residence_locality'),
//                'residence_street',
//                DB::raw('MAX(region_id) as region_id')
//            )
//            ->groupBy('residence_street')
//            ->orderBy('residence_street')
//            ->get();
//
//        return $kirovograd;
//    }

    public static function getResidenceData($surname, $first_name = null, $patronymic = null)
    {

        $residence = DB::table('kirovograd')
            ->select(
                'surname',
                'first_name',
                'patronymic',
                'birth_date',
                'residence_locality',
                'drfo_new',
                'residence_street',
                'house_number'
            )
            ->where('surname', 'like', '%' . $surname . '%')
            ->when($first_name, function ($query) use ($first_name) {
                return $query->where('first_name', 'like', '%' . $first_name . '%');
            })
            ->when($patronymic, function ($query) use ($patronymic) {
                return $query->where(function ($q) use ($patronymic) {
                    $q->where('patronymic', 'like', '%' . $patronymic . '%')
                        ->orWhereNull('patronymic')
                        ->orWhere('patronymic', '');
                });
            })
            ->get();

        return $residence;
    }



    //Выбирает уникальные улицы из запроса по конкретному городу и региону
    public static function get_gorod_residence()
    {
        $kirovograd = DB::table('kirovograd')
            ->select(
                'residence_locality',
                'residence_street',
                'region_id'
            )
            ->distinct()
            ->orderBy('residence_street')
            ->get();

        return $kirovograd;
    }

    // поиск по Кировограду улиц номер дома буква дома
    public static function get_gorod_residence_street_any($residence_street)
    {


        $residence_street = trim($residence_street);


        $kirovograd = DB::table('kirovograd')
            ->select(
                'residence_locality',
                'residence_street',
                'house_number',
                'house_letter',
                'region_id' // Додано region_id
            )

            ->whereRaw('LOWER(TRIM(residence_street)) = ?', [mb_strtolower($residence_street, 'UTF-8')])
            ->distinct()
            ->orderBy('residence_street')
            ->get();

        return $kirovograd;
    }

//исправленная функция поиска по дому и букве дома, если буквы нет.
    public static function get_gorod_residence_street_house($residence_street, $house_number, $house_letter)
    {
        // Подготовка входных параметров
        $residence_locality = 'КІРОВОГРАД';
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);
        $house_letter = $house_letter !== null ? trim($house_letter) : null;
        $region_id = 25;

        // Если house_letter пустая строка, считать её null
        if ($house_letter === '') {
            $house_letter = null;
        }

        $query = DB::table('kirovograd')
            ->select(
                'residence_locality',
                'residence_street',
                'house_number',
                'house_letter',
                'apartment_number',
                'drfo_new',
                'region_id'
            )
            ->where('region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')])
            ->whereRaw('LOWER(TRIM(residence_street)) LIKE ?', ['%' . mb_strtolower($residence_street, 'UTF-8') . '%'])
       //     ->whereRaw('LOWER(TRIM(house_number)) LIKE ?', ['%' . mb_strtolower($house_number, 'UTF-8') . '%']);
       ->where('house_number', $house_number);

        // Обработка house_letter
        if ($house_letter === null) {
            $query->whereRaw('house_letter IS NULL');
        } else {
            $query->whereRaw('LOWER(TRIM(house_letter)) LIKE ?', ['%' . mb_strtolower($house_letter, 'UTF-8') . '%']);
        }

        return $query->orderBy('apartment_number')->get();
    }


}