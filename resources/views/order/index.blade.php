@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Pesanan</h2>
            <form action="/order" method="GET" class="mb-3">
                <div class="row g-3 w-100">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" onchange="this.form.submit()" onclick="this.showPicker()">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" onchange="this.form.submit()" onclick="this.showPicker()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="select">
                            <select name="user_id" class="form-control" onchange="this.form.submit()">
                                <option value="" disabled selected>pilih user</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex gap-2">
                        <div class="input-group">
                            <input type="text" name="invoice" class="form-control" placeholder="Search by Invoice Number">
                            <button type="submit" class="btn btn-dark">Search</button>
                        </div>
                        {{-- reset --}}
                        <a href="/order" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                No
                            </th>
                            <th>Invoice</th>
                            <th>Order Id</th>
                            <th>User</th>
                            <th>Payment Status</th>
                            <th>Status pengiriman</th>
                            <th>Tanggal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order as $item)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>{{ $item->invoice_number }}</td>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->is_shipping ? 'Dikirim' : ($item->status != "SUCCESS" ? '-' : 'Belum Dikirim') }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                            <td>
                                <a href="" class="btn btn-sm text-dark shadow modal-show" data-id="{{ $item->id }}">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
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
            @if ($order->hasPages())
            <div class="d-flex justify-content-center">
                {{ $order->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- modal show --}}
<div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Data Pesanan</h4>
                <div class="w-100 px-3">
                    <table class="table table-borderless">
                        <tr>
                            <td>Invoice</td>
                            <td>:</td>
                            <td id="detailInvoice"></td>
                        </tr>
                        <tr>
                            <td>Order ID</td>
                            <td>:</td>
                            <td id="detailOrderId"></td>
                        </tr>
                        <tr>
                            <td>User</td>
                            <td>:</td>
                            <td id="detailNama"></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td id="detailAlamat"></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td>:</td>
                            <td id="detailTelepon"></td>
                        </tr>
                        <tr>
                            <td>Payment Status</td>
                            <td>:</td>
                            <td id="detailPaymentStatus"></td>
                        </tr>
                        <tr>
                            <td>Status Pengiriman</td>
                            <td>:</td>
                            <td id="detailStatusPengiriman"></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td id="detailCreated"></td>
                        </tr>
                        {{-- total harga --}}
                        <tr>
                            <td>Total Harga</td>
                            <td>:</td>
                            <td class="fw-bold">Rp. <span id="detailTotalHarga"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="mt-2 table-responsive px-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>nama</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="detailItems">
                            
                        </tbody>
                    </table>
                </div>
                {{-- tandai dikirim --}}
                <div class="px-3 mt-3">
                    <form action="/order/action" method="POST" id="formDetailTandai">
                        @csrf
                        <input type="hidden" name="order_id" id="detail_order_id">
                        <button type="submit" class="btn btn-dark d-none" id="detailBtnTandai">Tandai Sebagai Dikirim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $('.modal-show').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.ajax({
            url: `/order/${id}`,
            success: function(response) {
                console.log(response);
                const data = response.data;

                $('#detailInvoice').text(data.invoice_number);
                $('#detailOrderId').text(data.order_id);
                $('#detail_order_id').val(data.order_id);
                $('#detailNama').text(data.buyer.name);
                $('#detailAlamat').text(data.buyer.address);
                $('#detailTelepon').text(data.buyer.phone);
                $('#detailPaymentStatus').text(data.status);
                $('#detailStatusPengiriman').text(data.is_shipping == 1 ? 'Dikirim' : (data.status != "SUCCESS" ? '-' : 'Belum Dikirim'));
                $('#detailCreated').text(dateFormat(data.created_at));
                $('#detailTotalHarga').text(formatRupiah(data.total));

                if (data.is_shipping && data.status == "SUCCESS" || data.status != "SUCCESS") {
                    $('#detailBtnTandai').addClass('d-none');
                } else {
                    $('#detailBtnTandai').removeClass('d-none');
                }

                const items = data.items.map((item, index) => {
                    return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name} (warna : ${item.warna})</td>
                        <td>${item.qty}</td>
                        <td>Rp. ${formatRupiah(item.price)}</td>
                    </tr>
                    `;
                });

                $('#detailItems').html(items);

                $('#showModal').modal('show');
            }
        });
    });

    $('#detailBtnTandai').click(function(e) {
        e.preventDefault();
        
        let formData = $('#formDetailTandai').serialize();
        $.ajax({
            url: '/make-shipping',
            method: 'POST',
            data: formData,
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                toastr.error('Error marking as shipped');
                console.error('Error marking as shipped:', error);
            }
        });
    });

    function dateFormat(date) {
        const d = new Date(date);
        return `${d.getDate()}-${d.getMonth() + 1}-${d.getFullYear()}`;
    }

    function formatRupiah(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }
</script>
@endpush
