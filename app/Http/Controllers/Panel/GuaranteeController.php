<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuaranteeRequest;
use App\Http\Requests\UpdateGuaranteeRequest;
use App\Models\Guarantee;
use Illuminate\Http\Request;

class GuaranteeController extends Controller
{
    public function index()
    {
        $this->authorize('guarantees-list');

        // expire the guarantees where expired_at < now
        Guarantee::where('status', 'active')->where('expired_at', '<', now())->update(['status' => 'expired']);

        $guarantees = Guarantee::latest()->paginate(30);
        return view('panel.guarantees.index', compact('guarantees'));
    }

    public function create()
    {
        $this->authorize('guarantees-create');

        $serial = 'MP'.random_int(10000000, 99999999);
        return view('panel.guarantees.create', compact('serial'));
    }

    public function store(StoreGuaranteeRequest $request)
    {
        $this->authorize('guarantees-create');

        Guarantee::create([
            'serial' => $request->serial_number,
            'period' => $request->period,
            'status' => $request->status,
            'activated_at' => $request->status == 'active' ? now() : null,
            'expired_at' => $request->status == 'active' ? now()->addMonths($request->period) : null,
        ]);

        alert()->success('گارانتی جدید با موفقیت ایجاد شد','ایجاد گارانتی');
        return redirect()->route('guarantees.index');
    }

    public function show(Guarantee $guarantee)
    {
        //
    }

    public function edit(Guarantee $guarantee)
    {
        $this->authorize('guarantees-edit');

        return view('panel.guarantees.edit', compact('guarantee'));
    }

    public function update(UpdateGuaranteeRequest $request, Guarantee $guarantee)
    {
        $this->authorize('guarantees-edit');

        $guarantee->update([
            'serial' => $request->serial_number,
            'period' => $request->period,
            'status' => $request->status,
            'activated_at' => $guarantee->activated_at == null ? ($request->status == 'active' ? now() : null) : $guarantee->activated_at,
            'expired_at' => $guarantee->expired_at == null ? ($request->status == 'active' ? now()->addMonths($request->period) : null) : $guarantee->expired_at,
        ]);

        alert()->success('گارانتی با موفقیت ویرایش شد','ویرایش گارانتی');
        return redirect()->route('guarantees.index');
    }

    public function destroy(Guarantee $guarantee)
    {
        $this->authorize('guarantees-delete');

        $guarantee->delete();
        return back();
    }

    public function serialCheck(Request $request)
    {
        $serial = 'MP'.$request->serial;

        $guarantee = Guarantee::where('serial', $serial)->whereIn('status', ['active', 'inactive'])->first();

        if ($guarantee){
            if (!$guarantee->inventory_report){
                $error = false;
                $message = 'سریال گارانتی معتبر است';
            }elseif($guarantee->inventory_report->id == $request->inventory_report_id){
                $error = false;
                $message = 'سریال گارانتی معتبر است';
            }else{
                $error = true;
                $message = 'سریال گارانتی معتبر نیست';
            }
        }else{
            $error = true;
            $message = 'سریال گارانتی معتبر نیست';
        }

        $data = [
            'error' => $error,
            'message' => $message,
        ];

        return response()->json(['data' => $data]);
    }
}
