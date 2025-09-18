<?php

namespace App\Http\Controllers;

use App\Exports\ResidenceExport;
use App\Kirovograd;
use App\Svalyava;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SvalyavaController extends Controller
{

    // експорт ексель
    public function exportResidenceSvalyava($residence_street, $house_number = '%')
    {
        $region_id = 26;
        $residence_locality = 'М.СВАЛЯВА';
        $residence_street = trim($residence_street);
        $house_number = trim($house_number);

        $data = Svalyava::get_svalyava_data($residence_locality, $region_id, $residence_street, $house_number);

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены в таблице Svalyava для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'svalyava_data.xlsx');
    }

    public function exportSvalyava()
    {

        $data = Svalyava::get_svalyava();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Данные не найдены в таблице Svalyava для указанных параметров.');
        }

        return Excel::download(new ResidenceExport($data), 'svalyava_data.xlsx');
    }



    public function residence_svalyava()
    {

        //$regions = Regions::all();
        return view('svalyava.svalyava_ind');
    }

    public function residence_svalyava_show(Request $request)
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
        $residenceData = Svalyava::getResidenceData($surname, $first_name, $patronymic);
//dd($residenceData);
        return view('svalyava.svalyava_show',
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

        $residence = Svalyava::get_gorod_residence();

        //   dd($residence);

        return view('svalyava.any_settlement',
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


        $residence = Svalyava::get_gorod_residence_street_any($residence_street);

        //        dd($residence);

        //     dd($residence_locality,$residence_street);
        return view('svalyava.settlement_street',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                'residence_street' => $residence_street,
                //    'robota' => $robota,
            ]);
    }


    public function kirovograd()
    {
        $residence = Svalyava::get_gorod_residence();
        //   dd($residence);

        return view('svalyava.svalyava',
            [
                //      'drfo_new' => $drfo_new,
                'residence' => $residence,
                //    'robota' => $robota,
            ]);
    }


    public function svalyava_street_house($residence_street,$house_number,$house_letter = null)
    {

        //      dd($residence_street,$house_number,$house_letter);

        $residence = Svalyava::get_gorod_residence_street_house($residence_street,$house_number,$house_letter);
//        $drfo_new = Personal_data::getdrfo($drfo);
//        $resident = Residence_data::getresidence($drfo);
//        $robota = Robota::get_robota($drfo);

     //      dd($residence);

        return view('svalyava.svalyava_street_house',
            [
                //      'drfo_new' => $drfo_new,

                'residence' => $residence,
                'residence_street' => $residence_street,
                'house_number' =>$house_number,
                'house_letter' =>$house_letter,
            ]);
    }




}
