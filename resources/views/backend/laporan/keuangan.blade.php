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
                    <input type="date" class="form-control" name="tAwal" id="tAwal">
                </div>
                <div class="col-6">
                    <input type="date" class="form-control" name="tAhir" id="tAhir">
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
                <div class="card-body">
                    <div id="printArea">
                        <div class="text-center">
                            <h4>Laporan Keuangan</h4>
                            <h6 class="mb-2 float-left">Pembuat Laporan : {{ Auth()->user()->name }}</h6>
                            <div class="float-right mb-2" id="shTgl">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <h5>Uang Masuk</h5>
                            <table id="dt" class="table table-bordered w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Nama Pelanggan</th>
                                        <th rowspan="2">Tipe</th>
                                        <th rowspan="2">Bukti</th>
                                        <th rowspan="2">Ket.</th>
                                        <th colspan="2">Pemasukan</th>
                                        <th rowspan="2">Tanggal</th>
                                    </tr>
                                    <tr>
                                        <th>Cash</th>
                                        <th>Transfer</th>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:right" colspan="5">Total:</th>
                                        <th colspan="2">
                                            <div id="totil"></div>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr>
                        <h5>Uang Keluar</h5>
                        <div class="table-responsive">
                            <table id="dtp" class="table table-bordered w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Ket.</th>
                                        <th>Bukti</th>
                                        <th>Pengeluaran</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align:right" colspan="3">Total:</th>
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

        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);
        $('#tAwal').val(today);
        $('#tAhir').val(today);
        $('#shTgl').html('Tanggal : ' + today + ' s/d ' + today);

        var numberRenderer = $.fn.dataTable.render.number('.', ',', 0, '').display;

        var dt = $('#dt').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('laporan.keuangan') }}`,
                data: function(d) {
                    d.param = 'dt';
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
                    data: 'tipe',
                    name: 'tipe'
                },
                {
                    data: 'file',
                    name: 'file',
                    render: function(data, type, full, meta) {
                        if (full.file !== null) {
                            return "<a href='{{ asset ('storage/buktiTrf')}}/" + full.file + "' target='_blank'>" + full.file + "</a>";
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'detail',
                    name: 'detail'
                },
                {
                    data: 'pc',
                    name: 'pc'
                },
                {
                    data: 'pf',
                    name: 'pf'
                },
                {
                    data: 'tgl',
                    name: 'tgl'
                }
            ],
            rowCallback: function(row, data) {
                let api = this.api();
            },
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                console.log(api.table().footer())
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

                // Total over all pages
                totalKomisi = api
                    .column(6)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotalKomisi = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(5).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotal);

                // Update footer
                api.column(6).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotalKomisi);


                var totalSeluruh = pageTotal + pageTotalKomisi;
                $('#totil').html('Rp. ' + numberRenderer(totalSeluruh));
            }
        });

        var dtp = $('#dtp').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('laporan.keuangan') }}`,
                data: function(d) {
                    d.param = 'dtp';
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
                    data: 'detail',
                    name: 'detail'
                },
                {
                    data: 'file',
                    name: 'file',
                    render: function(data, type, full, meta) {
                        if (full.file !== null) {
                            return "<a href='{{ asset ('storage/buktiTrf')}}/" + full.file + "' target='_blank'>" + full.file + "</a>";
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'pc',
                    name: 'pc'
                },
                {
                    data: 'tgl',
                    name: 'tgl'
                }
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
                    .column(3)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(3).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotal);
            }
        });


        $('#tAwal, #tAhir').on('change', function() {
            $('#shTgl').html('Tanggal : ' + $('#tAwal').val() + ' s/d ' + $('#tAhir').val());
            dt.draw();
            dtp.draw();
        })

    });
</script>
@endsection