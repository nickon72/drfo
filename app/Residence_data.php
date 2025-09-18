<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Residence_data extends Model
{
    public function personal_data_rd()
    {
        return $this->hasOne(Personal_data::class, 'drfo_new');
    }

    public function robota_rd()
    {
        return $this->hasOne(Robota::class, 'drfo_new');
    }

    public static function getresidence($drfo)
    {
        $residence = DB::table('residence_data')
            ->select(
                'residence_locality',
                'residence_street',
                'house_number',
                'house_letter',
                'apartment_number',
                'drfo_new'
            )
            ->where('drfo_new', $drfo)
            ->get();

        return $residence;

    }

//экспорт в ексель
//    public static function get_residence_personal_data($residence_locality, $region_id, $residence_street)
//    {
//        $residence_locality = trim($residence_locality);
//        $residence_street = trim($residence_street);
//        $region_id = (int) $region_id;
//
//        return DB::table('residence_data')
//            ->select(
//                'residence_data.residence_locality',
//                'residence_data.residence_street',
//                'residence_data.house_number',
//                'residence_data.house_letter',
//                'residence_data.apartment_number',
//                'residence_data.drfo_new',
//                'personal_data.surname',
//                'personal_data.first_name',
//                'personal_data.patronymic',
//                'personal_data.birth_date'
//            )
//            ->join('personal_data', 'residence_data.drfo_new', '=', 'personal_data.drfo_new')
//            ->where('residence_data.region_id', $region_id)
//            ->whereRaw('LOWER(TRIM(residence_data.residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')])
//            ->whereRaw('LOWER(TRIM(residence_data.residence_street)) LIKE ?', ['%' . mb_strtolower($residence_street, 'UTF-8') . '%'])
//            ->orderBy('residence_data.house_number')
//            ->get();
//        // Возвращаем пустую коллекцию, если данных нет
//        return $data->isEmpty() ? collect([]) : $data;
//
//    }

//экспорт в ексель по улицам
    public static function get_residence_personal_data($residence_locality, $region_id, $residence_street)
    {
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $region_id = (int) $region_id;

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
                'personal_data.birth_date'
            )
            ->join('personal_data', 'residence_data.drfo_new', '=', 'personal_data.drfo_new')
            ->where('residence_data.region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_data.residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')]);

        // Если residence_street не '%', добавляем фильтр
        if ($residence_street !== '%') {
            $query->whereRaw('LOWER(TRIM(residence_data.residence_street)) LIKE ?', ['%' . mb_strtolower($residence_street, 'UTF-8') . '%']);
        }

        return $query->orderBy('residence_data.apartment_number')->get();
    }

    //экспорт в ексель по домам улицы
    public static function get_residence_personal_data_house($residence_locality, $region_id, $residence_street, $house_number)
    {
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);
        $region_id = (int) $region_id;

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
            ->where('residence_data.region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_data.residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')]);

        if ($residence_street !== '%') {
            $query->whereRaw('LOWER(TRIM(residence_data.residence_street)) LIKE ?', ['%' . mb_strtolower($residence_street, 'UTF-8') . '%']);
        }

        if ($house_number !== '%') {
            $query->whereRaw('LOWER(TRIM(residence_data.house_number)) = ?', [mb_strtolower($house_number, 'UTF-8')]);
        }

        return $query->orderBy('residence_data.apartment_number')->get();

    }

    //экспорт в ексель по городу
    public static function get_residence_personal_data_gorod($residence_locality, $region_id)
    {
        $residence_locality = trim($residence_locality);
        $region_id = (int) $region_id;

        return DB::table('residence_data')
            ->select(
                'residence_data.residence_locality',
                'residence_data.residence_street',
                'residence_data.house_number',
                'residence_data.house_letter',
                'residence_data.apartment_number',
                'residence_data.drfo_new',
                'personal_data.surname',
                'personal_data.first_name',
                'personal_data.patronymic',
                'personal_data.birth_date'
            )
            ->join('personal_data', 'residence_data.drfo_new', '=', 'personal_data.drfo_new')
            ->where('residence_data.region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_data.residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')])
            ->orderBy('residence_data.residence_street')
            ->get();
        // Возвращаем пустую коллекцию, если данных нет
        return $data->isEmpty() ? collect([]) : $data;

    }
