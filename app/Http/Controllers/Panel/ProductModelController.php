<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductModelController extends Controller
{
    public function index()
    {
        $this->authorize('productsModel-list');

        $productsModel = ProductModel::latest()->paginate(30);
        return view('panel.productsModel.index', compact('productsModel'));
    }

    public function create()
    {
        $this->authorize('productsModel-create');
        $categories = Category::all(); // برای انتخاب دسته‌بندی
        return view('panel.productsModel.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('productsModel-create');
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'category_id' => 'required'
        ]);

        ProductModel::create([
            'name' => $request->name,
            'slug' => make_slug($request->slug),
            'category_id' => $request->category_id,
        ]);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد مدل',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') مدل ' . $request->name . ' را ایجاد کرد.',
        ]);
        alert()->success('مدل با موفقیت ایجاد شد','ایجاد مدل');
        return redirect()->route('productsModel.index');
    }

    public function edit(ProductModel $productsModel)
    {
        $this->authorize('productsModel-edit');
        $categories = Category::all();
        return view('panel.productsModel.edit', compact('productsModel', 'categories'));
    }

    public function update(Request $request, ProductModel $productsModel)
    {
        $this->authorize('productsModel-edit');

        $productsModel->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
        ]);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش مدل',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') مدل ' . $request->name . ' را ویرایش کرد.',
        ]);
        alert()->success('مدل با موفقیت ویرایش شد','ویرایش مدل');
        return redirect()->route('productsModel.index');
    }

    public function destroy(ProductModel $productsModel)
    {
        $this->authorize('productsModel-delete');
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف مدل',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') مدل ' . $productsModel->name . ' را حذف کرد.',
        ]);
        $productsModel->delete();
        return back();
    }
}
