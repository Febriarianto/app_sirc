@extends('layouts.master')

@section('content')
<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pilih Tanggal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <input type="date" id="tAwal" class="form-control">
                </div>
                <div class="col-6">
                    <input type="date" id="tAhir" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Nama Kosumen</th>
                                    <th rowspan="2">No Kendaraan</th>
                                    <th colspan="2">Tanggal dan Waktu</th>
                                    <th rowspan="2">Kredit</th>
                                </tr>
                                <tr>
                                    <th>Tgl Berangkat</th>
                                    <th>Tgl Pulang</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right">Total:</th>
                                    <th></th>
                                </tr>
                            </tfoot>
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

        var numberRenderer = $.fn.dataTable.render.number('.', ',', 0, '').display;

        var dt = $('#dt').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'print',
                footer: true,
                text: 'Print',
                title: '<h6> Laporan Omset <br> Pembuat Laporan : {{ Auth()->user()->name }} <h6>',
            }],
            ajax: {
                url: `{{ route('laporan.omset') }}`,
                data: function(d) {
                    d.tAwal = $('#tAwal').val();
                    d.tAhir = $('#tAhir').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'no_kendaraan',
                    name: 'no_kendaraan'
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        return full.keberangkatan + " " + full.keberangkatan_time;
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        return full.kepulangan + " " + full.kepulangan_time;
                    }
                },
                {
                    data: 'biaya',
                    name: 'biaya',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.biaya);
                    }
                },
            ],
            rowCallback: function(row, data) {
                let api = this.api();
            },
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();

                // Remove the formatting to get integer data for summation
                let intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                // Total over all pages
                total = api
                    .column(5)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(5).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotal);
            }
        });

        $('#tAwal, #tAhir').on('change', function() {
            dt.draw();
        })

    });
</script>
@endsection