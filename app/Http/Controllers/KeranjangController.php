<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class KeranjangController extends Controller
{
    public function get()
    {
        $cart = Keranjang::where('user_id', auth()->id())
            ->with(['item' => function($query) {
                $query->select('id', 'nama', 'harga', 'image', 'stok')
                    ->where('stok', '>', 0);
            }])
            ->orderBy('created_at', 'desc')
            ->select('id', 'item_id', 'warna', 'qty', 'total')
            ->get();
        $hash = Crypt::encrypt(auth()->id() . '|' . now()->timestamp);
        return response()->json([
            'status' => 'success',
            'data' => $cart,
            'hash' => $hash
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'warna' => 'required',
            'qty' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $item = Items::find($request->item_id);

        $isItemExist = Keranjang::where('user_id', auth()->id())
            ->where('item_id', $request->item_id)
            ->where('warna', $request->warna)
            ->first();

        if ($request->qty > $item->stok || $request->qty < 1 || $item->stok < 1 || $request->qty > 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quantity exceeds stock'
            ], 400);
        }

        if ($isItemExist) {
            $item = $isItemExist;
            $item->qty += $request->qty;
            if ($item->qty > $item->item->stok || $item->qty < 1 || $item->item->stok < 1 || $item->qty > 10) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quantity exceeds stock'
                ], 400);
            }
            $item->total = $item->qty * $item->item->harga;
            $item->save();

            $tas = Items::find($request->item_id);
            $tas->stok = $tas->stok - $request->qty;
            $tas->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Item has been added to cart',
            ]);
        }

        $item = Keranjang::create([
            'user_id' => auth()->id(),
            'item_id' => $request->item_id,
            'warna' => $request->warna,
            'qty' => $request->qty,
            'total' => $request->qty * $item->harga
        ]);

        $tas = Items::find($request->item_id);
        $tas->stok = $tas->stok - $request->qty;
        $tas->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item has been added to cart',
        ]);
    }

    public function delete($id)
    {
        $cart = Keranjang::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();
        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        }
        $tas = Items::find($cart->item_id);
        $tas->stok = $tas->stok + $cart->qty;
        $tas->save();

        $cart->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item has been removed from cart'
        ]);
    }

    public function updateQty(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $cart = Keranjang::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();
        
        if ($request->qty > $cart->item->stok || $request->qty < 1 || $cart->item->stok < 1 || $request->qty > 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quantity exceeds stock'
            ], 400);
        }

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        }
        $beforeQty = $cart->qty;
        $cart->qty = $request->qty;
        $cart->total = $cart->item->harga * $request->qty;
        $cart->save();

        $tas = Items::find($cart->item_id);
        $tas->stok = $tas->stok - ($request->qty - $beforeQty);
        $tas->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Item quantity has been updated'
        ]);
    }
}
