<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Baseinfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseinfoController extends Controller
{
    public function index()
    {
        $baseInfos = BaseInfo::all();
        return view('panel.information.index', compact('baseInfos'));
    }

    public function create()
    {
        $this->authorize('information');
        return view('panel.information.create');
    }

    public function store(Request $request, Baseinfo $file)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'info' => 'required',
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {

            $filePath = upload_file($request->file('file'), 'InfoFiles');
            $data['file'] = $filePath;
        }
        BaseInfo::create($data);
        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'افزودن اطلاعات',
            'description' => 'کاربر ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . 'اطلاعاتی به جدول اطلاعات اضافه کرد',
        ]);
        alert()->success('اطلاعات با موفقیت بارگذاری شد','موفق');
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

    public function edit($id)
    {
        $this->authorize('information');
        $baseinfo = Baseinfo::findOrFail($id);
        return view('panel.information.edit', compact('baseinfo'));
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'info' => 'required|string',
            'access' => 'required|string',
        ]);
        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش اطلاعات',
            'description' => 'کاربر ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . 'اطلاعاتی را در جدول اطلاعات ویرایش کرد',
        ]);
        $baseinfo = Baseinfo::findOrFail($id);
        $baseinfo->update($validated);
        alert()->success('اطلاعات با موفقیت به روز رسانی شد','موفق');
        return redirect()->route('baseinfo.index')->with('success', 'اطلاعات با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Baseinfo $baseinfo)
    {
        $this->authorize('information');
// ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف اطلاعات',
            'description' => 'کاربر ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . 'اطلاعاتی را از جدول اطلاعات حذف کرد',
        ]);
        $baseinfo->delete();
        return back();
    }
}
