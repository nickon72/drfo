<?php

namespace App\Http\Controllers;

use App\Personal_data;
use App\Regions;
use App\Residence_data;
use App\Kirovograd;
use App\Robota;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResidenceExport;

class ResidenceController extends Controller
{
    public function residence_filia()
    {

        $regions = Regions::all();
        return view('residence.filia', ['regions' =>$regions]);
    }
// експорт в ексель по улицам города
    public function exportResidenceData($region_id, $residence_locality, $residence_street)
    {
        $region_id = (int) $region_id;
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);

        $data = Residence_data::get_residence_personal_data($residence_locality, $region_id, $residence_street);

        // Проверка на пустые данные
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'residence_data.xlsx');
    }

    // експорт в ексель по поду определённой улицы города
    public function exportResidenceDataHouse($region_id, $residence_locality, $residence_street, $house_number)
    {
        $region_id = (int) $region_id;
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);

        $data = Residence_data::get_residence_personal_data_house($residence_locality, $region_id, $residence_street, $house_number);

      //  dd($data);
        // Проверка на пустые данные
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'residence_data.xlsx');
    }

    // експорт в ексель по выбранному городу
    public function exportResidenceDataGorod($region_id, $residence_locality)
    {
        $region_id = (int) $region_id;
        $residence_locality = trim($residence_locality);


        $data = Residence_data::get_residence_personal_data_gorod($residence_locality, $region_id);

        // Проверка на пустые данные
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'residence_data.xlsx');
    }


// Новый метод для экспорта из таблицы kirovograd
    public function exportResidenceKirovograd($region_id, $residence_locality, $residence_street)
    {
        $region_id = (int) $region_id;
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);

        $data = Kirovograd::get_kirovograd_data($residence_locality, $region_id, $residence_street);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены в таблице kirovograd для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'kirovograd_data.xlsx');
    }


    // Новый метод для экспорта из таблицы Свалява
    public function exportResidenceSvalyava($region_id, $residence_locality, $residence_street)
    {
        $region_id = (int) $region_id;
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);

        $data = \App\Svalyava::get_svalyava_data($residence_locality, $region_id, $residence_street);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены в таблице svalyava для указанных параметров.');
        }

        return Excel::download(new \App\Exports\ResidenceExport($data), 'svalyava_data.xlsx');
    }

       public function residence_filia_show(Request $request)
    {
        $this->validate($request, [
            'surname' => 'required'
        ]);
        //   dd($request->all());
        $regions = Regions::all();
        $surname = $request->get('surname');
        $first_name = $request->get('first_name');
        $patronymic = $request->get('patronymic');
        $region_id = $request->input('region_id', []); // Масив region_id із чекбоксів

      //  dd($surname,$first_name,$patronymic,$request->region_id);
        $residenceData = Residence_data::getResidenceData($surname, $first_name, $patronymic,$region_id);
//dd($residenceData);
        return view('residence.filia_show',
            [
                'regions' => $regions,
                'residenceData' => $residenceData,
                //'exp' => $request->get('expiration')
         ]);

       //    dd($request->all());

    }


    //информация по конкретному человеку
    public function show($drfo)
    {
        //$drfo=$drfo;

        $drfo_new = Personal_data::getdrfo($drfo);
        $resident = Residence_data::getresidence($drfo);
        $robota = Robota::get_robota($drfo);


        return view('residence.people_show',
          [
                'drfo_new' => $drfo_new,
                    'residence' => $resident,
                    'robota' => $robota,
            ]);
    }

    public function gorod($residence_locality,$region_id)
    {

          $resident = Residence_data::get_gorod_residence($residence_locality,$region_id);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

     dd(Sresident);
        return view('gorod.kirovograd',
            [
          //      'drfo_new' => $drfo_new,
                'residence' => $resident,
            //    'robota' => $robota,
            ]);
    }


    public function kirovograd()
    {
        $residence_locality="КІРОВОГРАД";
        $region_id = 25;

        $residence = Residence_data::get_gorod_residence($residence_locality,$region_id);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

     //   dd($residence);

        return view('gorod.kirovograd',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                //    'robota' => $robota,
            ]);
    }

    //вывод улиц по конкретному городу, поселку, селу any_settlement

    public function any_settlement($region_id,$residence_locality)
    {

        $residence = Residence_data::get_gorod_residence($residence_locality,$region_id);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

        //   dd($residence);

        return view('gorod.any_settlement',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                'region_id' => $region_id,
                'residence_locality' => $residence_locality,

                //    'robota' => $robota,
            ]);
    }

    public function kirovograd_street($residence_street)
    {
        $residence_locality="КІРОВОГРАД";
        $region_id = 25;

   //     dd($residence_locality,$region_id,$residence_street);

        $residence = Residence_data::get_gorod_residence_street($residence_locality,$region_id,$residence_street);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

       //    dd($residence);

        return view('gorod.kirovograd_street',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                //    'robota' => $robota,
            ]);
    }

    // вывод всех улиц и домов по любому городу
    public function settlement_street($a,$b,$c)
    {
     //   dd($a,$b,$c);
        $region_id = $a;
        $residence_locality = $b;
        $residence_street = $c;

     //     dd($residence_locality,$region_id,$residence_street);
        $region_id = (int) $region_id;
        $residence_locality = trim($residence_locality);
        $residence_street = trim($residence_street);


        $residence = Residence_data::get_gorod_residence_street_any($residence_locality,$region_id,$residence_street);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

   //        dd($residence);

   //     dd($residence_locality,$residence_street);
        return view('gorod.settlement_street',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                'region_id' => $region_id,
                'residence_locality' => $residence_locality,
                'residence_street' => $residence_street,
                //    'robota' => $robota,
            ]);
    }






    public function kirovograd_street_house($residence_street,$house_number,$house_letter = null)
    {
        $residence_locality="КІРОВОГРАД";
        $region_id = 25;

       //    dd($residence_locality,$region_id,$residence_street,$house_number);

        $residence = Residence_data::get_gorod_residence_street_house($residence_locality,$region_id,$residence_street,$house_number,$house_letter);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

        //   dd($residence);

        return view('gorod.kirovograd_street_house',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                //    'robota' => $robota,
            ]);
    }


    // вывод домов по улице города любого
    public function settlement_street_house($region_id,$residence_locality,$residence_street,$house_number,$house_letter = null)
    {

        //      dd($residence_locality,$region_id,$residence_street,$house_number,$house_letter);

        $residence = Residence_data::get_gorod_residence_street_house($residence_locality,$region_id,$residence_street,$house_number,$house_letter);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

        //   dd($residence);

        return view('gorod.kirovograd_street_house',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                'region_id' => $region_id,
                'residence_locality' => $residence_locality,
                'residence_street' => $residence_street,
                'house_number' =>$house_number,
            ]);
    }



    //блок функций по работе с отдельным городом-улицей-домом

    // вывод списка регионов для выбора города
    public function any()
    {
        $regions = Regions::all();
        return view('gorod.any', ['regions' =>$regions]);

    }

    public function any_region($region_id)
    {
        $reg = Regions::find($region_id);
        $regions = $reg->region_name;
        $resident = Residence_data::get_gorod_any($region_id);

     //   dd($regions);
        return view('gorod.any_region',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $resident,
                'regions' => $regions,
            ]);
    }



}
