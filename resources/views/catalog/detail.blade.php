@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ $product->image }}" class="img-fluid" alt="">
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center">
                    <h1>{{ $product->nama }}</h1>
                    <p>Rp. {{ number_format($product->harga) }}</p>
                    <p>
                        stok: <span id="stockItem">{{ $product->stok }}</span><br>
                    </p>
                    <hr>
                    <h5>Deskripsi</h5>
                    <div class="desc" style="max-height: 300px; overflow-y: auto;">
                        <p>{!! $product->description !!}</p>
                    </div>
                    <form action="" method="post">
                        @csrf
                        <div class="d-flex mb-3 gap-2">
                            <div>
                                <label for="warna" class="form-label">Warna</label>
                                <select name="warna" id="warna" class="form-select" style="width: 200px;">
                                    @php
                                        $warna = explode(',', $product->warna);
                                    @endphp
                                    @foreach($warna as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="width: 100px;">
                                <label for="qty" class="form-label">Jumlah</label>
                                <input type="number" name="qty" id="qty" class="form-control" value="1" min="1">
                            </div>
                        </div>
                        <input type="hidden" name="item_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-dark w-100">Add to cart</button>
                    </form>
                    {{-- back --}}
                    <a href="{{ route('catalog') }}" class="btn btn-secondary mt-3">Back</a>
                </div>
            </div>
        </div>         
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '/cart',
                method: 'POST',
                data: formData,
                success: function(data) {
                    toastr.success('Item added to cart');
                    cekStock();
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.message);
                }
            });
        });

        function cekStock() {
            $.ajax({
                url: '/stock/{{ $product->id }}',
                method: 'GET',
                success: function(data) {
                    $('#stockItem').text(data.stock);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching stock data:', error);
                }
            });
        }

        $(document).on('change', '.jumlah-input', async function() {
            cekStock();
        });

        $(document).on('keyup', '.jumlah-input', async function() {
            cekStock();
        });


        $(document).on('click', '.remove-item', async function() {
            cekStock();
        });
    });
</script>
@endpush
