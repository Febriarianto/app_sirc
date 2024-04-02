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
                <div class="card-header">
                    <button class="btn btn-success" onclick="printdiv('printArea')">PRINT</button>
                </div>
                <div class="card-body" id="printArea">
                    <div class="text-center">
                        <h4>Laporan Invoice Belum Terbayar</h4>
                        <h6 class="mb-2 float-left">Pembuat Laporan : {{ Auth()->user()->name }}</h6>
                        <div class="float-right mb-2" id="shTgl">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Nama Pelanggan</th>
                                    <th rowspan="2">No Kendaraan</th>
                                    <th rowspan="2">Lama Sewa</th>
                                    <th colspan="2">Tanggal dan Waktu</th>
                                    <th rowspan="2">Belum Terbayar</th>
                                    <th rowspan="2">Total</th>
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
                                    <th></th>
                                    <th style="text-align:right">Total:</th>
                                    <th></th>
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
    function printdiv(elem) {
        var header_str = '<html><head><style>@page{size: A4 landscape;}body{font-size:10px; line-height:8px;}</style><title>' + document.title + '</title></head><body>';
        var footer_str = '</body></html>';
        var new_str = document.getElementById(elem).innerHTML;
        var old_str = document.body.innerHTML;
        document.body.innerHTML = header_str + new_str + footer_str;
        window.print();
        window.location.reload();
        // document.body.innerHTML = old_str;
        return false;
    }
</script>
<script>
    $(document).ready(function() {
        var numberRenderer = $.fn.dataTable.render.number('.', ',', 0, '').display;
        var groupColumn = 1;

        var dt = $('#dt').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            // dom: 'Bfrtip',
            order: [
                [groupColumn, 'asc']
            ],
            columnDefs: [{
                visible: false,
                targets: groupColumn
            }],
            buttons: [{
                extend: 'print',
                footer: true,
                text: 'Print',
                title: 'Laporan Referal',
            }],
            ajax: {
                url: `{{ route('laporan.belumLunas') }}`,
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
                    data: 'durasi',
                    name: 'durasi'
                },
                {
                    data: 'keberangkatan',
                    name: 'keberangkatan'
                },
                {
                    data: 'kepulangan',
                    name: 'kepulangan'
                },
                {
                    data: 'sisa',
                    name: 'sisa',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.sisa);
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
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;

                api.column(groupColumn, {
                        page: 'current'
                    })
                    .data()
                    .each(function(group, i) {
                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before(
                                    '<tr class="group"><td colspan="8">' +
                                    group +
                                    '</td></tr>'
                                );

                            last = group;
                        }
                    });
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
                    .column(6)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over all pages
                totalKomisi = api
                    .column(7)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotalKomisi = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(6).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotal);

                // Update footer
                api.column(7).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotalKomisi);
            }
        });

        $('#tAwal, #tAhir').on('change', function() {
            var tA = $('#tAwal').val();
            var tH = $('#tAhir').val();
            $('#shTgl').html('Tanggal : ' + tA + ' s/d ' + tH);
            dt.draw();
        })

    });
</script>
@endsection