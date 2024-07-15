@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="text-center">Transaksi</h2>
            <hr>
            <div class="w-100">
                <div class="row justify-content-center">
                    <div class="px-3 py-2 col-md-6">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>{{ date('d F Y', strtotime($transaction['created_at'])) }}</th>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>{!! $transaction['status'] == 'SUCCESS' ? '<span class="badge bg-success">Success</span>' : '<span class="badge bg-danger">Failed</span>' !!}</th>
                                </tr>
                                {{-- status pengiriman --}}
                                <tr>
                                    <th>Status Pengiriman</th>
                                    <th>{!! $transaction['is_shipping']? '<span class="badge bg-success">Shipped</span>' : ($transaction['status'] == 'SUCCESS' ? '<span class="badge bg-warning text-dark">Pending</span>' : '-') !!}</th>
                                <tr>
                                    <th>Invoice</th>
                                    <th>{{ $transaction['invoice_number'] }}</th>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <th>Rp. {{ number_format($transaction['total']) }}</th>
                                </tr>
                            </thead>
                            
                        </table>
                    {{-- </div>
                    <div class="px-3 py-2 col-md-6"> --}}
                        <div class="row mb-2 align-items-center">
                            <h5>Detail Items</h5>
                        </div>
                        @foreach($transaction['items'] as $item)
                        <div class="row mb-2 align-items-center" style="border-bottom: 1px solid #ccc;">
                            <div class="col-3">
                                <img class="img-fluid" src="{{ $item['image'] }}" alt="">
                            </div>
                            <div class="col-4">
                                <h5>{{ $item['name'] }}</h5>
                                <p class="text-muted">{{ $item['warna'] }}</p>
                            </div>
                            <div class="col-5 text-end">
                                <p>Rp. {{ number_format($item['price']) }} x {{ $item['qty'] }}</p>
                            </div>
                        </div>
                        @endforeach

                        {{-- tombol back --}}
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('my-transactions') }}" class="btn btn-dark">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>         
    </div>
</div>
@endsection

