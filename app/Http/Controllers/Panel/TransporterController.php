<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class   TransporterController extends Controller
{
    public function index()
    {
        $this->authorize('transporters-list');
        $transporters = Transporter::paginate(30);
        return view('panel.transporters.index', compact('transporters'));
    }

    public function create()
    {
        $this->authorize('transporters-create');
        return view('panel.transporters.create');
    }

    public function store(Request $request)
    {
        $this->authorize('transporters-create');
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        $transporter=Transporter::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone'=> $request->phone,
            'code' => 'TR-' . random_int(1000000000, 9999999999),
        ]);
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ثبت حمل و نقل کننده',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک حمل و نقل کننده به نام ' . $transporter->name . ' اضافه کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('حمل و نقل کننده با موفقیت اضافه شد.','موفق');
        return redirect()->route('transporters.index');
    }
    public function edit($id)
    {
        $this->authorize('transporters-edit');
        $transporter=Transporter::findOrFail($id);
        return view('panel.transporters.edit',compact('transporter'));
    }
    public function update(Request $request,Transporter $transporter )
    {
        $this->authorize('transporters-edit');
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20', // اعتبارسنجی اختیاری برای شماره تلفن
        ]);
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش حمل و نقل کننده',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک حمل و نقل کننده به نام ' . $transporter->name . ' را ویرایش کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        // بروزرسانی اطلاعات
        $transporter->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);
        // نمایش پیام موفقیت
        alert()->success('اطلاعات حمل و نقل کننده با موفقیت بروزرسانی شد.', 'موفق');

        // بازگشت به صفحه لیست
        return redirect()->route('transporters.index');
    }

    public function destroy(Transporter $transporter)
    {
        $this->authorize('transporters-delete');
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف حمل و نقل کننده',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک حمل و نقل کننده به نام ' . $transporter->name . ' را حذف کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        $transporter->delete();
        alert()->success('موفق', 'حمل و نقل کننده با موفقیت حذف شد.');
        return redirect()->route('transporters.index');
    }
}
