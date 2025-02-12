<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\OffSiteProduct;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Tag\P;

class OffSiteProductController extends Controller
{
    public function index($website)
    {
        $this->authorize('shops');

        $data = OffSiteProduct::where('website', $website)->latest()->paginate(30);
        return view('panel.off-site-products.index', compact('data'));
    }

    public function create()
    {
        $this->authorize('shops');

        return view('panel.off-site-products.create');
    }

    public function store(Request $request)
    {
        $this->authorize('shops');

        switch ($request->website) {
            case 'emalls':
                $this->publicStore($request);
                break;
            case 'torob':
                $this->publicStore($request);
                break;
            case 'digikala':
                $this->digikalaStore($request);
                break;
            case 'royzkala':
                $this->publicStore($request);
                break;
            case 'ariaprint':
                $this->publicStore($request);
                break;
            default:
                return back();
        }
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد محصول',
            'description' => 'محصول جدید در وب‌سایت ' . ucfirst($request->website) . 'توسط' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . ' ثبت شد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('محصول مورد نظر با موفقیت ایجاد شد', 'ایجاد محصول');
        return redirect()->route('off-site-products.index', $request->website);
    }

    public function show(OffSiteProduct $offSiteProduct)
    {
        $this->authorize('shops');

        switch ($offSiteProduct->website) {
            case 'torob':
                return $this->torob($offSiteProduct->url);
            case 'emalls':
                return $this->emalls($offSiteProduct->url);
            case 'digikala':
                return $this->digikala($offSiteProduct->url);
            case 'royzkala':
                return $this->royzkala($offSiteProduct->url);
            case 'ariaprint':
                return $this->ariaprint($offSiteProduct->url);
            default:
                return '';
        }
    }

    public function edit(OffSiteProduct $offSiteProduct)
    {
        $this->authorize('shops');

        return view('panel.off-site-products.edit', compact('offSiteProduct'));
    }

    public function update(Request $request, OffSiteProduct $offSiteProduct)
    {
        $this->authorize('shops');

        switch ($offSiteProduct->website) {
            case 'emalls':
                $this->publicUpdate($offSiteProduct, $request);
                break;
            case 'torob':
                $this->publicUpdate($offSiteProduct, $request);
                break;
            case 'digikala':
                $this->digikalaUpdate($offSiteProduct, $request);
                break;
            case 'royzkala':
                $this->publicUpdate($offSiteProduct, $request);
                break;
            case 'ariaprint':
                $this->publicUpdate($offSiteProduct, $request);
                break;
            default:
                return back();
        }
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش محصول',
            'description' => 'محصول با عنوان "' . $offSiteProduct->title . '" در وب‌سایت '  . ucfirst($offSiteProduct->website) . 'توسط' . auth()->user()->family .  '(' . Auth::user()->role->label . ')' . ' ویرایش شد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('محصول مورد نظر با موفقیت ویرایش شد', 'ویرایش محصول');
        return redirect()->route('off-site-products.index', $offSiteProduct->website);
    }

    public function destroy(OffSiteProduct $offSiteProduct)
    {
        $this->authorize('shops');

        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف محصول',
            'description' => 'محصول با عنوان "' . $offSiteProduct->title . '" در وب‌سایت ' . ucfirst($offSiteProduct->website) . 'توسط' . auth()->user()->family  .  '(' . Auth::user()->role->label . ')' . ' حذف شد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $offSiteProduct->delete();
        return back();
    }

    public function priceHistory($website, OffSiteProduct $offSiteProduct)
    {
        switch ($website) {
            case 'torob':
                return $this->torobHistory($offSiteProduct);
            case 'digikala':
                return $this->digikalaHistory($offSiteProduct);
            case 'emalls':
                return $this->emallsHistory($offSiteProduct);
        }
    }

    public function avgPrice($website, OffSiteProduct $offSiteProduct)
    {
        switch ($website) {
            case 'torob':
                return $this->torobAvgPrice($offSiteProduct);
            case 'emalls':
                return $this->emallsAvgPrice($offSiteProduct);
        }
    }

    private function torobAvgPrice($offSiteProduct)
    {
        $pattern = '/\/p\/([^\/]+)/';
        preg_match($pattern, $offSiteProduct->url, $matches);
        $id = $matches[1];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.torob.com/v4/base-product/sellers/?prk=$id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        if ($response == null){
            return redirect()->route('')->with('error','قیمتی برای محصول یافت نشد');
        }
        curl_close($ch);

        $sellers = collect(json_decode($response)->results)->whereNotNull('last_price_change_date')->filter(function ($item) {
            return in_array($item->last_price_change_date, ['دیروز', '۳ روز پیش', '۲ روز پیش']) || strpos($item->last_price_change_date, 'ساعت') || strpos($item->last_price_change_date, 'دقیقه');
        });

        $avg = (int)$sellers->pluck('price')->avg();
        return number_format($avg) . ' تومان ';
    }

