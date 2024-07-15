<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string',
            'sort' => 'nullable|in:asc,desc'
        ]);

        $products = Items::where('stok', '>', 0)
            ->when($request->search, function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            })
            ->when($request->sort, function ($query) use ($request) {
                $query->orderBy('harga', $request->sort);
            })
            ->paginate(12);
        return view('catalog.catalog', compact('products'));
    }

    public function show($id)
    {
        $product = Items::find($id);
        return view('catalog.detail', compact('product'));
    }

    public function checkStock($id)
    {
        $product = Items::find($id);
        return response()->json([
            'status' => 'success',
            'stock' => $product->stok
        ]);
    }
}
