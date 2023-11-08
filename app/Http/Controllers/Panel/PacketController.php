<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacketRequest;
use App\Http\Requests\UpdatePacketRequest;
use App\Models\Invoice;
use App\Models\Packet;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class PacketController extends Controller
{
    public function index()
    {
        $this->authorize('packets-list');

        if (auth()->user()->isAdmin()){
            $packets = Packet::latest()->paginate(30);
            $invoices = Invoice::with('customer')->latest()->get(['id','customer_id']);
        }else{
            $packets = Packet::where('user_id', auth()->id())->latest()->paginate(30);
            $invoices = Invoice::with('customer')->where('user_id', auth()->id())->latest()->get(['id','customer_id']);
        }

        return view('panel.packets.index', compact('packets', 'invoices'));
    }

    public function create()
    {
        $this->authorize('packets-create');

        $invoices = Invoice::with('customer')->doesntHave('packet')->latest()->get()->pluck('customer.name','id');
        return view('panel.packets.create', compact('invoices'));
    }

    public function store(StorePacketRequest $request)
    {
        $this->authorize('packets-create');

        $sent_time = Verta::parse($request->sent_time)->datetime();

        Packet::create([
            'user_id' => auth()->id(),
            'invoice_id' => $request->invoice,
            'receiver' => $request->receiver,
            'address' => $request->address,
            'sent_type' => $request->sent_type,
            'send_tracking_code' => $request->send_tracking_code,
            'receive_tracking_code' => $request->receive_tracking_code,
            'packet_status' => $request->packet_status,
            'invoice_status' => $request->invoice_status,
            'description' => $request->description,
            'sent_time' => $sent_time,
            'notif_time' => Carbon::parse($sent_time)->addDays(20),
        ]);

        alert()->success('بسته مورد نظر با موفقیت ایجاد شد','ایجاد بسته');
        return redirect()->route('packets.index');
    }

    public function show(Packet $packet)
    {
        //
    }

    public function edit(Packet $packet)
    {
        // access to packets-edit permission
        $this->authorize('packets-edit');

        // edit own packet OR is admin
        $this->authorize('edit-packet', $packet);

        $invoices = Invoice::with('customer')->doesntHave('packet')->latest()->get()->pluck('customer.name','id');

        return view('panel.packets.edit', compact('invoices', 'packet'));
    }

    public function update(UpdatePacketRequest $request, Packet $packet)
    {
        // access to packets-edit permission
        $this->authorize('packets-edit');

        // edit own packet OR is admin
        $this->authorize('edit-packet', $packet);

        $sent_time = Verta::parse($request->sent_time)->datetime();

        $packet->update([
            'invoice_id' => $request->invoice,
            'receiver' => $request->receiver,
            'address' => $request->address,
            'sent_type' => $request->sent_type,
            'send_tracking_code' => $request->send_tracking_code,
            'receive_tracking_code' => $request->receive_tracking_code,
            'packet_status' => $request->packet_status,
            'invoice_status' => $request->invoice_status,
            'description' => $request->description,
            'sent_time' => $sent_time,
            'notif_time' => Carbon::parse($sent_time)->addDays(20),
        ]);

        alert()->success('بسته مورد نظر با موفقیت ویرایش شد','ویرایش بسته');
        return redirect()->route('packets.index');
    }

    public function destroy(Packet $packet)
    {
        $this->authorize('packets-delete');

        $packet->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('packets-list');

        if (auth()->user()->isAdmin()){
            $invoices = Invoice::with('customer')->latest()->get(['id','customer_id']);
            $invoice_id = $request->invoice_id == 'all' ? $invoices->pluck('id') : [$request->invoice_id];
            $packet_status = $request->packet_status == 'all' ? array_keys(Packet::PACKET_STATUS) : [$request->packet_status];
            $invoice_status = $request->invoice_status == 'all' ? array_keys(Packet::INVOICE_STATUS) : [$request->invoice_status];

            $packets = Packet::whereIn('invoice_id', $invoice_id)
                ->whereIn('packet_status', $packet_status)
                ->whereIn('invoice_status', $invoice_status)
                ->latest()->paginate(30);
        }else{
            $invoices = Invoice::with('customer')->where('user_id', auth()->id())->latest()->get(['id','customer_id']);
            $invoice_id = $request->invoice_id == 'all' ? $invoices->pluck('id') : [$request->invoice_id];
            $packet_status = $request->packet_status == 'all' ? array_keys(Packet::PACKET_STATUS) : [$request->packet_status];
            $invoice_status = $request->invoice_status == 'all' ? array_keys(Packet::INVOICE_STATUS) : [$request->invoice_status];

            $packets = Packet::where('user_id', auth()->id())
                ->whereIn('invoice_id', $invoice_id)
                ->whereIn('packet_status', $packet_status)
                ->whereIn('invoice_status', $invoice_status)->latest()->paginate(30);
        }

        return view('panel.packets.index', compact('packets', 'invoices'));
    }
}
