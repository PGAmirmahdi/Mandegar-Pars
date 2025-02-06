<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacketRequest;
use App\Http\Requests\UpdatePacketRequest;
use App\Models\Activity;
use App\Models\Invoice;
use App\Models\Packet;
use Carbon\Carbon;
use DOMDocument;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PacketController extends Controller
{
    public function index()
    {
        $this->authorize('packets-list');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO() || auth()->user()->isAccountant()){
            $packets = Packet::latest()->paginate(30);
            $invoices = Invoice::with('customer')->latest()->get(['id','customer_id']);
        }else{
            $packets = Packet::where('user_id', auth()->id())->latest()->paginate(30);
            $invoices = Invoice::with('customer')->latest()->get(['id','customer_id']);
        }

        return view('panel.packets.index', compact('packets', 'invoices'));
    }

    public function create()
    {
        $this->authorize('packets-create');

        $invoices = Invoice::with('customer')->latest()->get()->pluck('customer.name','id');
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
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد بسته',
            'description' => 'بسته‌ای برای فاکتور به شماره ' . $request->invoice . ' با وضعیت "' . $request->packet_status . '" توسط ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . ' ایجاد شد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت
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

        $invoices = Invoice::with('customer')->latest()->get()->pluck('customer.name','id');

        $url = \request()->url;

        return view('panel.packets.edit', compact('invoices', 'packet', 'url'));
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
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش بسته',
            'description' => 'بسته‌ای برای فاکتور به شماره ' . $request->invoice . ' با وضعیت "' . $request->packet_status . '" توسط ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . ' ویرایش شد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت

        $url = $request->url;

        alert()->success('بسته مورد نظر با موفقیت ویرایش شد','ویرایش بسته');
        return redirect($url);
    }

    public function destroy(Packet $packet)
    {
        $this->authorize('packets-delete');
// ثبت فعالیت قبل از حذف بسته
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف بسته',
            'description' => 'بسته‌ای برای فاکتور به شماره ' . $packet->invoice_id . ' با وضعیت "' . $packet->packet_status . '" توسط ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . ' حذف شد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت
        $packet->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('packets-list');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO() || auth()->user()->isAccountant()){
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

    public function excel()
    {
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'دانلود فایل Excel بسته‌ها',
            'description' => 'کاربر ' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . ' فایل Excel بسته‌ها را دانلود کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت
        return Excel::download(new \App\Exports\PacketsExport, 'packets.xlsx');
    }

    public function getPostStatus(Request $request)
    {
        $code = $request->code;

        $url = 'https://tracking.post.ir/';

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://tracking.post.ir',
            'Referer: https://tracking.post.ir/',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
            'sec-ch-ua: "Not A(Brand";v="8", "Chromium";v="132", "Google Chrome";v="132"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'Cookie: ASP.NET_SessionId=paapk3hbqpuibquzpycva1uw; BIGipServerPool_Farm_126=2120548618.20480.0000'
        ];

        $postData = "__EVENTTARGET=btnSearch&__EVENTARGUMENT=&__VIEWSTATE=6ujzkjs3ju8%2FamCa1L4C88LAJT2oY5SqLLmp25Z4HX7hVwY7Un0B34zkWHwdmaWcrhCWPisl6VHb28IrWNUKf%2BYjfy5JrYpzePp2EaFCm8%2BIAqD%2BqqGPxrA2Hqv7XB3tggmwwITS0ZWvtSLa8W0Ix3FEy00%2Bx2C64Uz3rkTHhjq%2F8LVZ%2BBeVsz48A%2FifrXYPo66rDx%2FCtd4m5Tzho6qTkd2nUjaNFdgSM0kwa6wbz%2Bia%2BYbNNsIJ8beg33PMF0h2PUZYW4FpwibBtdxeZlGdY84h1pBQnnJY8g2lvULBn7pRd62lBVF6Y%2Blfyn4jOeOp44rLuD1CdQLkfKQnsQlZiZmoOxrrgim%2FkWy6iXFieA1tl9OvrC1HA2nib7zcCLCCxpNK9m2pvSQQB5KUoqVp35dPvfLf1Qb94tNE7hZlxr%2BkPK7JfSQfx0qmXnsHwLBVBxwmQE%2FGta3ir5lrdUTXcNseHyXU%2BnBI8NGCAgmyhIJ78N9HGt2YVCqOHG35hfhmLdu00toUh1Ad3B5Msbpclqfwflaee4OswPPUW4w%2Foz2FukaoT%2FZRZLQrK38niQ%2FGxXFL08cB9G9N89Z8PsQde5xSi0%2Fzv6ApElHCjgWTViybwzwZWcIa15ophX4UwvRw152Qn1ltED8CpZmuE6bi04VGYJsVBCfr7%2Fc4%2BDHngAPUcHG1628JCgtuHpFWvYfn%2FIT%2B03YKDNU2zTvKAUDPZtLVzxSN07q1NyQsH1xq6kLbfy74ZqNf26UdlbqWoXyGSrr%2FCEPF8173dqaoELDwwyXv9TFzqvUaomG5IwcKXlJQSqUc4CBBQ5w3%2FLGqwmuEmPW3Fled6TUcIRhB0OwV%2Fho4w%2BSBi%2BXBqpNdeIvv2oEmqR24Fw0g325Th%2FDi66bwjAA3GwdjRg09QZkEPAJUzRfqgRYN4BDE24M%2Bi8rgKctmQv0rTSMDWJn1wCxeNcQijPT%2F8OGD0lOxyFdfc9Doojm0n87wgKPLzXrEdKJhq7qZNBJR%2BVIuXVLiLLW4miEUYhma009FXwusj0KchUqYTub8W89MXTGZf8GUCDD%2BV7ih%2BpDZr6%2BeDG%2FmjSTffWY8XI6UoalA9LtaXWZ7eIwd4ALVsdApnlb%2F9Z6DDmLrcBzH4hSRxDHymhtfNBf%2F1kGfTDSZq0vF3l5ZKeassjFtVCA%2B080epOqZ3dbsUdOAQDDJjS5Dk%2F45TCoH0GSVmc%2FpYY%2FpIo%2FJ7oL3N2nVFywZIhVVHTk%2FVrDNcuZ54cC5fa2QTAq9q8IeGSbYEXTVWQiirh9KfcgRk%2BCO%2BTNHOKbrsyVedhZawR1tnhfaRHAyjKYx2yaJCJvjyY7lBWS4QN7vTaydvCazBrId0ksfHeafAhamAOkZ7NUmdrRPe%2FaHAxxNgucWX87wM3foA8vIp57%2BzK3GDg8YXK9UVA16DshRe6XKoHfcZnduo%2FUOkXSPgiRR%2F60ivV5Qvgk7GBVqO%2FrvGecMPT38U5alTbb0qs%2FoK8bBDukKEzEoz9phjfDOwmQBE0Hpw%2FprooCgf9QAhk5a8E9UT3tOXsdXz%2Bd1gkJ47IHR3zzKzzCVq1QfTqanDOvbe%2FKy5oqBxDgSznY7cdJqXaqSKOtiI%2BERZQ%2F9GPepJkJYM%2BayPdZOeoOXDX7kS%2BE8pB7r4EvpPAUWsPRqbK94EzrpMLe5Y%2BlRdsybF2EwD3%2F8o2TFKXKnRlpWAqY%2Fu0aDZhi%2FuSXtoSdejsp0Ij3%2FUqbjjBYZR8%2BgOn5NREXJT%2BswG4RSEi%2BRjVVk9UKYrxyqeYmw4xesvDIqtIjK1tNTSbD16o8mycDt6gNchESro%2BHal4b6PZMAdtYrNm%2B4n%2B7aGESBerrrQNxvNEWiv3KPgdEqLsIwJ97GSEFaxumrNSQk8cccJaPOBWDWfOwGu%2BTcuxxIr1KmU11XXzFVehujiCb9Rjv6ijx8ZGoA807tmnTvUfX0JCTrYXHOeUW%2BZRt990CSwA6OS%2BP3z1VbjyhteHFktU2NAn4Y0msX4zY6kmHkOSh%2FgANXLm%2FC%2FY8dXsxBYAzMrIJyQahE7nPdofcemau%2F6paiXJ11E1pQzXQRFEStDJEqVQ3AZU82HKAJ0IiLEQdgLML%2FEHAr0XrtjD15FNYkn04wyQXltxShoOThmALVJbtj0Lh31tUw%2FsizJ1kR37PGqhWoLbcNTR7l8cCNkCV1zvk8kBB16cHDzPhecVpqRbhc3D4mQrfVbENDiRTuqsVxhtLDmrY3pnIRcjl81yBLsDwgXERVvKJHZ9IN4V2ccvUDybGWxqW3vd%2FdcDt4raecUbE2%2Fr8Vaw5qmW6iZVCk9Pc7H%2F2OVNpH5PlifRXXsvwJ8oT9o7JY1gC%2FIUQ%2Fk9UR0cItZj9gsB9hmVAhjWqR1Ta5q3htsvk%2FfHqB2eF8lnMANqkwY5h6HiT82IbxCuJ4%2F8RTZLnCFDi0JSRHrNrsAhgXf1FL3jVsq25SYh482SKb8BDZ6SaGOyOaE6psFqtoR8z3bpwKpihiDYY0J%2Bbvp9euf6bKpKixTJCRCfUiPvORf05zR7BUYKq2FZKrISfqfHMC84Kt8K3sEKaUyKwgPyOwr%2Bq4BWsct3O0u9KhGfxfwqXrYTT03p2cM%2FJDuH9CAWgKu4jMry8LuL03PUM39OU%2FaTfBcbC7I6lN3KFdxliio2wdF1XZbPX4Y8UakpslrZzmM28NG5req%2BYC5x%2F%2Bcin7EFctcXOYTI0hEzGXwksKC6ZA0mcaiV%2FQ66M8hGzy58%2BMsL1mbBqSRv5DPMKDo%2BaJ7V3MFIoSkoV45KH4ecMXWyZTU8wZvxT2VZa9xKfZ%2BcLEKC3oPeH9wDPh%2B76ZgGyh2lBLpIBLbHomVOzCdDR8yks3V7bt73%2BKRxV4ZyFXIuflOWs7aPS9cwNyKnoyhbjUKxIVDdKJ1Vbf2PtdE7ertMmrBQbZoh8x31QfaE%2B3cxTFmc8B7QnrphhHtP5nfyDM0FFdQj9QnQE4Wyk3C9Taa86G%2B5zNKSeTBtck31xp8AvDk%2Fgnsntt952vw8kbuaitpBz3I6GB9mEccJPVlpK2Dd5RVfNPo3hH5%2B5iYSq7zqPN3%2BqBy0%2FuE3Pl68gHU90L5jaFoVYmXVA7WMOP79d27Fp67wbHrYlpWxi4wDILFtTsfT3eg%2FRwlYeC3AmvDex%2Bn5aiuFIDQGC4GnMoUO1G8nLkFz4Vnrzzy2cuvN%2F8d1jSG6h2eMHLaxR5rx747XCFFT2V%2Bf%2F4na3d4uxs5Tx6tUjI1jjhXkX71KcTgR6AQ77Q4m3FZ0ZfsM5wg86iroZMs%2FCb6fFbc1p4mwDHnllzmVhkNahpw7FgNGwKzpiSPH2h%2BjfrxzPI%2Fhrky0Taxq7c60%2FqgGQjDX1jqwL%2Fu5yhIGo%2FII8mXuicNWCYK5jHW%2FhOc3FOavDpp2SrMSHc%2BFY8xrEWOFaY5koHmBf9QdFFUOxzuGraRSVn79jFyJoJBAYZu7xu3ARWcFcNRVVnVfXrptQhWacD6F2BHMYSc8A0yGclA6rLPnGJb2DgVEFU75VDwUQVQ3vQaWruESQrLUjmuETHy515aLWX%2FWPDNokpPHfM41Y9nM%2FNyNh7E%2BzrfXrk65ZO1Xi3t7UFBY99%2FKyoYo6DwSGGH5zb1K2HZJTE2eGi6rQYu24cJVhBDoUnrqbAHttv4YP%2FSHUV2gChWLRyrET7zcqYoodx%2BqQz87SHYhzYoU0PvUqdGgbRZ87rk8w9CHmca9aR4%2Bh43xYJGJzrNXHASkPpbkldKXlySaMcOArX40jlEEZz6Mt4q1ib%2Fl3zU72KMXgXg%2B8zFTsLfsxj3t62aZeW8%2FnVL69NrQOoX5izrNGwQ41IKZ3MqhVfA2HXoSjEAB5dkiwEpEX7wTwR8CQyNI5DSePX6Vbjw3quyUl8ixRMiPKgEZt0DiX5izBikuDw%2F%2BnpxySz0h6bA3%2B0J3w1ZlBsUp49Irj7GnhPS%2FDyNgKXi4DniZXewve18npKl5Jy%2BEC9JHvNGAc9ZBk57CKdbA1PBIJzFvjkMQepOUCq4aLo79iylmJL92UWV52rZEVLUhr%2BleOTmpJH2UusBF20S5TQGoihy1WSjLnganZtPMzUIK5lEzWPOtCaBE39q4wVQQEdVnKH3bSMW%2B3W44NskfnUaT2%2BYDC9FMkl2%2FxWbcQZgdTPhQYFab4%2BUjX3EUl7f8M5NIz8uflehxrb%2FiD5sDGc39RaoxHUdsRdutPAYqLmMDszr00YAWvrQtPPNEKSFTJ0c4f5m1LLc6M%2BF27NXnVvD5Oo%2FvTr74oykBKBHmri%2B8pYZV9Dv5gfr6wjraazFOmcHyMbXEigHuP6j93ADLVoC6o9pdG7A6Cb%2FCEYnvuwKNvyePXnVfp8eQL7Bmad5FNs1GxC2zxfXjoETZmkYJC8nkrpoB5zcN%2BKI%2BifoEdOmdK%2FBRmgnuYqOdIlCUdFl5pu9zPGeo3X15IG%2BTzgDEXaYJ1JhGQ8K6XKhAiNM3jj7%2B9AsK2AmRoPQkrNPXfqX4qVeYbJRZ2fpNyQj7FEnAqMVxQK9QhFSep%2BROl%2BRB5L0t9qmy3JwhotVjHNQJQfvFNBZJVH%2BDlvU%2BA5HGwl0gaPIHaIDte6EiCUfFOR3m3AnT3Gr7Fu0qrSmldKfpHpp7fp4fOBzDb%2BeOR4zkPzKICceGwTDTEbeJ4OdmDHfRjsHWpaQXl%2BVzaLKbSt%2BXd4D1YcFV2keQZua4v25ct0uSRRoSsIj8MGacTb2owfFrEdnv7VwdULL5CY7Urnef%2BwHpE%2BJaZsUop2BIpClO%2Bvj%2BDt79fY1dLKb%2F2Gbxb8gycI7gHXfGHVeNmiEAKBqNq%2F8TKP3w68LRRNsgVpQ%2BTXj1a%2B5zLJLdk%2B%2BpHnpqLxVhEU0fRiJpr7MWc1xyjyx%2FMZbcRWEc6Zu%2FWfNz%2FMFMIlmD2awtdnVwsuhCy6nMJUb03n8I7ZeI7rrC2kPFEvDIVNng0mHFZ8ElmAnldgQZtZA4TV5jZVj2kA4hovvoN9hMOMNu36ql88Ml7Tr7qeQ%2BbczC3leRcIfYYTX8meeBnbWnzqTdJ4kTsMNj68tdvwLYBTyaNQKLGjdmN2PK%2BjrrRJNxuSgxeXUaKEiUDR8uBqHeEI7uFuhbGbZw6CdQlbbBgg5gsrYJ3EP9UbYa6xKHpTSI8Z5SeZh0J6dE32p4JwjSN0t4%2FxdFOQ9uGj1E1jv5gErAXIMHdviDDCj0wvd8v3J%2FVctC8rV63FBIc1VoE9t06%2Bt8rQFHGiReQWUNfuCfyODB%2FgN%2F%2BptvkIjnvsCCsDgHWQ%2BDFqV9GtSSAORxNRA1UyzY2SDaSSYVJ2mfF8t%2FLuY5decKPxbW8%2BCx8yXigbjaclwvgdYTllvqNsX0NUu6onenVR88w79DvxBuK1C8sbVLwBbhnx43XlhpmSU51aQlDnqIh7MQLu9D2qe0izVnMXwMXMm3fPckGRDermDiVCm8THa43RGmfZBXC9a9zezQw7cjAmrAqMhH0lpcHUrAibp5l%2F4xHzJK8uwSJ48JD2lfGVvJkyzKNH2A5InnCsfB3XsUEGiSGYNlXBusiS7X7wBwnFDIiEtIP0f6eiuulezymySsJsg4UAK8TgR7cQBXjFjQOFiga8TFwSCii7jHTpw%2Bd1yl%2B59L562RJs%2BhTG4TTufxgZBAZzimE9HO37%2BbYiDgLPjd881uF3kv6AlsbiEPwfTzkN7m58Spm%2BF1fwB%2BwOvyR%2FnUpLsy%2FWQWRmdzsOfSYcxChyBtqycFhdV2sLR95CDYI9SZ1vAeD87Cmzq2LIoZ6K6pRLo9l2kN7gM%2FzikCUv1XcTBwCoenufjn1XaNlE0QMV0NKDayZ0nbYAZCtQ3aDirqkqM6CjWF7YuoVtOSQp16cXoDwIAmHVGXj6cLgdLM8Yk7sXGxruhTsaQ3zaXGmy%2BIuUVRqPpHbQFGUA0gZWH3eFaeKr4GCC8fF72CXprrpHA9Y3vqmT9jKkCe8BNoven6oe3vPAdaPU0QkJKCJPwjUiyTHtSf2VaRB%2BbNW%2BTudT%2FM5Ou3%2BBtl0JbTZz1CdpNYI%2BwqYOlh%2FwQzsDv00fuWAggcrC69Mcn8tBnASnZcbu3dv%2F3dQRCkVlkTFhAi%2BDxQ6waqKNLMHLj9Y4BXkUlSLay0jGNGae3P2%2F%2BS4B1KE3uqeNmdtbnF%2FRKDBg3wn6qOVQDz9q0b1bysjU41okxHND0iKwtGV8ALCObixEm7gMRXyZM%2BRN5qP55iLQpgUKIaMXYbG%2BaoT%2Bvew13dxesrGY43fcAEquC44%2Fq6m%2Bnk%2FoF1%2BMNFPW197E%2BBY23VL4%2B70TdWU5GKfTMgSaGC7xpLdRIKJXWRKVq1V0yRoMArLj3ykyku3RGwwAcGrjcBmVN%2FnoEw%2F6gflGc8evyIMt56VLufwY0ZU9AYoKVc0c5Q04QHsQHO9T7ME%2FOjJhNaKosg%2FmoW7JNggkIF4MfiS00dTJGBGPQYtDCi%2BbkMfHDBnRdf7rFAj8wpdwYSFHga0XK7v2Yhy9lbuh3rba94Pt7ptD4GHaAMyg0wzDR3uw%2BlyOndftsno5rM292A8M%2FnSv1DMYlt1c0uvbrvq2ojOGf5Cz%2BttBFp7VEZyG4vtlFjs1M8cI8YXwmqXqwZL08z19TImO0JNPMfDmywodUThVnCeO0kUoflILQ8TKyNwXrfOUqGGeKGpFP3NSSR4OfoqRzXdkWLGcH0TKtL%2BypWX6RzpMzRPzT6DBxrV2oWygxq5Xw%2BMx1hW%2BNU%2FWGxpToS%2FJIS98e5qiwGxtJ53vl%2FyOTjplotZJud0IVY4eqngOi6YDnQyJmhxeOvsIQIQs0Vvl%2BpX9UrsWJpb4Clc1k6Yqe2j2eYkwuXEOeMRk0LMpBv0LJKKHgg4Js46FXhgYvDFKN9S5njKj1bQQfNKDDQRmoKhAUtElpOSWN4wU2ZmHKq59nsSkDt0W8O5ko18plSskS%2BKxCMMDvgJKGypjgQ%2BlNxrZMzbQyTMbcKQ1aeqEFnJVzFNMUnxGAdtGMFIQB9mK6xgd92jFC%2BnJPa9kC1nL8nSOR91TMyzEUAJrEkXKIwQJ25%2BgRwH3pHGJghGSoAObteWzU0NG6OjByi4ZSbCT4v9kESWfOyjwPOJpVTdc%2FSA429n4GJ1NDUdUovmi4WCIoGB5yA0XpIM3OOdgBOIqpuJEixq6BVh%2BvY%2FzlAv1kKU8fhQRAR6RaSioJJhfBDh4d%2BIaKfqXHXF%2BxEFinPhHY2fg2QeiJ0eI501gKloiROsgXzLHamKrwHRmthl5LnNIf4Psq9Mc0oQDApcTH%2Fnb%2BtSwRXFGIFLlYdnxTujCSx5BExFQKaE5ICJVFW5bnbZ3uSjGu5LELqoYS56IAKYrE6pa0Sal0g8JGr%2F3y78jht8CWKbDG0qQEQzT6qu5%2FD0Em7XuDweiI2O38KBdB9tMI6GSSErsNzkXzXv1wDZMxvzZauNT7krjVXtD31GuNZO0Q646ricibsFJAsrQ6%2FBMnxIeT19vmNatCUjVFvN5q8p5AYyVPWTR8jEXRTpGJ9kpqgcBkdhKR%2BlZTYOvjj3G0b159eNmdScU4EIXScPB04rbqOVCRwDKEQ5Q0wmkudjklqiJQFhEcVOBi5DuMn2Rigz20vXDyMjVyAmwg7XzQtT3eSDPuaCqZ5Cf6Khr7NUJBYbQmsqhhpydgu8WxU3HFmqVS2efkWMXiZDzS3OEDUhPZkTau8LTkSjCZeCIeUcYmkm2ZobcYGifYcDdlTkfmlIlWV8iLVMDwo2R0FO8nCjMtMLIpjwDNf9YhPtSTbiyl%2BKBMT7aVN%2BZVBSlUCgtMXvTaJhCqdz7zdiHzkwr%2BahWbM2svYZwdswuSTVSbbmTE9ClMadrPddLBwHNIu8X5lJf8SAEQawUYpzkLyV6h4rLT71I0mNjCfNkv1b%2FXqZo%2BfdRGC6gJjvGcc%2Br1el2QneQq%2Fi3y5HuodGs2MEXNBAdzHyZkzdZmQRRN7CztVx8ukEUJl4OGjVVNYezE%2Bo%2BUyb8t09%2Fai9QYxX1aZ%2F%2FJGaW8rsEZtrJQQQAsHX0jqfNnPVh3R46rB%2FtCiOgYX6VaJAjCQOLRLXYZPFtpleAxUyuXBSfawdH2fSOT4zZWV94eeroF6WubPHIn7gID%2BU32G3N9zJW7joIdSqD9xoFl1KMnDwSmY9WuVIp9RCQr1dI6Dv1NbqNdxsXx%2F%2BwkE5O7StN%2B1OIATWvVQ7Rn%2B3KVypjvnVDjZ0Fg2SAqKWd9uXxH6GwAqit77FoSzQl1LRz%2FMetM7qZlVyshtkElftP8Q0NIzeD82W8A37O2EW%2BBE3ydNOJNKEYqfi2Khavslk%2B5L3a7RaOLC8qpjta94jgKbgIvPpMv2Y8o44B8bRp71C0m9CrP70nzSNn%2FZu%2B2LdiLsoc07lOCMH8SOBO5S3TSymVj6Gi763kDbfVkDf6RvTeiuqZPYDWqZWbaupnixUHW1Vv61VHSgABF6hH7%2BjXQzI5DWyfVEmHocpGTT7M%2Foel%2BTmVM%2BGtiWGdp5l5tZYEq%2Bq19xN9gsjePygR%2Bc%2BIYRmdh7EKOpj%2FsuwL5ki4bGpUuoYv%2FhsIp3WilDJ9%2F8YZAj2lLWt8fYFhX4pw0FnyusoLiFxkzSNv4wD3cs7y6pfGUXPW50B5i9YEuCvgtJzzG4bnICchIrPdrntYD9VtVNRirrDtK4bczS%2F0kdtrZ6V0lHrf76tXTvq4xnZTZ0du2ilKrP4PmA79DtYtFWCe7%2Fs8TNns%2F8r77AbBLWdd08gxYc750kLbc%2FTs7B65sOt0cxVU%2Fwi0qHUEHwESAACT1hFgdioY3BeffYjbIfo%2FeAvg0GhFnlOZcjDFM9dmp0x7Bpwc81Ze9uDhrBmq3qpdWIpyA0ErEJ34pF5qKO0jqvVt6gne9M%2F9NI2zA1jP%2BVHG%2BIanz0A1ZXwVaIvwN3so0epYYWPNEHKi05gDrs3UJVzEg1e04BsxsLsCo9V%2FmGMdaVs8H47eJAYAPak0N2VVRIePKANfWlWUT%2BY1SK%2FuvKoiLICtaYJxNlPHn6Pev27KnPXrfPljonkfqTXhg3pcEEyYSgaaa5ryIlANnvc15TkQJwKOBIviE55p2byHY8jLCdYRPSHzfldd5SQm4wmde2Er83DguXV1xrTUrdWmaqyy9FDwCfd6LHNIsmJOd0Md0IY7%2BRxeI%2ByggwYn6vGBFMFCWqLnuSEigFkhv2h9t6Pj1Hivem%2BULODPvcS%2FpeDySEqykZzkDmwcTsznwcC8DJfeaVgQjChQypd%2BIOgbsKdMpVL8aqCFVAQjJ2A4D80ddZaDceflnJ6YeMRYD%2BEvq3lFb8hy7tJrqbFQbIqgQTXOmP9qkoocbrGFyYTgO5unlBTnIGcDd2tZRfed3l%2FWB34Xye7TMNH6XLekfWPRwJKfkU9amaCvEPU4%2BdrJIaCtbvN8uHytnoN%2B9nVEIpKczsmeh9b7tKzLzERYbddStkCIkwGVyfmstTfAXITCnjFo5z6aX4cL7q7g1ZR90zXrWGw%2FEthy6T8mP5HTOLOFX0ZVY0W0eavhO32vRiII4Q5HBweUSsUN5K7uclgn1kYKd6C%2FQyi%2BK%2FEps15efmQvkc3BRBpNlKCkJb3XGcVCONTHRDJMSJchBgSHcPAuU3hdHcI3j0bFxd6wHpSMOfpZUwSdeBc%2FP6AYUq65lsIZW7ZZFXkzGlXu4%2BVLYYrffIEPfN5DhiEdZZR9Ao0RSgUsbKFZuwEI%2FEU7oaLdezj2%2FCUZAqytwQAbOLkaoZ3XmJlOWLlKL2z8HBqChMhmAaWh5E%2F45mDGHOtskgcF5HZ7gI4i9u7%2FINfIMix2S0%2FqMImn%2FDSqtVDsHZ0Hxc%2BOuZ1P3gGdRt7jPTdkeCIQWo8vRt6sz6vU7VzWqCYa6nW6hognlcQO%2B4lakB6fdqiiojsrFqx37bBXyICfzak5JXKRYYR4JXuavhk4P8nbJUIy0dpRf%2F929jb5ySvKU4PzFJ8YQzSsBOqDZi6WOokUyzoNHL0o4jrrWAT9XviV7xKGjr1A67wkIcw5l326eDIRKluY5fKLeBIkA7Mj9%2Bng5XY4B3M4Q5dSeswiJ2xE0XtzwCNOyL%2BoinOZm%2BL2rC%2FWPhq6r8CtTAsYrSEOgul9cV9zMu%2BpgvS9kdg9z6Li4wRFyrbVbxxnkXHk3fClLamfuGmwJGE8yc4VL2LQQxFeM4vS1x5B31z1oAbMQ%2Bhky6RZpklY5mQc5EtEzRxD3QRZwLG%2FjYroIjyrpVTo4Ba5%2BMX9xwhE2fxd0MQd9JzbnPhXl7%2BdK2H04IkTuYsQWukNwuGaI%2Bkt2vZS9c8igWSlAHaRSJ8Hxq%2FUpgc95d7jSP98DhxK1yxg%2FSFlbknjQzjgXO3NcuGOJCqoLPuSy9mhvqAt1cbjF0ZIOHejcvTTXE2%2Fwrav8h%2FzRocYWPESoB%2FCih2GU8NF6VolozPeXNRdyA3fKvabliqu3FXFzwQpMqdC77v0qcUh3k%2F5piciERaimZL3oZnnTwyf7vpllEHSAvHy2QstnHTm3jHejuPqWpjR%2BcZWPNBwp96W6keHOEaOfB3O72W7%2FxHeGH3ftx%2FqmidgPfGARdJ8CrChEUEfOmZTPy1NoVWlyLC58qXWltNKI4BVVBhy4BkkJV2Z3K6u2yvoqi7TQVcR1RhI9I5UtUlIlJmNihU3x%2FnVY3Jv6z9Zn%2Fan1DVcxU1a2fH%2BYdiZUcp%2FLhha2S4YeK962Hhx03ZDy%2BmrRzMBxCLm9SZFGDH6WlioRAmlIBOfKgxcDCKJtSW1HfdnUjZofuxkwUKL8pu364i4lycpPCVK%2FmwNvlz%2FSvpmiNhIj2WZUxu0BidGRISUzAbQuJepHMVvA8p4JIxEM4bvSXX3xEKt11M3PR%2F9FgLZTgUKaoAJLSAJn0sp5QO78wHgQ3pEpIAzjNecodSyhe9PhLEsnRqaiTmG3PHkAI%2B5vWOvhvI0sgEhy2Hzsep7Uz9BPuXzZ8QCpqO6Q4rZnlwGMbGth%2BYPAYf5pBcqdxAp9dzhQ%2BwbQQMJWBKGEumDtsCun57t8GNJ1wcLX6RqvdXD7u4rou3TIAyvq2YPWX5q41HHAeCDyz8nAzCnKRvWLcSBvDOamofHuDrnEYRGdkL4qaJq%2FOn0xLt0JqHpbKhVWZAT7uIfwujOH%2BbtX1QQVm%2BXUOuhHoXldMENUPng4aNvTM7TvSHsLbLuS1gbVnQ87A5AafCotGEe6%2BhXjhewsBIav%2BEhsKD05cLDJMLPLVB7CpWMIfKr%2FeXr26YQ7brO6Exa7sA0zxJ4%2BDjTnDKi%2BvUIXbguTRQgoR29MVqBKnfKgUwKO%2FdrtGvYbo4G3tOjdPkePtasgGNznRr7Px%2FnM9n%2BO1sZdBodQab9FBs2tyhu5RkFwnwFGZXj1a%2BAxvjIlIUbsimjGCZ6cMdIaJlq7erdDiI5f%2BCu0HdxbeSPg8Yqs56ujbqJWO6%2BYWbkjmufcAaMeF7gRN3A%2BdHpHXkuGDmu8yCyjBbSNdh4mtRqA7YpBU5v8erM89Z4X0VUo95qAiyWdr6c1ysv7NHxgnvONBtr%2B5hqBWyncHIcwI3aMoKaiIbG%2B2itXe2q64EZNK89YSur6zy1C2t5KCAwxqY26VkeiG%2Fl5%2Bn0OaN3m0vWMnvq69JBuAIN5JsFHq1eahz7BBqnay6vroKo1ryk%2FEGhQ8ZwwK22tRMhAnaEYpHJvWbprHBjgv3YGS970IbfEBBvO8aWen2bQZOi%2FlzFJ9iwWMCFi6zLGfRXw%2Bz94f45gGpOu19sHEpm0ds4xwb3%2BN9y9acOUWsEWTIr%2FfzhAmHwnUpuiwiMgXWXLMMqJ5Yeunzs255j1MfdtSgQNhlSw%2FDyMtbJsxfay%2FN9eNRYxFje7w7lzgkFSZDVT2SBXPOFT%2FuA1HGxxiPM%2FPNZs0ZuT%2B3%2FaaUJffuvq47SeCl2NFupBjFOfKxEL%2BEUmL9xNQQQKPOy4Wrn84MVvVfd8el8FzEjOVdS2cLJhuGoZmxASE6jXeBFghGAfqxc1D8nDWiGCUOOOfF%2FqacllegynSVtOBZXStyUHDKwMCagEkDQm06keuEWbnm6kgqTj4DY9vJTv%2FwSpOsntQZTbV0C10xUYTgn%2BI4TDytUGorq9vKlEGwL2%2FGkcKqYXnoJTS%2F3iq9p3jR3ITeCLwUXtX3Jo91a1cRk8RoH8M4kYipPu2PcPJV0H3gTw7jC%2F%2Bi7Fey9Yj3HdehTEqOicMDYFTrr3N8YEBIqiJZob6DhWiIIac9Q0ACrKBuCQtTGgthb274N3%2BGl7CAGc8VUzg9oCezeRGl3PJ5EwdSrkzuBkShY%2FPbXqG3J28MCv8%2FjBdZOopOqTDXgMoSKtlvDubgeSp4b%2FxkEe7tAB%2B0g0HHQnAdtwbrOhlCvAncli5TR1TQ7NrEakwf70OiMRNqHqwDF%2F1yZKk4Muq1ld5Z7Hr7LKYnpruE5QkvdOd2nXzxuW0mF3vzH%2FJYMZ0q4Qo7psGPHOC44KvTuz%2FuYcScJwrpXcOeeuDbU6d7dtW%2FJR9zypaECWoT1qt1EwLbWij8HeFNLHt86KWBTN44AC4hD7kPN%2FS%2FNwsGlekR%2BwArBwTaxx2pWmSPgP5qqQdP9Xy1xQiVL2Tz3hFESlSb%2F3A4q0CmzaAaBmagDOy87X8P23EO1OHb8CAvQO%2F7l4dNvyHiYkD0%2FIMQpD06Alk3lDwK3yr%2F1TJQR5UbznO1Ngq4pz0lByGS2hbMCiIplbNbaNdDrUDq6eZ3UWrs7hTGlP8%2B5rxpd5AXhpphrBD2IQRljqIsf1OMD1v2Uo3Z0WmFS%2F%2BG1kj0giEyAKvYHRoHZ%2F3pIjDKbOk1mPikMx1t57Bvp97YpjS0PfJrCB%2FujJel4%2BVQULCft2Ftaccy2rjgLCIQDuv88J73WqJXAJpbsozV2mq8E3oCGGLCFMQx%2FO68eOdH9cKafmZBzVGv1F0DO9SNQTL75D6GKRSHJYT%2FoYLwiaggVmBWxpO26xuZomDqCOQIo3xb5RPuEhKUREq85sPdkZsrtZjSmXOnFzV%2BwOkoBCdiRGbsHtroJtk%2BpCXHaiARyv0%2FLmmtQSvTIYubzF3AzWYhTkQJIc3%2BlSdcAfMyaOF4kFTZaIRNxvJmLDaNVLs4eULhiXnHxMTEXcS4xYZv1vO7w%2BP7%2FnESz0fucHgAfB0tvcKNslhqK74x9XIFv80ssXG4x%2FTEav59rYU%2FfcX8jdy9dC7pH0kJvvR0tHLbTUfKXXSTnQ3QadoM0Vl9gKTU321I7AyoZMtgRzCXbbNH4Qi2mzngFmlEIcYhxeqeg%2FlQkb8Z5bSIUqnrDyeWFTzjxzODpPhBM%2FaOBVNvyncfPV6qcpwv8zJsKnc9g9ojsjUDSHcmwQUEAqzCWbGzB3PfAwDQPQWaEd0fVt5aFBbTKBk3P3i%2BOkc7Nx%2BTCpUNua2%2FbHCJKQQi7qisBswtU5HZV%2BInLitraa2WeHkdw%2F%2BK%2BdS8FV5o3%2BwEDQz%2BHwtZNiv6tp3JzipxjbUFHJ6acSGKCKpKoCBBTGVNnWD15Wz4Sk0XB1rPsU2a3ozuiMtDTg8l%2F964tGuuXuVRcNQ%2BMCX0WI3dI2lxBB56ny1zs3%2F8nhoyfU25P7upPsAfi7YYf76cnkSDGZa%2BfdwE0ss7wsVNNtntcPeYYR0qSpny3RJnfN2dTTL6rx3oXx04QbTzz1xl9i7xDky3Cqxi8xW4ULhAgyGnghnCeMqXtjY9wbLnn24ItyFY1NaiAzRtdu5IlVhpkcLY2cmAEgtFVyVnOiNP4FBVZqrHELRPPF3qq6URtsLk8%2FWz9DPFmfQ7nzGMVOZldSY%2F%2BJtAoDy10kflePmU6%2Bj%2F14MmluamI%2FGbyC%2FZRxiizWYpL%2FgAjUEK4yyTEo9QPTe6FeWVSa7XJmAIuoVlawQUQYPGvY5iDWeBLRkT6M3as5p0c5gQTmi2BGZUX8BKE4Ra6KQWbs4Y3KOsKC79p6WKZ5xrCC3eswIT2Mf0%2Fv%2F6b0tvx3M%2FQ0HyRZ%2FJYSH0FBu54Ew8Prb2n8pVCBfB57o9VTKdGJC09hl6JQGaM91HkXOlTCLx0OZku2SmOSfZIY0AqM7xuAd9BgF65773R28a715uvMx%2B19%2FROl0uC13UhNVoQRygDcIPJjRLmoASMcjtJArb3x%2Bb95yKiZPg2S5DiVaDHbaFfVuSSMNz%2BrmfRwFZkyZ2wUd7KszbKqw1iZx8Azrmsn22K0cQnr3hEhwDs%2BRWbAPYom5sTNaCNDmlEsFzgBvI6BLpjWyoZGs6CtnrkUkoNUA4ZEZwlqGiOoGqgpDN4jQimXA%2FcvJkMbS0X8uC9apvnhAASBuuiWC4vvUInIVzq4zbdMndjs%2FWJJMTZjaLECAEKb9YtygPD7IGIWMJFC4wq%2FOGEx9CnOLhq7%2BENwt25zSoA6%2FlE%2Fi5oCjj0jlgDaWMaE%2Bcp9yFcTzB7pQIfnmw%2BsPkHQuJTb9xL0CUCMdYTAmUwJ8AuUWt%2FhONd9rJE0PnpP7Uwn6WnA8OZ4tNMxVavEf%2FbTpHqqsw3dtsCwh298g1o%2FIrkX464YhVPARQrZ3ZVeKq%2B0LP5tPaoPBzneCkshDFi%2BhnnmcYFlMqxKVWj9wF5a7fPf3GV0rL%2BUovqeb57I0n0KzFe9%2BJE%2B3%2FX5bJU7VP%2FzjGwxEb9BcjBkpI28xLiVrG0%2BJM2sGWoOjY81c6fcOBTUUI%2F7Ftn%2FiPcj502VFd8l9MCxFB9nKWeQI8FJt0GG4kl3EEhtm7AhS1EsM5nDSyc9ffkbWsJMz6PeYKchKKzYOMwQlJ1f4LFrw3p0%2BxV4%2Bv8Bg%3D%3D&__VIEWSTATEGENERATOR=BBBC20B8&__VIEWSTATEENCRYPTED=&__EVENTVALIDATION=tEo0NsIUid1FsBqdvhIZx4ubMYqRGIyOy2mofR1B5BXhbd15fmYsGHwaUiczRsWMQzyUS6Po9LNnC2JsvC%2BraHQVvCRrILu%2FVHszf3E7mG3WzOlS%2B9RdIIzGpwfcgHkqEiosv8xjwto%2BOOHD8%2BkZmZMwkR1AuAUd%2Fb1Va6yHGwk6dFAcER3Gx22563%2BXPnqxp0pspqOVjfoBEKH60CbyYw%3D%3D&txtbSearch=$code&txtVoteReason=&txtVoteTel=";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        $dom = new DOMDocument();
        $dom->validateOnParse = true;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $result);
        
        $rows = $dom->getElementById('pnlResult')->childNodes->item(0)->childNodes;

        if ($rows->item(0)->getAttribute('class') == 'alert alert-danger') {
            return response()->json(['data' => $rows->item(0)->textContent]);
        }

//    $xpath = new DOMXPath($dom);
//    $elements = $xpath->query('//*[contains(@class, "newrowdata")]');

        $rows->item(0)->remove();

        $data = [];
        foreach ($rows as $element){
            if (str_contains($element->getAttribute('id'), 'showuser')){
                continue;
            }

            if ($element->getAttribute('class') == 'row'){
                $data[] = [
                    'title' => $element->childNodes->item(0)->nodeValue ?? '',
                    'is_header' => true,
                ];
            }else{
                $data[] = [
                    'row' => $element->childNodes->item(0)->nodeValue ?? '',
                    'last_status' => $element->childNodes->item(1)->nodeValue ?? '',
                    'location' => $element->childNodes->item(2)->nodeValue ?? '',
                    'time' => $element->childNodes->item(3)->nodeValue ?? '',
                    'is_header' => false,
                ];
            }

            if ($element->childNodes->item(0)->nodeValue == "1"){
                break;
            }
        }

        return response()->json(['data' => $data]);
    }
}
