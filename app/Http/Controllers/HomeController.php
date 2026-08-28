<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Halaman Utama Katalog Game GameNexa
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');

        $categoriesQuery = Category::active()->has('products')->withCount('products');

        if ($type !== 'all') {
            $categoriesQuery->where('type', $type);
        }

        $categories = $categoriesQuery->get();

        return view('pages.home', compact('categories', 'type'));
    }
}
