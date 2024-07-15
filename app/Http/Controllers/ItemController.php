<?php

namespace App\Http\Controllers;

use App\Models\Items as Item;
use App\Models\Keranjang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index()
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        $items = Item::orderBy('created_at', 'desc')->paginate(20);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        return view('items.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'warna' => 'required|string',
            'stok' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('items-image', $imageName, 'public');
            $path = '/storage/' . $path;
        }

        Item::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'image' => $path,
            'description' => $request->description,
            'warna' => $request->warna,
            'stok' => $request->stok
        ]);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function show(Item $item)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'warna' => 'required|string',
            'stok' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        // cek apakah ada kerajang yang menggunakan item yang akan diupdate
        $cart = Keranjang::where('item_id', $item->id)->get();
        // cek warna item yang akan diupdate
        $warna = [];
        foreach ($cart as $c) {
            $warna[] = $c->warna;
        }
        
        $warnaRequest = explode(',', $request->warna);
        
        // cek apakah warna yang ada di keranjang ada di item yang akan diupdate
        foreach ($warna as $w) {
            if (!in_array($w, $warnaRequest)) {
                return redirect()->back()->with('error', 'haraus ada warna ' . $w . ' di item yang akan diupdate');
            }
        }

        $path = $item->image;
        if ($request->hasFile('image')) {
            if ($path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $path));
            }
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('items-image', $imageName, 'public');
            $path = '/storage/' . $path;
        }

        $item->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'image' => $path,
            'description' => $request->description,
            'warna' => $request->warna,
            'stok' => $request->stok
        ]);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }
        // cek pada transaksi di kolom items(json) apakah ada item yang akan dihapus
        $transactions = Transaksi::whereJsonContains('items', ['id' => $item->id])->get();
        if ($transactions->count() > 0) {
            return redirect()->route('items.index')->with('error', 'Item cannot be deleted because it is used in transactions.');
        }

        // cek pada keranjang apakah ada item yang akan dihapus
        $carts = Keranjang::where('item_id', $item->id)->get();
        if ($carts->count() > 0) {
            return redirect()->route('items.index')->with('error', 'Item cannot be deleted because it is used in carts.');
        }

        if ($item->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $item->image));
        }

        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
