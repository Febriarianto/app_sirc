@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Tanggal</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <input type="date" id="tAwal" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="date" id="tAhir" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header justify-content-between">
                    <div class="header-title">
                        <div class="row">
                            <div class="col-sm-6 col-lg-6">
                                <h4 class="card-title">Data Invoice</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No Inv.</th>
                                    <th>Nama Penyewa</th>
                                    <th>Alamat</th>
                                    <th>No Hp</th>
                                    <th>Berangkat</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
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
        $('#tAwal, #tAhir').on('change', function() {
            dt.draw();
        })

        var dt = $('#dt').DataTable({
            aLengthMenu: [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "All"]
            ],
            responsive: true,
            serverSide: true,
            processing: true,
            dom: "<'row'<'col-sm-2'l><'col-sm-2'B><'col-sm-8'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>",
            buttons: [{
                extend: 'print',
                footer: true,
                text: 'Print',
                title: function() {
                    var judul = '<h6> Cetak Invoice <br> Pembuat Laporan : {{ Auth()->user()->name }} ( Periode : ' + $('#tAwal').val() + ' s/d ' + $('#tAhir').val() + ')<h6>';
                    return judul;
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }],
            order: [
                [5, 'desc']
            ],
            ajax: {
                url: `{{ route('invoice.index') }}`,
                data: function(d) {
                    d.tAwal = $('#tAwal').val();
                    d.tAhir = $('#tAhir').val();
                }
            },
            columns: [{
                    data: 'no_inv',
                    name: 'no_inv',
                },
                {
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
                    name: 'keberangkatan',
                    render: function(data, type, full, meta) {
                        if (full.tanggal !== null) {
                            return full.keberangkatan + " " + full.keberangkatan_time
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'kepulangan',
                    name: 'kepulangan',
                    render: function(data, type, full, meta) {
                        if (full.tanggal !== null) {
                            return full.kepulangan + " " + full.kepulangan_time
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                    render: function(data, type, full, meta) {
                        if (full.keterangan == 'lunas') {
                            return '<span class="badge badge-success">' + full.keterangan + '</span>';
                        } else {
                            return '<span class="badge badge-warning">' + full.keterangan + '</span>';
                        }
                    }
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
                        url = `{{ route("invoice.index") }}/` + pk;
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