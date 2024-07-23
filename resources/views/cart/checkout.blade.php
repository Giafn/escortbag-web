@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="text-center">Checkout</h2>
            <hr>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h4>Shipping Address</h4>
                    <form action="" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-dark d-md-block d-none">Checkout</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h4>Cart</h4>
                    <div class="w-100">
                        <div class="d-flex">
                            <div class="px-3 py-2 w-100">
                                @foreach($cart as $item)
                                <div class="row mb-2 align-items-center">
                                    <div class="col-3">
                                        <img class="img-fluid" src="{{ $item->item->image }}" alt="">
                                    </div>
                                    <div class="col-4">
                                        <h5>{{ $item->item->nama }}</h5>
                                        <p class="text-muted">{{ $item->warna }}</p>
                                    </div>
                                    <div class="col-5 text-end">
                                        <p>Rp. {{ number_format($item->item->harga) }} x {{ $item->qty }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        @php
                            $subtotal = $cart->reduce(function($acc, $item) {
                                return $acc + ($item->total);
                            }, 0);
                        @endphp
                        <h5>subtotal</h5>
                        <h5>Rp {{ number_format($subtotal) }}</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Shipping</h5>
                        <h5>Free</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold">Total</h5>
                        <h5 class="fw-bold">Rp {{ number_format($subtotal) }}</h5>
                    </div>
                    <button class="btn btn-dark d-block d-md-none" onclick="document.querySelector('form').submit()">Checkout</button>
                </div>
            </div>
            
        </div>         
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
<script>
    $('form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/checkout/' + '{{ request()->segment(2) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('success',result);
                        // return;
                        result._token = '{{ csrf_token() }}';
                        $.ajax({
                            url: '/handle/' + '{{ request()->segment(2) }}',
                            method: 'POST',
                            data: result,
                            success: function(data) {
                                console.log(data);
                                if (data.status == 'success') {
                                    $('#checkoutBtn').prop('disabled', true);
                                    toastr.success('Payment successful');
                                    setTimeout(() => {
                                        window.location.href = '/payment/success/' + data.hash;
                                    }, 3000);
                                } else {
                                    $('#checkoutBtn').prop('disabled', true);
                                    toastr.error('Payment failed');
                                    setTimeout(() => {
                                        window.location.href = '/payment/failed/' + data.hash;
                                    }, 3000);
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#checkoutBtn').prop('disabled', true);
                                toastr.error('Error:', error);
                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 3000);
                            }
                        });
                    },
                    onPending: function(result) {
                        console.log('pending', result);
                        // return;
                        result._token = '{{ csrf_token() }}';
                        $.ajax({
                            url: '/handle/' + '{{ request()->segment(2) }}',
                            method: 'POST',
                            data: result,
                            success: function(data) {
                                console.log(data);
                                if (data.status == 'success') {
                                    $('#checkoutBtn').prop('disabled', true);
                                    toastr.success('Payment successful');
                                    setTimeout(() => {
                                        window.location.href = '/payment/success/' + data.hash;
                                    }, 3000);
                                } else {
                                    $('#checkoutBtn').prop('disabled', true);
                                    toastr.error('Payment failed');
                                    setTimeout(() => {
                                        window.location.href = '/payment/failed/' + data.hash;
                                    }, 3000);
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#checkoutBtn').prop('disabled', true);
                                toastr.error('Error:', error);
                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 3000);
                            }
                        });
                    },
                    onError: function(result) {
                        console.log('err', result);
                        // return;
                        result._token = '{{ csrf_token() }}';
                        $.ajax({
                            url: '/handle/' + '{{ request()->segment(2) }}',
                            method: 'POST',
                            data: result,
                            success: function(data) {
                                console.log(data);
                                if (data.status == 'success') {
                                    $('#checkoutBtn').prop('disabled', true);
                                    toastr.success('Payment successful');
                                    setTimeout(() => {
                                        window.location.href = '/payment/success/' + data.hash;
                                    }, 3000);
                                } else {
                                    $('#checkoutBtn').prop('disabled', true);
                                    toastr.error('Payment failed');
                                    setTimeout(() => {
                                        window.location.href = '/payment/failed/' + data.hash;
                                    }, 3000);
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#checkoutBtn').prop('disabled', true);
                                toastr.error('Error:', error);
                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 3000);
                            }
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseJSON);
                toastr.error(xhr.responseJSON.message);
            }
        });
    });
</script>
@endpush

