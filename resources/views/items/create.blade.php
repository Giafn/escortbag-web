@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Add New Item</h2>
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="form-group mb-3">
                    <label for="harga">Harga</label>
                    <input type="number" step="0.01" class="form-control" id="harga" name="harga" required>
                </div>
                <div class="form-group mb-3">
                    <label for="image">Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="warna">Warna</label>
                    <small class="d-block">
                        <i>Contoh: Red, Blue, Green, etc.</i>
                    </small>
                    <input type="text" class="form-control" id="warna" name="warna" required>
                </div>
                <div class="form-group mb-3">
                    <label for="stok">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" required>
                </div>
                <button type="submit" class="btn btn-dark">Save</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
        $('#description').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']],
            ]
        });
    });
</script>
@endpush
