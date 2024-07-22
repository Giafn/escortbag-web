@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Item Details</h2>
            <div class="card bg-white">
                <div class="card-body row align-items-center">
                    <div class="col-md-6">
                        @if ($item->image)
                        <img src="{{ asset($item->image) }}" class="img-fluid" alt="{{ $item->nama }}">
                        @else
                        <img src="https://via.placeholder.com/300" class="img-fluid" alt="Placeholder">
                        @endif
                    </div>
                    <div class="col-md-6 py-3">
                        <h5 class="title">{{ $item->nama }}</h5>
                        <p class="text">Price: {{ $item->harga }}</p>
                        <hr>
                        <p class="text">Description:</p>
                        <div style="max-height: 300px; overflow-y: auto;"> 
                            @php
                                $item->description = str_replace("\n", "<br>", $item->description);
                            @endphp
                            {!! $item->description !!}
                        </div>
                        <hr>
                        <p class="text">Color: {{ $item->warna }}</p>
                        <p class="text">Stock: {{ $item->stok }}</p>
                        <a href="{{ route('items.index') }}" class="btn btn-dark mt-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
