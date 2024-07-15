<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Keranjang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('services.midtrans.serverKey');
        MidtransConfig::$isProduction = config('services.midtrans.isProduction');
        MidtransConfig::$isSanitized  = config('services.midtrans.isSanitized');
        MidtransConfig::$is3ds        = config('services.midtrans.is3ds');
    }

    public function checkout($hash)
    {
        try {
            $hash = Crypt::decrypt($hash);
        } catch (\Exception $e) {
            return redirect()->route('catalog');
        }

        $hash = explode('|', $hash);
        $user_id = $hash[0];
        $time = $hash[1];
        if ($time < now()->subMinutes(60)->timestamp) {
            return redirect()->route('catalog');
        }
        if ($user_id != auth()->id()) {
            return redirect()->route('catalog')->with('error', 'Unauthorized');
        }
        $cart = Keranjang::where('user_id', auth()->id())
            ->with(['item' => function($query) {
                $query->select('id', 'nama', 'harga', 'image', 'stok')
                    ->where('stok', '>', 0);
            }])
            ->orderBy('created_at', 'desc')
            ->select('id', 'item_id', 'warna', 'qty', 'total')
            ->get();

        if ($cart->isEmpty()) {
            return redirect()->route('catalog')->with('error', 'Cart is empty');
        }

        return view('cart.checkout', compact('cart'));
    }

    public function pay(Request $request, $hash)
    {
        try {
            $hash = Crypt::decrypt($hash);
        } catch (\Exception $e) {
            return redirect()->route('catalog');
        }

        $hash = explode('|', $hash);
        $user_id = $hash[0];
        $time = $hash[1];
        if ($time < now()->subMinutes(60)->timestamp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Time expired'
            ], 400);
        }
        if ($user_id != auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone' => 'required|string|numeric|min:9999999999',
            'address' => 'required|string|min:20|max:255',
        ]);

        $validator->setCustomMessages([
            'phone.min' => 'the :attribute must be at least 10 characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $items = Keranjang::where('user_id', auth()->id())
            ->with(['item' => function($query) {
                $query->select('id', 'nama', 'harga', 'image', 'stok')
                    ->where('stok', '>', 0);
            }])
            ->orderBy('created_at', 'desc')
            ->select('id', 'item_id', 'warna', 'qty', 'total')
            ->get();
        if ($items->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty'
            ], 400);
        }
        DB::transaction(function() use ($items, $request) {
            // create uuid
            $orderId = Str::uuid();
            $transaksi = Transaksi::create([
                'user_id' => auth()->id(),
                'invoice_number' => 'INV/' . now()->timestamp,
                'order_id' => $orderId,
                'items' => $items->map(function($item) {
                    return [
                        'id' => $item->item->id,
                        'name' => $item->item->nama,
                        'warna' => $item->warna,
                        'price' => $item->item->harga,
                        'qty' => $item->qty,
                        'total' => $item->total,
                    ];
                }),
                'buyer' => [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ],
                'total' => $items->sum('total'),
                'cart_ids' => implode(',', $items->pluck('id')->toArray()),
            ]);

            // Buat transaksi ke midtrans kemudian save snap tokennya.
            $payload = [
                'transaction_details' => [
                    'order_id'      => $orderId,
                    'gross_amount'  => $items->sum('total'),
                ],
                'customer_details' => [
                    'first_name'    => $request->name,
                    'email'         => auth()->user()->email,
                    'phone'         => $request->phone,
                    'address'       => $request->address,
                ],
                'item_details' => $items->map(function($item) {
                    return [
                        'id' => $item->item->id,
                        'price' => $item->item->harga,
                        'quantity' => $item->qty,
                        'name' => $item->item->nama,
                    ];
                }),
            ];
            
            $snapToken = Snap::getSnapToken($payload);
            $transaksi->snap_token = $snapToken;
            $transaksi->save();
            
            // Beri response snap token
            $this->response['snap_token'] = $snapToken;
        });

        return response()->json($this->response);
    }

    public function handle(Request $request, $hash)
    {
        try {
            $hash = Crypt::decrypt($hash);
        } catch (\Exception $e) {
            return redirect()->route('catalog');
        }

        $hash = explode('|', $hash);
        $user_id = $hash[0];
        $time = $hash[1];
        if ($time < now()->subMinutes(60)->timestamp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Time expired'
            ], 400);
        }
        if ($user_id != auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $request = $request->all();

        // cek transaction status ke midtrans
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.sandbox.midtrans.com/v2/' . $request['order_id'] . '/status', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(config('services.midtrans.serverKey') . ':')
            ]
        ]);

        $payload = json_decode($response->getBody()->getContents(), true);

        $transaction = Transaksi::where('order_id', $payload['order_id'])->first();
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }
        $orderId = $transaction->order_id;
        $hash = Crypt::encrypt($orderId . '|' . now()->timestamp);
        $success = false;
        if ($payload['transaction_status'] == 'capture' || $payload['transaction_status'] == 'settlement') {
            $transaction->setSuccess();
            $success = true;
        } else if ($payload['transaction_status'] == 'expire') { 
            $transaction->setFailed(true);
        } else {
            $transaction->setFailed();
        }

        if ($success) {
            $transaction->deleteCart();
            return response()->json([
                'status' => 'success',
                'hash' => $hash,
                'message' => 'Transaction status payment ' . $payload['transaction_status']
            ]);
        }

        return response()->json([
            'status' => 'error',
            'hash' => $hash,
            'message' => 'Transaction status payment ' . $payload['transaction_status']
        ]);
        
    }

    public function paymentPage($type, $hash)
    {
        try {
            $hash = Crypt::decrypt($hash);
        } catch (\Exception $e) {
            return redirect()->route('catalog');
        }

        $hash = explode('|', $hash);
        $orderId = $hash[0];
        $time = $hash[1];
        if ($time < now()->subMinutes(2)->timestamp) {
            return redirect()->route('catalog');
        }

        $transaction = Transaksi::where('order_id', $orderId)->first();
        if (!$transaction) {
            return redirect()->route('catalog');
        }
        $invoice = $transaction->invoice_number;
        if ($transaction->status == 'SUCCESS') {
            return view('payment.success', compact('invoice'));
        } else if ($transaction->status == 'FAILED') {
            return view('payment.failed', compact('invoice'));
        }
    }

    // my transaction
    public function myTransaction()
    {
        $transactions = Transaksi::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->where('status', '!=', 'PENDING')
            ->select('id', 'invoice_number', 'total', 'status', 'created_at', 'is_shipping')
            ->paginate(10);
        return view('transaksi.index', compact('transactions'));
    }

    public function detail($id)
    {
        $transaction = Transaksi::where('user_id', auth()->id())
            ->where('transaksi.id', $id)
            ->select('transaksi.id', 'invoice_number', 'transaksi.total', 'status', 'items', 'transaksi.created_at', "is_shipping")
            ->first();

        $items = collect($transaction->items);
        $items = $items->map(function($item) {
            $itemDB = Items::find($item['id']);
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'warna' => array_key_exists('warna', $item) ? $item['warna'] : '-',
                'price' => $item['price'],
                'qty' => $item['qty'],
                'total' => $item['total'],
                'image' => $itemDB->image,
            ];
        });
        $transaction = collect($transaction);
        $transaction = $transaction->merge(['items' => $items->toArray()]);
        if (!$transaction) {
            return redirect()->route('my-transactions');
        }
        return view('transaksi.detail', compact('transaction'));
    }

    public function pesanan()
    {
        $transactions = Transaksi::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->where('status', 'PENDING')
            ->select('id', 'invoice_number', 'total', 'status', 'created_at')
            ->paginate(10);
        return view('transaksi.pesanan', compact('transactions'));
    }
}
