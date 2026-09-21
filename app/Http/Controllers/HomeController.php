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

        $categoriesQuery = Category::active()
            ->whereHas('products', function ($q) {
                $q->where('status', 'active');
            })
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }]);

        if ($type !== 'all') {
            $categoriesQuery->where('type', $type);
        }

        $categories = $categoriesQuery->orderBy('name', 'asc')->get();

        return view('pages.home', compact('categories', 'type'));
    }
}
