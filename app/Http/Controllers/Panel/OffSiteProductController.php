<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\OffSiteProduct;
use Illuminate\Http\Request;
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

        switch ($request->website)
        {
            case 'torob' || 'emalls':
                $this->publicStore($request);
                break;
            case 'digikala':
                $this->digikalaStore($request);
                break;
            default:
                return back();
        }

        alert()->success('محصول مورد نظر با موفقیت ایجاد شد','ایجاد محصول');
        return redirect()->route('off-site-products.index', $request->website);
    }

    public function show(OffSiteProduct $offSiteProduct)
    {
        $this->authorize('shops');

        switch ($offSiteProduct->website)
        {
            case 'torob':
                return $this->torob($offSiteProduct->url);
            case 'emalls':
                return $this->emalls($offSiteProduct->url);
            case 'digikala':
                return $this->digikala($offSiteProduct->url);
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

        switch ($offSiteProduct->website)
        {
            case 'torob' || 'emalls':
                $this->publicUpdate($offSiteProduct ,$request);
                break;
            case 'digikala':
                $this->digikalaUpdate($offSiteProduct ,$request);
                break;
            default:
                return back();
        }

        alert()->success('محصول مورد نظر با موفقیت ویرایش شد','ویرایش محصول');
        return redirect()->route('off-site-products.index', $offSiteProduct->website);
    }

    public function destroy(OffSiteProduct $offSiteProduct)
    {
        $this->authorize('shops');

        $offSiteProduct->delete();
        return back();
    }

    private function torob($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        curl_close($ch);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->validateOnParse = true;
        $dom->loadHTML($response);
        libxml_clear_errors();

        $res = json_decode($dom->getElementsByTagName('script')->item(0)->nodeValue);
        $data = $res->offers->offers;

        return view('panel.off-site-products.torob', compact('data'));
    }

    private function digikala($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response);
        $data = $res->data->product;

        return view('panel.off-site-products.digikala', compact('data'));
    }

    private function emalls($url)
    {
        $ch = curl_init();

        $id = explode('~',$url)[2];
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

    private function publicStore($request)
    {
        $request->validate([
            'title' => 'required',
            'url' => 'required',
        ]);

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
            'url' => "https://api.digikala.com/v1/product/$request->code/",
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
            'url' => "https://api.digikala.com/v1/product/$request->code/",
        ]);
    }
}