//    public static function getResidenceData($surname, $first_name, $patronymic)
//    {
//        //dd($surname,$first_name, $patronymic);
//        $residence = DB::table('personal_data as pd')
//            ->select(
//                'pd.surname',
//                'pd.first_name',
//                'pd.patronymic',
//                'pd.birth_date',
//                'rd.residence_locality',
//                'rd.drfo_new',
//                'rd.region_id',
//                'r.region_name'
//            )
//            ->join('residence_data as rd', 'pd.drfo_new', '=', 'rd.drfo_new')
//            ->join('regions as r', 'rd.region_id', '=', 'r.id')
//            ->whereIn('rd.region_id',[1, 2, 25])
//            ->where('pd.surname', 'like', "%" . $surname . "%")
//            ->where('pd.first_name', 'like', "%" . $first_name . "%")
////            ->where(function ($query) use ($patronymic) {
////                $query->where('pd.patronymic', 'like', "%" . $patronymic . "%")
////                    ->orWhereNull('pd.patronymic')
////                    ->orWhere('pd.patronymic', '');
////            })
//            ->get();
//
//        return $residence;
//    }

    public static function getResidenceData($surname, $first_name = null, $patronymic = null, $region_id = [])
    {
        // Перевірка, чи $region_id є масивом і не порожній
        if (!is_array($region_id) || empty($region_id)) {
            // Якщо масив порожній, можна або повернути помилку, або вибрати всі регіони
            $region_id = range(1, 28); // За замовчуванням вибираємо всі регіони від 1 до 28
        }

        $residence = DB::table('personal_data as pd')
            ->select(
                'pd.surname',
                'pd.first_name',
                'pd.patronymic',
                'pd.birth_date',
                'rd.residence_locality',
                'rd.drfo_new',
                'rd.region_id',
                'r.region_name'
            )
            ->join('residence_data as rd', 'pd.drfo_new', '=', 'rd.drfo_new')
            ->join('regions as r', 'rd.region_id', '=', 'r.id')
            ->whereIn('rd.region_id', $region_id)
            ->where('pd.surname', 'like', '%' . $surname . '%')
            ->when($first_name, function ($query) use ($first_name) {
                return $query->where('pd.first_name', 'like', '%' . $first_name . '%');
            })
            ->when($patronymic, function ($query) use ($patronymic) {
                return $query->where(function ($q) use ($patronymic) {
                    $q->where('pd.patronymic', 'like', '%' . $patronymic . '%')
                        ->orWhereNull('pd.patronymic')
                        ->orWhere('pd.patronymic', '');
                });
            })
            ->get();

        return $residence;
    }

//Выбирает уникальные улицы из запроса по конкретному городу и региону
    public static function get_gorod_residence($residence_locality, $region_id)
    {
        $kirovograd = DB::table('residence_data')
            ->select(
                DB::raw('MAX(residence_locality) as residence_locality'),
                'residence_street',
                DB::raw('MAX(region_id) as region_id')
            )
            ->where('region_id', $region_id)
            ->where('residence_locality', $residence_locality)
            ->groupBy('residence_street')
            ->orderBy('residence_street')
            ->get();

        return $kirovograd;
    }
