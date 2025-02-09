<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDO;
use Illuminate\Support\Facades\Http;

class ArtinController extends Controller
{
    public function products(Request $request)
    {
        $this->authorize('artin-products-list');

        $page = $request->input('page', 1);
        $response = Http::get('https://artintoner.com/wp-json/custom/v1/products/', [
            'page' => $page
        ]);

        if ($response->successful()) {
            $products = collect($response->json())->map(function ($item) {
                return (object)$item;
            })->all();
        } else {
            return abort(500, 'خطا در دریافت اطلاعات از API');
        }

        return view('panel.artin.products', compact(['products', 'page']));
    }

    public function updatePrice(Request $request)
    {
        $this->authorize('artin-products-edit');

        $request->validate([
            'product_id' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $response = Http::post('https://artintoner.com/wp-json/custom/v1/update-price/', [
            'product_id' => $request->input('product_id'),
            'price' => $request->input('price')
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'خطا در بروزرسانی قیمت'], 500);
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $this->authorize('artin-products-create');

        $request->validate([
            'title' => 'required|string',
            'sku' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|string|in:publish,draft',
            'code_accounting' => 'nullable|string',
        ]);

        $response = Http::post('https://artintoner.com/wp-json/custom/v1/create-product/', [
            'title' => $request->input('title'),
            'sku' => $request->input('sku'),
            'price' => $request->input('price'),
            'status' => $request->input('status'),
            'code_accounting' => $request->input('code_accounting'),
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'خطا در ایجاد محصول'], 500);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->authorize('artin-products-delete');

        $response = Http::delete("https://artintoner.com/wp-json/custom/v1/delete-product/{$id}");

        if (!$response->successful()) {
            return response()->json(['error' => 'خطا در حذف محصول'], 500);
        }

        return redirect()->back();
    }

}
