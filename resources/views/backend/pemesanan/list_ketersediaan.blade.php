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
                                <h4 class="card-title">Data Ketersediaan Kendaraan</h4>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No Kendaraan</th>
                                    <th>Warna</th>
                                    <th>Jenis</th>
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
        $('#dt').DataTable({

            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('kendaraan.status') }}`
            },
            columns: [{
                    data: 'no_kendaraan',
                    name: 'no_kendaraan'
                },
                {
                    name: 'warna',
                    data: 'warna'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.s != 0) {
                            return '<span class="badge badge-danger">Tidak Tersedia</span>';
                        } else {
                            return '<span class="badge badge-success">Tersedia</span>';
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
        });
    });
</script>
@endsection