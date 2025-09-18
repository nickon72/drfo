<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Reference;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reference = Reference::all();
        return view('admin.reference.index',['references' => $reference]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $company = Company::pluck('title', 'id')->all();
        return view('admin.reference.create',['company' => $company]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        {
            $this->validate($request, [
                'company_id' => 'required',
                'fio' => 'required',
                'position' => 'required'
            ]);

            Reference::create($request->all());
            return redirect()->route('reference.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $references = Reference::find($id);

        return view('admin.reference.edit', compact(
            'references'
                    ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        {
            $this->validate($request, [
                'fio' => 'required',
                'position' => 'required'
            ]);

            $reference = Reference::find($id);
            $reference->update($request->all());
            return redirect()->route('reference.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Reference::find($id)->delete();
        return redirect()->route('reference.index');
    }
}