    private function emallsAvgPrice($offSiteProduct)
    {
        $ch = curl_init();

        $id = explode('~', $offSiteProduct->url)[2];
        $params = [
            'id' => $id,
            'startfrom' => 0
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://emalls.ir/swservice/webshopproduct.ashx');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = [];
        $headers[] = "Referer: $offSiteProduct->url";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $data = collect(json_decode($response));

        $avg = (int)$data->where('ismojood', true)->filter(function ($item) {
            return in_array($item->lastupdate, ['۱ روز پیش', '۳ روز پیش', '۲ روز پیش']) || strpos($item->lastupdate, 'ساعت') || strpos($item->lastupdate, 'دقیقه');
        })->pluck('Price')->avg();

        return number_format($avg) . ' تومان ';
    }

    private function torob($url)
    {
        $pattern = '/\/p\/([^\/]+)/';
        preg_match($pattern, $url, $matches);
        $id = $matches[1];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.torob.com/v4/base-product/sellers/?prk=$id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        curl_close($ch);
        if (!isset(json_decode($response)->results)){
            return redirect()->back()->with('error','قیمتی برای محصول یافت نشد');
        }
        $data = collect(json_decode($response)->results);


        return view('panel.off-site-products.torob', compact('data'));
    }

    private function digikala($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            // دیگر هدرها در صورت نیاز
        ]);
        $response = curl_exec($ch);
        $res = json_decode($response);
        if ($response === false) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $res = json_decode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo 'JSON decode error: ' . json_last_error_msg();
            }
        }

        curl_close($ch);

        $data = $res->data->product;

        return view('panel.off-site-products.digikala', compact('data'));
    }

    private function emalls($url)
    {
        $ch = curl_init();

        $id = explode('~', $url)[2];
        $params = [
            'id' => $id,
            'startfrom' => 0
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://emalls.ir/swservice/webshopproduct.ashx');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = [];
        $headers[] = "Referer: $url";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $data = collect(json_decode($response));
        $data = $data->where('ismojood', true);

        return view('panel.off-site-products.emalls', compact('data'));
    }

    private function royzkala($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $dom = new DOMDocument();
        $dom->validateOnParse = true;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $response);

        $xpath = new DOMXPath($dom);
        $classname = "variations_form cart";
        $rows = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

        $response = html_entity_decode($rows->item(0)->getAttribute('data-product_variations'));
        $data = collect(json_decode($response));

        return view('panel.off-site-products.royzkala', compact('data'));
    }

    private function ariaprint($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $dom = new DOMDocument();
        $dom->validateOnParse = true;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $response);

        $response = html_entity_decode($dom->getElementsByTagName('script')->item(20)->nodeValue);
        $data = collect(json_decode($response));

        return view('panel.off-site-products.ariaprint', compact('data'));
    }

    private function publicStore($request)
    {
        $request->validate([
            'title' => 'required',
            'url' => 'required',
        ]);
        if ($request->website == 'torob') {
            $pattern = '/\/p\/([^\/]+)/';
            preg_match($pattern, $request->url, $matches);

            if (!str_contains($request->url, 'torob.com')) {
                $request->validate([
                    'error' => 'required',
                ], [
                    'error.required' => 'لینک وارد شده نامعتبر میباشد',
                ]);
            }

            if (!isset($matches[1])) {
                $request->validate([
                    'error' => 'required',
                ], [
                    'error.required' => 'لینک وارد شده نامعتبر میباشد',
                ]);
            }
        }
//            Emalls
        if ($request->website == "emalls") {
            if (!isset(explode('~', $request->url)[2])){
                $request->validate([
                    'error' => 'required',
                ], [
                    'error.required' => 'لینک وارد شده نامعتبر میباشد',
                ]);
            }

            $id = explode('~', $request->url)[2];
            if (!str_contains($request->url, 'emalls.ir')) {
                $request->validate([
                    'error' => 'required',
                ], [
                    'error.required' => 'لینک وارد شده نامعتبر میباشد',
                ]);
            }
        }
        OffSiteProduct::create([
            'title' => $request->title,
            'url' => $request->url,
            'website' => $request->website,
        ]);
    }

    private function publicUpdate(OffSiteProduct $offSiteProduct, $request)
    {
        $request->validate([
            'title' => 'required',
            'url' => 'required',
        ]);

        $offSiteProduct->update([
            'title' => $request->title,
            'url' => $request->url,
        ]);
    }

    private function digikalaStore($request)
    {
        $request->validate([
            'title' => 'required',
            'code' => 'required|numeric',
        ]);


        OffSiteProduct::create([
            'title' => $request->title,
            'url' => "https://api.digikala.com/v2/product/$request->code/",
            'website' => $request->website,
        ]);
    }

    private function digikalaUpdate(OffSiteProduct $offSiteProduct, $request)
    {
        $request->validate([
            'title' => 'required',
            'code' => 'required',
        ]);

        $offSiteProduct->update([
            'title' => $request->title,
            'url' => "https://api.digikala.com/v2/product/$request->code/",
        ]);
    }

    private function torobHistory(OffSiteProduct $offSiteProduct)
    {
        $url = str_replace('https://torob.com/p/', '', $offSiteProduct->url);
        $offset = strpos($url, '/');

        $product_id = substr($url, 0, $offset);

        $endpoint = "https://api.torob.com/v4/base-product/price-chart/?prk=$product_id";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response);

        return response()->json(['data' => $res]);
    }

    private function digikalaHistory(OffSiteProduct $offSiteProduct)
    {
        $product_id = str_replace(['https://api.digikala.com/v2/product/', '/'], '', $offSiteProduct->url);

        $endpoint = "https://api.digikala.com/v1/product/$product_id/price-chart/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response);

        return response()->json(['data' => $res]);
    }

    private function emallsHistory(OffSiteProduct $offSiteProduct)
    {
        $product_id = preg_replace("/[^0-9]/", "", substr($offSiteProduct->url, strpos($offSiteProduct->url, '~')));

        return response()->json(['data' => $product_id]);
    }
}
