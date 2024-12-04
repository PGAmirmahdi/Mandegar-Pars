<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryAnalyseController extends Controller
{
    public function store(Request $request)
    {
        $category = Category::create(['name' => $request->name]);
        return response()->json($category);
    }
}
