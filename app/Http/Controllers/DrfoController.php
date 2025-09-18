<?php

namespace App\Http\Controllers;

use App\Personal_data;
use App\Residence_data;
use App\Robota;
use Illuminate\Http\Request;

class DrfoController extends Controller
{

    public function index()
    {
        return view('drfo.index');

    }


    public function index_date(Request $request)
    {

        $this->validate($request, [
            'drfo'=> 'required|integer'
        ]);
     //   dd($request->all());

        $drfo=$request->get('drfo');
        $drfo_new = Personal_data::getdrfo($drfo);
        $resident = Residence_data::getresidence($drfo);
        $robota = Robota::get_robota($drfo);

    //    dd($request->all(), $drfo_new,$resident,$robota);

//        $contract_info = Contract::find($id);
//        $materials_child = Materials_children::all()->where('contract_id',$id);
//        $contract_dop = ContractDop::all()->where('contract_id',$id);


//        $deliveries = Delivery::getMaterialsContract($id);
//        $deliveries_itog = Delivery::getItogContract($id);

//        if (!$drfo_new) {
//            return redirect()->back()->withErrors(['drfo_new' => 'Людину з таким DRFO не знайдено']);
//        }

        return view('drfo.index_date',
            ['drfo_new' => $drfo_new,
               'residence' => $resident,
               'robota' => $robota,
        ]);
    }


}
