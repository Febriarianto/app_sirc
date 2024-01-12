@extends('layouts.master')

@section('content')
<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pilih Tanggal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <input type="date" class="form-control" name="tgl" id="tgl">
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
                <div id="printArea">
                    <div class="card-body">
                        <div class="text-center">
                            <h4>Laporan Harian</h4>
                            <h6 class="mb-2">Pembuat Laporan : {{ Auth()->user()->name }}</h6>
                        </div>
                        <div class="table-responsive">
                            <table id="dt" class="table table-bordered w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">No Kendaraan</th>
                                        <th rowspan="2">Nama Pelanggan</th>
                                        <th rowspan="2">Lama Sewa</th>
                                        <th rowspan="2">Berangkat</th>
                                        <th rowspan="2">Pulang</th>
                                        <th colspan="3">Pembayaran</th>
                                        <th rowspan="2">Kurang</th>
                                        <th rowspan="2">Total</th>
                                        <th rowspan="2">Ket.</th>
                                    </tr>
                                    <tr>
                                        <th>DP</th>
                                        <th>Transfer</th>
                                        <th>Cash</th>
                                    </tr>
                                </thead>
                                <tbody id="isi">
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="dt2" class="table table-bordered w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Tipe</th>
                                        <th>Metode</th>
                                        <th>Ket.</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody id="isi2">
                                </tbody>
                                <tfoot id="foot2">
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

        $('#tgl').val(today);

        // var dt = $('#dt').DataTable({
        //     searching: false,
        //     paging: false,
        //     info: false,
        //     responsive: true,
        //     serverSide: true,
        //     processing: true,
        //     dom: 'Bfrtip',
        //     buttons: [{
        //         extend: 'print',
        //         footer: true,
        //         text: 'Print',
        //         title: 'Laporan Harian ',
        //         customize: function(win) {

        //             var last = null;
        //             var current = null;
        //             var bod = [];

        //             var css = '@page { size: landscape; }',
        //                 head = win.document.head || win.document.getElementsByTagName('head')[0],
        //                 style = win.document.createElement('style');

        //             style.type = 'text/css';
        //             style.media = 'print';

        //             if (style.styleSheet) {
        //                 style.styleSheet.cssText = css;
        //             } else {
        //                 style.appendChild(win.document.createTextNode(css));
        //             }
        //             head.appendChild(style);
        //         }
        //     }],
        //     ajax: {
        //         url: `{{ route('laporan.harian') }}`,
        //         data: function(d) {
        //             d.tgl = $('#tgl').val();
        //         }
        //     },
        //     columns: [{
        //             data: 'id',
        //             name: 'id',
        //             render: function(data, type, full, meta) {
        //                 return meta.row + 1;
        //             }
        //         },
        //         {
        //             data: 'kendaraan',
        //             name: 'kendaraan'
        //         },
        //         {
        //             data: 'penyewa',
        //             name: 'penyewa'
        //         },
        //         {
        //             data: 'lama_sewa',
        //             name: 'lama_sewa',
        //             render: function(data, type, full, meta) {
        //                 return full.lama_sewa;
        //             }
        //         },
        //         {
        //             data: 'id',
        //             name: 'id',
        //             render: function(data, type, full, meta) {
        //                 if (full.keberangkatan == null && full.keberangkatan_time == null) {
        //                     return "-"
        //                 } else if (full.keberangkatan_time == null) {
        //                     return full.keberangkatan
        //                 } else {
        //                     return full.keberangkatan + " " + full.keberangkatan_time;
        //                 }
        //             }
        //         },
        //         {
        //             data: 'id',
        //             name: 'id',
        //             render: function(data, type, full, meta) {
        //                 if (full.kepulangan == null && full.kepulangan_time == null) {
        //                     return "-"
        //                 } else if (full.kepulangan_time == null) {
        //                     return full.kepulangan
        //                 } else {
        //                     return full.kepulangan + " " + full.kepulangan_time;
        //                 }
        //             }
        //         },
        //         {
        //             data: 'id',
        //             name: 'id',
        //             render: function(data, type, full, meta) {
        //                 if (full.tipe == "dp") {
        //                     return full.nominal
        //                 } else {
        //                     return "-";
        //                 }
        //             }
        //         },
        //         {
        //             data: 'id',
        //             name: 'id',
        //             render: function(data, type, full, meta) {
        //                 if (full.metode == 'transfer' && full.tipe == "pelunasan") {
        //                     return full.nominal
        //                 } else {
        //                     return "-";
        //                 }
        //             }
        //         },
        //         {
        //             data: 'id',
        //             name: 'id',
        //             render: function(data, type, full, meta) {
        //                 if (full.metode == 'cash' && full.tipe == "pelunasan") {
        //                     return full.nominal
        //                 } else {
        //                     return "-";
        //                 }
        //             }
        //         },
        //         {
        //             data: 'keterangan',
        //             name: 'keterangan',
        //         },
        //         {
        //             data: 'penerima',
        //             name: 'penerima',
        //         },

        //     ],
        // });


        function load_table() {
            var tglA = $('#tgl').val();
            $.ajax({
                url: `{{ route('laporan.harian') }}`,
                type: 'GET',
                data: {
                    tgl: tglA
                },
                success: function(response) {
                    $.each(response.data1, function(key, value) {
                        $('#isi').append("<tr class='th'>\
                                            <td>" + (key + 1) + "</td>\
                                            <td>" + value.no_kendaraan + "</td>\
                                            <td>" + value.nama + "</td>\
                                            <td>" + value.lama_sewa + "</td>\
                                            <td>" + value.keberangkatan + " " + value.keberangkatan_time + "</td>\
                                            <td>" + value.kepulangan + " " + value.kepulangan_time + "</td>\
                                            <td>" + value.dp + "</td>\
                                            <td>" + value.transfer + "</td>\
                                            <td>" + value.cash + "</td>\
                                            <td>" + value.kekurangan + "</td>\
                                            <td>" + value.total + "</td>\
                                            <td>" + value.keterangan + "</td>\
                                            </tr></div>");
                    });

                    var total = 0;
                    $.each(response.data2, function(key, value) {
                        $('#isi2').append("<tr class='th'>\
                                            <td>" + (key + 1) + "</td>\
                                            <td>" + value.tipe + "</td>\
                                            <td>" + value.metode + "</td>\
                                            <td>" + value.detail + "</td>\
                                            <td>" + value.nominal + "</td>\
                                            </tr></div>");
                        total += value.nominal;
                    });
                    $('#foot2').append("<tr>\
                    <td colspan='4'> Total </td>\
                    <td>" + total + "</td>\
                    </tr>")
                }
            })
        }

        load_table();

        $('#tgl').on('change', function() {
            $('#isi').empty();
            $('#isi2').empty();
            $('#foot2').empty();
            load_table();
        })

    });
</script>
@endsection