<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Transporter;
use Illuminate\Http\Request;

class TransporterController extends Controller
{
    public function index()
    {
        $transporters = Transporter::all();
        return view('panel.transporters.index', compact('transporters'));
    }

    public function create()
    {
        return view('panel.transporters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        Transporter::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone'=> $request->phone,
            'code' => 'TR-' . random_int(1000000000, 9999999999),
        ]);
        alert()->success('حمل و نقل کننده با موفقیت اضافه شد.','موفق');
        return redirect()->route('transporters.index');
    }
    public function edit()
    {
        return view('panel.transporters.edit');
    }
    public function destroy(Transporter $transporter)
    {
        $transporter->delete();
        alert()->success('موفق', 'حمل و نقل کننده با موفقیت حذف شد.');
        return redirect()->route('transporters.index');
    }
}
