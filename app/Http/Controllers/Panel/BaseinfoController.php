<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Baseinfo;
use Illuminate\Http\Request;

class BaseinfoController extends Controller
{
    public function index()
    {
        $baseInfos = BaseInfo::all();
        return view('baseinfo.index', compact('baseInfos'));
    }

    public function create()
    {
        return view('baseinfo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'info' => 'required',
        ]);

        BaseInfo::create($request->all());
        return redirect()->route('baseinfo.index')->with('success', 'اطلاعات با موفقیت ذخیره شد.');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
