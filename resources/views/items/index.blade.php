@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Items List</h2>
            <a href="{{ route('items.create') }}" class="btn btn-dark mb-3 float-end">Add New Item</a>
            <div class="table-responsive w-100">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Color</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ number_format($item->harga) }}</td>
                            <td><img src="{{ asset($item->image) }}" width="100" alt="{{ $item->nama }}"></td>
                            <td>{{ $item->warna }}</td>
                            <td>{{ $item->stok }}</td>
                            <td>
                                <a href="{{ route('items.show', $item->id) }}" class="btn btn-sm text-dark shadow">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm text-dark shadow">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-dark shadow" onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No Items Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
            <div class="d-flex justify-content-center">
                {{ $items->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
