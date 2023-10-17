<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('categories-list');

        $categories = Category::latest()->paginate(30);
        return view('panel.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('categories-create');

        return view('panel.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('categories-create');

        Category::create([
            'name' => $request->name,
            'slug' => make_slug($request->slug),
        ]);

        alert()->success('دسته بندی مورد نظر با موفقیت ایجاد شد','ایجاد دسته بندی');
        return redirect()->route('categories.index');
    }

    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
        $this->authorize('categories-edit');

        return view('panel.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('categories-edit');

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        alert()->success('دسته بندی مورد نظر با موفقیت ویرایش شد','ویرایش دسته بندی');
        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $this->authorize('categories-delete');

        if (!$category->products()->exists()){
            $category->delete();
            return back();
        }else{
            return response('محصولاتی با این دسته بندی وجود دارند',500);
        }

    }
}
