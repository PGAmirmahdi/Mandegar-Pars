<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSoftwareUpdateRequest;
use App\Models\Activity;
use App\Models\SoftwareUpdate;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoftwareUpdateController extends Controller
{
    public function index()
    {
        $this->authorize('software-updates-list');

        $software_updates = SoftwareUpdate::latest()->paginate(30);
        return view('panel.software-updates.index', compact('software_updates'));
    }

    public function create()
    {
        $this->authorize('software-updates-create');

        return view('panel.software-updates.create');
    }

    public function store(StoreSoftwareUpdateRequest $request)
    {
        $this->authorize('software-updates-create');

        $softwareUpdate=SoftwareUpdate::create([
            'version' => $request->version_number,
            'date' => Verta::parse($request->release_date)->toCarbon(),
            'description' => $request->description,
        ]);
        // ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ثبت تغییرات نرم‌افزاری',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') تغییرات نسخه ' . $softwareUpdate->version . ' را ثبت کرده است.',
            'created_at' => now(),
        ]);
        alert()->success('تغییرات نرم افزار با موفقیت افزوده شد','ثبت تغییرات');
        return redirect()->route('software-updates.index');
    }

    public function show(SoftwareUpdate $softwareUpdate)
    {
        //
    }

    public function edit(SoftwareUpdate $softwareUpdate)
    {
        $this->authorize('software-updates-edit');

        return view('panel.software-updates.edit', compact('softwareUpdate'));
    }

    public function update(Request $request, SoftwareUpdate $softwareUpdate)
    {
        $this->authorize('software-updates-edit');

        $softwareUpdate->update([
            'version' => $request->version_number,
            'date' => Verta::parse($request->release_date)->toCarbon(),
            'description' => $request->description,
        ]);
        // ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش تغییرات نرم‌افزاری',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') تغییرات نسخه ' . $softwareUpdate->version . ' را ویرایش کرده است.',
            'created_at' => now(),
        ]);
        alert()->success('تغییرات نرم افزار با موفقیت ویرایش شد','ویرایش تغییرات');
        return redirect()->route('software-updates.index');
    }

    public function destroy(SoftwareUpdate $softwareUpdate)
    {
        $this->authorize('software-updates-delete');
// ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف تغییرات نرم‌افزاری',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') تغییرات نسخه ' . $softwareUpdate->version . ' را حذف کرده است.',
            'created_at' => now(),
        ]);
        $softwareUpdate->delete();
        return back();
    }

    public function versions()
    {
        $versions = SoftwareUpdate::latest()->paginate(30);
        return view('panel.software-updates.versions', compact('versions'));
    }
}