//              public  static function get_gorod_residence_street($residence_locality,$region_id,$residence_street)
//      {
//          $kirovograd = DB::table('residence_data')
//              ->select(
//                  'residence_locality',
//                  'residence_street',
//                  'house_number',
//                  'house_letter',
//                  'apartment_number',
//                  'drfo_new',
//                  'region_id'
//              )
//              ->where('region_id', $region_id)
//              ->where('residence_locality', $residence_locality)
//              ->where('residence_street', $residence_street)
//              ->orderby('residence_street')
//              ->get();
//
//          return $kirovograd;
//
//
//     }

     // поиск по Кировограду улиц номер дома буква дома
    public static function get_gorod_residence_street($residence_locality, $region_id, $residence_street)
    {

        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $region_id = (int) $region_id;

        $kirovograd = DB::table('residence_data')
            ->select(
                DB::raw('MAX(residence_locality) as residence_locality'),
                DB::raw('MAX(residence_street) as residence_street'),
                'house_number',
                'house_letter',
                'region_id' // Додано region_id
            )
            ->where('region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')])
            ->whereRaw('LOWER(TRIM(residence_street)) = ?', [mb_strtolower($residence_street, 'UTF-8')])
            ->groupBy('house_number', 'house_letter', 'region_id') // Групування лише за house_number і house_letter
            ->orderBy('residence_street')
            ->get();

        return $kirovograd;
    }

    public static function get_gorod_residence_street_any($residence_locality, $region_id, $residence_street)
    {

        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $region_id = (int) $region_id;

        $kirovograd = DB::table('residence_data')
            ->select(
                DB::raw('MAX(residence_locality) as residence_locality'),
                DB::raw('MAX(residence_street) as residence_street'),
                'house_number',
                'house_letter',
                'region_id' // Додано region_id
            )
            ->where('region_id', $region_id)
            ->whereRaw('LOWER(TRIM(residence_locality)) = ?', [mb_strtolower($residence_locality, 'UTF-8')])
            ->whereRaw('LOWER(TRIM(residence_street)) = ?', [mb_strtolower($residence_street, 'UTF-8')])
            ->groupBy('house_number', 'house_letter', 'region_id') // Групування лише за house_number і house_letter
            ->orderBy('residence_street')
            ->get();

        return $kirovograd;
    }

//    public  static function get_gorod_residence_street_house($residence_locality,$region_id,$residence_street,$house_number,$house_letter)
//    {
//        $kirovograd = DB::table('residence_data')
//            ->select(
//                'residence_locality',
//                'residence_street',
//                'house_number',
//                'house_letter',
//                'apartment_number',
//                'drfo_new',
//                'region_id'
//            )
//            ->where('region_id', $region_id)
//            ->where('residence_locality', $residence_locality)
//            ->where('residence_street', $residence_street)
//            ->where('house_number', $house_number)
//            ->where('house_letter', $house_letter)
////            ->groupBy('house_number')
//            ->orderby('apartment_number')
//            ->get();
//
//        return $kirovograd;
//
//
//    }

//
//   исправленная функция поиска по дому и букве дома, если буквы нет.
    public static function get_gorod_residence_street_house($residence_locality, $region_id, $residence_street, $house_number, $house_letter)
    {
        // Подготовка входных параметров
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);
        $house_letter = $house_letter !== null ? trim($house_letter) : null;
        $region_id = (int) $region_id;

        // Если house_letter пустая строка, считать её null
        if ($house_letter === '') {
            $house_letter = null;
        }

        $query = DB::table('residence_data')
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
            ->whereRaw('LOWER(TRIM(house_number)) LIKE ?', ['%' . mb_strtolower($house_number, 'UTF-8') . '%']);

        // Обработка house_letter
        if ($house_letter === null) {
            $query->whereRaw('house_letter IS NULL');
        } else {
            $query->whereRaw('LOWER(TRIM(house_letter)) LIKE ?', ['%' . mb_strtolower($house_letter, 'UTF-8') . '%']);
        }

        return $query->orderBy('apartment_number')->get();
    }

// выбирает уникальные названия городов и посёлков в регионе
    public static function get_gorod_any($region_id)
    {
        $localities = DB::table('residence_data')
            ->select(
                'residence_locality',
                DB::raw('MAX(region_id) as region_id')
            )
            ->where('region_id', $region_id)
            ->groupBy('residence_locality')
            ->orderBy('residence_locality')
            ->get();

        return $localities;
    }


}
