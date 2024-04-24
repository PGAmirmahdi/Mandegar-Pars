<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacketRequest;
use App\Http\Requests\UpdatePacketRequest;
use App\Models\Invoice;
use App\Models\Packet;
use Carbon\Carbon;
use DOMDocument;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
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

        $url = $request->url;

        alert()->success('بسته مورد نظر با موفقیت ویرایش شد','ویرایش بسته');
        return redirect($url);
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
        return Excel::download(new \App\Exports\PacketsExport, 'packets.xlsx');
    }

    public function getPostStatus(Request $request)
    {
        $code = $request->code;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://tracking.post.ir/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "scripmanager1=pnlMain%7CbtnSearch&__LASTFOCUS=&txtbSearch=$code&txtVoteReason=&txtVoteTel=&__EVENTTARGET=btnSearch&__EVENTARGUMENT=&__VIEWSTATE=0QlaQX2oyvzjIxiNWS7EH2E%2BjNjiUlZDd0g%2Bmy4%2B5ILRJ9u9QT%2BOZbdJO1R1vPoMqCzCz91fqHwcc6qOiEOmbxuXatguwL6B8eGIshJaP14l%2FaT5UYh1UkMgvddZnBI5%2BJq0LjLg%2Fv9N%2Ft3iwCF3NJPTCjdKcF0KxwrQ%2BWkbyqlLAdVDEiU4CL5lMTUzqx5Ig4EBM7msGBh6mQGbohMmryQYKG2RPH1hDznJg79qFsMhzzSoIuIKy2Ork%2FswWCDv2lVsQez1uVjIWY%2FG2JXH6RZxklChb6V3QD4bBAjFJrE4LswKXRJ7hGmk7D5RlLE%2F2Ged7SY%2BZA%2Fj3943DQs4uQbhBt7zMjKqs%2Fwlw5rl887%2BT5tebN%2BHgVQ31ZL9c5yaOUiq82AU%2F6JmRzCvbC08aVOlMapRu1T7FdmQrm0wSltKHifa4EUiShVkbrDbozbI2QqoTSyPb9fIpUNbhA9li2%2Bae8yS1QkCPFbTcrOpH7eJ64jLr1O8j12sB4BoVLdWGYSfsmJapkeLNmKPoiwtMpY1qrDVJ86UPfB3iE4vXY5f%2FmBVq2V%2FplKDznENHJ7W3FmAFyB5xWy8a%2BZGvy752tLafYZz5zNWHwRfRI4wCDISuOX%2F%2B%2FV7bgSLqZrQIonTZojRc%2Fg9B4a%2F4rrx0Do8Cg%3D%3D&__VIEWSTATEGENERATOR=BBBC20B8&__EVENTVALIDATION=0qG%2BCCYceg%2BYN38fAi8Srq7YY36Xmb7PBZP947ggR%2FMkGydxpWYVYXZmoo4Znxwlcujn7Y1aWWRqwCyfmt5a6j%2FDjzDkQxH%2BnmmJ7zWD9WaN7eb%2BJyq%2FDndv4uWaJQzsaJHO%2BfCgqh7eEf%2FlSwAvbYSMl16q%2BBoLL1qMas4fzBnQYwpgnG72G2RqICIvdJg%2B%2BV5wIrAYg%2BMXFHhN4F%2FaVQ%3D%3D&__VIEWSTATEENCRYPTED=&__ASYNCPOST=true&");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $headers = array();
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0';
        $headers[] = 'Accept: */*';
        $headers[] = 'Accept-Language: en-US,en;q=0.5';
        $headers[] = 'Referer: https://tracking.post.ir/';
        $headers[] = 'Origin: https://tracking.post.ir';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Cookie: ASP.NET_SessionId=d0wwktqw3m3rdtour05a1ott; BIGipServerPool_Farm_126=2120548618.20480.0000; ASP.NET_SessionId=0dztzdhnxnl0t3kdn2yfbwt2; BIGipServerPool_Farm_126=2120548618.20480.0000';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Sec-Fetch-Mode: no-cors';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'X-Microsoftajax: Delta=true';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        $headers[] = 'Pragma: no-cache';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $dom = new DOMDocument();
        $dom->validateOnParse = true;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $result);
        $rows = $dom->getElementById('pnlResult')->childNodes->item(0)->childNodes;

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
