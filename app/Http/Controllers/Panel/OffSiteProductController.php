<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\OffSiteProduct;
use Illuminate\Http\Request;

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

        $request->validate([
            'title' => 'required',
            'url' => 'required',
        ]);

        OffSiteProduct::create([
            'title' => $request->title,
            'url' => $request->url,
            'website' => $request->website,
        ]);

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

        $request->validate([
            'title' => 'required',
            'url' => 'required',
        ]);

        $offSiteProduct->update([
            'title' => $request->title,
            'url' => $request->url,
        ]);

        alert()->success('محصول مورد نظر با موفقیت ویرایش شد','ویرایش محصول');
        return redirect()->route('off-site-products.index', $request->website);
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
}
