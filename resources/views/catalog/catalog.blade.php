@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="text-center">Catalog</h2>
            <hr>
            {{-- search --}}
            <form action="{{ route('catalog') }}" method="GET">
                <div class="d-flex  mb-3 gap-2">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search product" name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
                    </div>
                    <div class="ms-auto d-none d-md-block">
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>
                    {{-- reset --}}
                    <a href="{{ route('catalog') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
            <div class="row">
                @forelse($products as $product)
                <div class="col-md-3 col-6 pop-hover">
                    <a class="p-2 text-center d-block text-decoration-none text-dark" href="{{ route('catalog.show', $product->id) }}">
                        <img src="{{ $product->image }}" class="card-img-top mb-2" alt="">
                        <p class="fs-4">{{ $product->nama }}</p>
                        <p class="fs-5">Rp. {{ number_format($product->harga) }}</p>
                    </a>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-center">No products found</p>
                </div>
                @endforelse
            </div>
            {{-- paginasi --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>         
    </div>
</div>
@endsection

