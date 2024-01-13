<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSoftwareUpdateRequest;
use App\Models\SoftwareUpdate;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

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

        SoftwareUpdate::create([
            'version' => $request->version_number,
            'date' => Verta::parse($request->release_date)->toCarbon(),
            'description' => $request->description,
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

        alert()->success('تغییرات نرم افزار با موفقیت ویرایش شد','ویرایش تغییرات');
        return redirect()->route('software-updates.index');
    }

    public function destroy(SoftwareUpdate $softwareUpdate)
    {
        $this->authorize('software-updates-delete');

        $softwareUpdate->delete();
        return back();
    }

    public function versions()
    {
        $versions = SoftwareUpdate::latest()->paginate(30);
        return view('panel.software-updates.versions', compact('versions'));
    }
}
