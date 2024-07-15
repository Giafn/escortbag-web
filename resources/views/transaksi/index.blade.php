@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="text-center">Transaksi</h2>
            <hr>
            <div class="row">
                @forelse($transactions as $transaction)
                <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="d-block">Invoice: {{ $transaction->invoice_number }}</small>
                        <small class="d-block">Total: Rp. {{ number_format($transaction->total) }}</small>
                        <small class="d-block">Status: {!! $transaction->status == 'SUCCESS' ? '<span class="badge bg-success">Success</span>' : '<span class="badge bg-danger">Failed</span>' !!}</small>
                    </div>
                    <a href="/transaction/{{ $transaction->id }}" class="btn btn-dark">Detail</a>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-center">No transactions found</p>
                </div>
                @endforelse
            </div>
            {{-- paginasi --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->links() }}
            </div>
        </div>         
    </div>
</div>
@endsection

