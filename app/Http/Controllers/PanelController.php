<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function index()
    {
        $invoices = Invoice::whereHas('products', function ($query) {
            $query->select('products.id', 'invoice_product.invoice_net');
        })->where('status','!=','invoiced')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get(['province','amount']);

        $factors = Invoice::whereHas('products', function ($query) {
            $query->select('products.id', 'invoice_product.invoice_net');
        })->where('status','invoiced')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get(['province','amount']);

        return view('panel.index', compact('invoices','factors'));
    }

    public function readNotification($notification = null)
    {
        if ($notification == null){
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        }

        $notif = auth()->user()->unreadNotifications()->whereId($notification)->first();
        $notif->markAsRead();
        return redirect()->to($notif->data['url']);
    }

    public function login(Request $request)
    {
        if ($request->method() == 'GET'){
            $adminRoleId = Role::where('name', 'admin')->first()->id;
            $users = User::where('id', '!=', auth()->id())->where('role_id','!=',$adminRoleId)->get(['id','name','family']);

            return view('panel.login', compact('users'));
        }

        Auth::loginUsingId($request->user);
        return redirect()->route('panel');
    }
}
