<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductModelRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductModelController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('productsModel-list');
        $query = ProductModel::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('slug', 'LIKE', "%{$search}%");
        }

        $productsModel = $query->paginate(10);
        return view('panel.productsModel.index', compact('productsModel'));
    }

    public function create()
    {
        $this->authorize('productsModel-create');
        $categories = Category::all(); // برای انتخاب دسته‌بندی
        return view('panel.productsModel.create', compact('categories'));
    }

    public function store(StoreProductModelRequest $request)
    {
        $this->authorize('productsModel-create');

        $productModelExist = ProductModel::where(['category_id' => $request->category_id, 'slug' => make_slug($request->slug)])->exists();

        if ($productModelExist){
            $request->validate(['exist.required']);

            return back()->withInput($request->all())->withErrors(['exist' => 'اسلاگ وارد شده در دسته بندی مورد نظر موجود می باشد']);
        }

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

    public function update(StoreProductModelRequest $request, ProductModel $productsModel)
    {
        $this->authorize('productsModel-edit');

        $productModelExist = ProductModel::where(['category_id' => $request->category_id, 'slug' => make_slug($request->slug)])->where('id','!=',$productsModel->id)->exists();

        if ($productModelExist){
            $request->validate(['exist.required']);

            return back()->withInput($request->all())->withErrors(['exist' => 'اسلاگ وارد شده در دسته بندی مورد نظر موجود می باشد']);
        }

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
