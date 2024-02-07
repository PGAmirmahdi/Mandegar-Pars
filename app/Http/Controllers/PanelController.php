<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request->from_date ? Verta::parse($request->from_date)->toCarbon()->toDateTimeString() : Invoice::orderBy('created_at')->first()->created_at;
        $to_date = $request->to_date ? Verta::parse($request->to_date)->endDay()->toCarbon()->toDateTimeString() : Invoice::orderBy('created_at','desc')->first()->created_at;

        // invoices
        $invoices1 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('products', function ($query) {
            $query->select('products.id', 'invoice_product.invoice_net');
        })->where('status','pending')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get();

        // invoices
        $invoices2 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('other_products', function ($query) {
            $query->select('other_products.invoice_net');
        })->where('status','pending')
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

        // final discount
        $invoices_discounts = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->where('status','pending')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoices.discount) as discount'))
            ->get();

        foreach ($invoices as $key => $invoice)
        {
            $invoices[$key]->amount -= $invoices_discounts->where('province', $invoice->province)->first()->discount;
        }
        // end merge same province invoices and sum it amounts

        // factors
        $factors1 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('products', function ($query) {
            $query->select('products.id', 'invoice_product.invoice_net');
        })->where('status','invoiced')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get(['province','amount']);

        // factors
        $factors2 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('other_products', function ($query) {
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

        // final discount
        $factors_discounts = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->where('status','invoiced')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoices.discount) as discount'))
            ->get();

        foreach ($factors as $key => $factor)
        {
            $factors[$key]->amount -= $factors_discounts->where('province', $factor->province)->first()->discount;
        }
        // end merge same province factors and sum it amounts

        $factors_monthly = $this->getFactorsMonthly();

        return view('panel.index', compact('invoices','factors','factors_monthly'));
    }

    public function readNotification($notification = null)
    {
        if ($notification == null){
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        }

        $notif = auth()->user()->unreadNotifications()->whereId($notification)->first();
        if (!$notif){
            return back();
        }

        $notif->markAsRead();
        return redirect()->to($notif->data['url']);
    }

    public function login(Request $request)
    {
        if ($request->method() == 'GET'){
            $users = User::where('id', '!=', auth()->id())->whereIn('id', [3, 4, 152])->get(['id','name','family']);

            return view('panel.login', compact('users'));
        }

        Auth::loginUsingId($request->user);
        return redirect()->route('panel');
    }

    public function sendSMS(Request $request)
    {
        $result = sendSMS($request->bodyId, $request->phone, $request->args, ['text' => $request->text]);
        return $result;
    }

    public function najva_token_store(Request $request)
    {
        \auth()->user()->update([
            'najva_token' => $request->najva_user_token
        ]);

        return response()->json(['data' => 'your token stored: '. $request->najva_user_token]);
    }

    public function saveFCMToken(Request $request)
    {
        auth()->user()->update(['fcm_token' => $request->token]);
        return response()->json(['token saved successfully.']);
    }

    private function getFactorsMonthly()
    {
        $factors = [
            'فروردین' => 0,
            'اردیبهشت' => 0,
            'خرداد' => 0,
            'تیر' => 0,
            'مرداد' => 0,
            'شهریور' => 0,
            'مهر' => 0,
            'آبان' => 0,
            'آذر' => 0,
            'دی' => 0,
            'بهمن' => 0,
            'اسفند' => 0,
        ];

        for ($i = 1; $i <= 12; $i++)
        {
            $from_date = \verta()->month($i)->startMonth()->toCarbon()->toDateTimeString();
            $to_date = \verta()->month($i)->endMonth()->toCarbon()->toDateTimeString();

            // factors
            $factors1 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('products', function ($query) {
                $query->select('products.id', 'invoice_product.invoice_net');
            })->where('status','invoiced')
                ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
                ->groupBy('province')
                ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
                ->get(['amount']);

            // factors
            $factors2 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('other_products', function ($query) {
                $query->select('other_products.invoice_net');
            })->where('status','invoiced')
                ->join('other_products', 'invoices.id', '=', 'other_products.invoice_id')
                ->groupBy('province')
                ->select('province', DB::raw('SUM(other_products.invoice_net) as amount'))
                ->get(['amount']);

            $month = \verta()->month($i)->format('%B');

            foreach ($factors1 as $item){
                $factors[$month] += $item->amount;
            }
            foreach ($factors2 as $item){
                $factors[$month] += $item->amount;
            }

            $factors_discounts_amount = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->where('status','invoiced')->sum('discount');
            $factors[$month] -= $factors_discounts_amount;
        }

        return collect($factors);
    }
}
