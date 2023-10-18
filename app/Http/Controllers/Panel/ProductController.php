<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize('products-list');

        $products = Product::latest()->paginate(30);
        return view('panel.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('products-create');

        $categories = Category::all();
        return view('panel.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('products-create');
        $image = upload_file($request->image, 'Products');

        // product properties
        $properties = $this->json_properties($request);
        $total_count = array_sum($request->counts);

        // create product
        $product = Product::create([
            'title' => $request->title,
//            'slug' => make_slug($request->slug),
            'code' => $request->code,
            'image' => $image,
            'category_id' => $request->category,
            'properties' => $properties,
            'description' => $request->description,
            'system_price' => $request->system_price,
            'partner_price_tehran' => $request->partner_price_tehran,
            'partner_price_other' => $request->partner_price_other,
            'single_price' => $request->single_price,
            'creator_id' => auth()->id(),
            'total_count' => $total_count,
        ]);

        if ($request->compatible_printers){
            $product->printers()->sync($request->compatible_printers);
        }

        alert()->success('محصول مورد نظر با موفقیت ایجاد شد','ایجاد محصول');
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        $this->authorize('products-edit');

        return view('panel.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('products-edit');
        if ($request->has('image'))
        {
            $image = upload_file($request->image, 'Products');
        }

        // product properties
        $properties = $this->json_properties($request);
        $total_count = array_sum($request->counts);

        // create product
        $product->update([
            'title' => $request->title,
//            'slug' => make_slug($request->slug),
            'code' => $request->code,
            'image' => $image ?? $product->image,
            'category_id' => $request->category,
            'properties' => $properties,
            'description' => $request->description,
            'system_price' => $request->system_price,
            'partner_price_tehran' => $request->partner_price_tehran,
            'partner_price_other' => $request->partner_price_other,
            'single_price' => $request->single_price,
            'creator_id' => auth()->id(),
            'total_count' => $total_count,
        ]);

        if ($request->compatible_printers){
            $product->printers()->sync($request->compatible_printers);
        }else{
            $product->printers()->detach();
        }

        alert()->success('محصول مورد نظر با موفقیت ویرایش شد','ویرایش محصول');
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $this->authorize('products-delete');

        if ($product->invoices()->exists()){
            return response('این محصول در پیش فاکتور هایی موجود است',500);
        }

        $product->delete();
        return back();
    }

    private function json_properties($request){
        $items = [];
        foreach ($request->colors as $key => $color){
            $items[] = [
                'color' => $color,
                'print_count' => $request->print_count[$key],
                'counts' => $request->counts[$key],
            ];
        }
        return json_encode($items);
    }
}
