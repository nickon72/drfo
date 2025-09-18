<?php

namespace App\Http\Controllers;

use App\Kirovograd;
use App\Regions;
use App\Residence_data;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResidenceExport;

class KirovogradController extends Controller
{
  // експорт ексель
    public function exportResidenceKirovograd($residence_street, $house_number = '%')
    {
        $region_id = 25;
        $residence_locality = 'КІРОВОГРАД';
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);

        $data = Kirovograd::get_kirovograd_data($residence_locality, $region_id, $residence_street, $house_number);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены в таблице kirovograd для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'kirovograd_data.xlsx');
    }

    public function exportKirovograd()
    {

        $data = Kirovograd::get_kirovograd();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены в таблице kirovograd для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'kirovograd_data.xlsx');
    }

    public function residence_kirovograd()
    {

        //$regions = Regions::all();
        return view('kirovograd.kirovograd_ind');
    }

    public function residence_kirovograd_show(Request $request)
    {
        $this->validate($request, [
            'surname' => 'required'
        ]);
        //   dd($request->all());
     //   $regions = Regions::all();
        $surname = $request->get('surname');
        $first_name = $request->get('first_name');
        $patronymic = $request->get('patronymic');
     //   $region_id = $request->input('region_id', []); // Масив region_id із чекбоксів

        //  dd($surname,$first_name,$patronymic,$request->region_id);
        $residenceData = Kirovograd::getResidenceData($surname, $first_name, $patronymic);
//dd($residenceData);
        return view('kirovograd.kirovograd_show',
            [
             //   'regions' => $regions,
                'residenceData' => $residenceData,
                //'exp' => $request->get('expiration')
            ]);

        //    dd($request->all());

    }



    //вывод улиц по конкретному городу, поселку, селу any_settlement

    public function any_settlement()
    {

        $residence = Kirovograd::get_gorod_residence();

        //   dd($residence);

        return view('kirovograd.any_settlement',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,

                //    'robota' => $robota,
            ]);
    }

// вывод всех улиц и домов по любому городу
    public function settlement_street($c)
    {
        $residence_street = $c;

        //     dd($residence_locality,$region_id,$residence_street);
        $residence_street = trim($residence_street);


        $residence = Kirovograd::get_gorod_residence_street_any($residence_street);

        //        dd($residence);

        //     dd($residence_locality,$residence_street);
        return view('kirovograd.settlement_street',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                'residence_street' => $residence_street,
                //    'robota' => $robota,
            ]);
    }




    public function kirovograd()
    {
            $residence = Kirovograd::get_gorod_residence();
        //   dd($residence);

        return view('gorod.kirovograd',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                //    'robota' => $robota,
            ]);
    }

    public function kirovograd_street_house($residence_street,$house_number,$house_letter = null)
    {

        //      dd($residence_street,$house_number,$house_letter);

        $residence = Kirovograd::get_gorod_residence_street_house($residence_street,$house_number,$house_letter);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

       //    dd($residence);

        return view('kirovograd.kirovograd_street_house',
            [
                //      'drfo_new' => $drfo_new,

                'residence' => $residence,
                'residence_street' => $residence_street,
                'house_number' =>$house_number,
            ]);
    }



}
