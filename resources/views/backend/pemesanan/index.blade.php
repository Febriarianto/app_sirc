@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <div class="header-title">
                        <div class="row">
                            <div class="col-sm-6 col-lg-6">
                                <h4 class="card-title">Data Pemesanan</h4>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <a href="{{ route('pemesanan.create') }}" class="btn btn-primary float-right">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Nama Pemesan</th>
                                    <th>Alamat</th>
                                    <th>No Hp</th>
                                    <th>Tanggal Berangkat</th>
                                    <th>NO Kendaraan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#dt').DataTable({

            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('pemesanan.index') }}`
            },
            columns: [{
                    data: 'penyewa.nama',
                    name: 'penyewa.nama'
                },
                {
                    data: 'penyewa.alamat',
                    name: 'penyewa.alamat'
                },
                {
                    data: 'penyewa.no_hp',
                    name: 'penyewa.no_hp'
                },
                {
                    data: 'keberangkatan',
                    name: 'keberangkatan'
                },
                {
                    data: 'kendaraan.no_kendaraan',
                    name: 'kendaraan.no_kendaraan'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
            ],
            rowCallback: function(row, data) {
                let api = this.api();
                $(row).find('.btn-delete').click(function() {
                    let pk = $(this).data('id'),
                        url = `{{ route("pemesanan.index") }}/` + pk;
                    Swal.fire({
                        title: "Anda Yakin ?",
                        text: "Data tidak dapat dikembalikan setelah di hapus!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Tidak, Batalkan",
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                error: function(response) {
                                    toastr.error(response, 'Failed !');
                                },
                                success: function(response) {
                                    if (response.status === "success") {
                                        toastr.success(response.message, 'Success !');
                                        api.draw();
                                    } else {
                                        toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
                                    }
                                }
                            });
                        }
                    });
                });
            }
        });
    });
</script>
@endsection