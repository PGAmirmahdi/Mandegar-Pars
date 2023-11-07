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
        // invoices - not invoiced status
        $invoices1 = Invoice::whereHas('products', function ($query) {
            $query->select('products.id', 'invoice_product.invoice_net');
        })->where('status','!=','invoiced')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get();

        // invoices - invoiced status
        $invoices2 = Invoice::whereHas('other_products', function ($query) {
            $query->select('other_products.invoice_net');
        })->where('status','!=','invoiced')
            ->join('other_products', 'invoices.id', '=', 'other_products.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(other_products.invoice_net) as amount'))
            ->get();

        // merge same province invoices and sum it amounts
        $invoices = collect();
        $invoices = $invoices->merge($invoices1);

        $invoices2->each(function ($item) use ($invoices) {
            $existingInvoice = $invoices->firstWhere('province', $item->province);

            if ($existingInvoice) {
                $existingInvoice->amount += $item->amount;
            } else {
                $invoices->push($item);
            }
        });
        // end merge same province invoices and sum it amounts


        // factors - invoiced status
        $factors1 = Invoice::whereHas('products', function ($query) {
            $query->select('products.id', 'invoice_product.invoice_net');
        })->where('status','invoiced')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get(['province','amount']);

        // factors - not invoiced status
        $factors2 = Invoice::whereHas('other_products', function ($query) {
            $query->select('other_products.invoice_net');
        })->where('status','invoiced')
            ->join('other_products', 'invoices.id', '=', 'other_products.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(other_products.invoice_net) as amount'))
            ->get();

        // merge same province factors and sum it amounts
        $factors = collect();
        $factors = $factors->merge($factors1);

        $factors2->each(function ($item) use ($factors) {
            $existingInvoice = $factors->firstWhere('province', $item->province);

            if ($existingInvoice) {
                $existingInvoice->amount += $item->amount;
            } else {
                $factors->push($item);
            }
        });
        // end merge same province factors and sum it amounts

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
