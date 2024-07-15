<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'invoice' => 'nullable|string|exists:transaksi,invoice_number',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $users = User::select('id', 'name')->get();

        $order = Transaksi::with('user')
            ->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->invoice, function ($query) use ($request) {
                return $query->where('invoice_number', $request->invoice);
            })
            ->when($request->start_date, function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->end_date);
            })
            ->orderBy('status', 'asc')
            ->orderBy('is_shipping', 'asc')
            ->orderBy('created_at', 'desc')
            ->whereNot('status', 'PENDING')
            ->paginate(20);
            
        return view('order.index', compact('order', 'users'));
    }

    public function show($id)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        $order = Transaksi::select('id', 'invoice_number', 'buyer', 'items', 'total', 'status', 'is_shipping', 'created_at', 'order_id')
            ->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function makeShipping(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }
        
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:transaksi,order_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        $order = Transaksi::where('order_id', $request->order_id)->first();
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ]);
        }
        if ($order->is_shipping) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order already marked as shipping'
            ]);
        }
        if ($order->status !== 'SUCCESS') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order status must be SUCCESS'
            ]);
        }
        $order->is_shipping = true;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order has been marked as shipping'
        ]);
    }
}
