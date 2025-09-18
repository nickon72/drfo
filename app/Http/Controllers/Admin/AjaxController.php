<?php

namespace App\Http\Controllers\Admin;

use App\Bitum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AjaxController extends Controller
{
    public function index()
    {
        $bitum = Bitum::all();

        return view('admin.ajax.bitum',['bitums' => $bitum]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {


        return redirect()->route('filia.index');
    }
}
