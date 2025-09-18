<?php

namespace App\Http\Controllers;

use App\Regions;
use Illuminate\Http\Request;

class RegionController extends Controller
{

    public function index()
    {
        $regions = Regions::all();
        return view('region.index', ['regions' =>$regions]);

    }


}
