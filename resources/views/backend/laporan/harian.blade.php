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
                            <h6 class="mb-2 float-left">Pembuat Laporan : {{ Auth()->user()->name }}</h6>
                            <div class="float-right mb-2" id="shTgl">
                            </div>
                        </div>
                        <br>
                        <hr>
                        <h6>Laporan Mobil Di Sewa</h6>
                        <div class="table-responsive">
                            <table id="tbSewa" class="table table-bordered w-100">
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
                                        <th>Titip</th>
                                        <th>Pelunasan</th>
                                    </tr>
                                </thead>
                                <tbody id="isi">
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <h6>Laporan Mobil Pulang</h6>
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
                                        <th>Titip</th>
                                        <th>Pelunasan</th>
                                    </tr>
                                </thead>
                                <tbody id="isi1">
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <h6>Laporan Uang Masuk</h6>
                        <div class="table-responsive">
                            <table id="dt2" class="table table-bordered w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Nama Pelanggan</th>
                                        <th rowspan="2">Tipe</th>
                                        <th rowspan="2">Bukti</th>
                                        <th rowspan="2">Ket.</th>
                                        <th colspan="2">Nominal</th>
                                    </tr>
                                    <tr>
                                        <th>Cash</th>
                                        <th>Transfer</th>
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

        var numberRenderer = $.fn.dataTable.render.number('.', ',', 0, '').display;

        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear() + "-" + (month) + "-" + (day);

        $('#tgl').val(today);

        function load_table() {
            var tglA = $('#tgl').val();
            $('#tgl').prop('disabled', true);
            $('#shTgl').append('Tanggal : ' + tglA);
            $.ajax({
                url: `{{ route('laporan.harian') }}`,
                type: 'GET',
                data: {
                    tgl: tglA
                },
                success: function(response) {
                    $.each(response.data, function(key, value) {
                        let idT = value.id;
                        let lama_sewa = (value.lama_sewa == null) ? ('') : (value.lama_sewa);
                        let kepulangan = (value.kepulangan == null) ? ('') : (value.kepulangan);
                        let kepulangan_time = (value.kepulangan_time == null) ? ('') : (value.kepulangan_time);
                        let keberangkatan = (value.keberangkatan == null) ? ('') : (value.keberangkatan);
                        let keberangkatan_time = (value.keberangkatan_time == null) ? ('') : (value.keberangkatan_time);
                        let total = (value.total == null) ? (0) : (value.total);
                        let kekurangan = (value.kekurangan == null) ? (0) : (value.kekurangan);
                        let keterangan = (value.keterangan == null) ? ('') : (value.keterangan);
                        let dp = (value.dp == null) ? (0) : (value.dp);
                        let titip = (value.titip == null) ? (0) : (value.titip);
                        let pelunasan = (value.pelunasan == null) ? (0) : (value.pelunasan);
                        let paket = (value.paket == 'jam') ? ('Jam') : ('Hari');
                        $('#isi').append("<tr id='" + value.id + "' class='th'>\
                                            <td>" + (key + 1) + "</td>\
                                            <td>" + value.no_kendaraan + "</td>\
                                            <td>" + value.nama + "</td>\
                                            <td>" + lama_sewa + ' ' + paket + "</td>\
                                            <td>" + keberangkatan + " " + keberangkatan_time + "</td>\
                                            <td>" + kepulangan + " " + kepulangan_time + "</td>\
                                            <td>" + numberRenderer(dp) + "</td>\
                                            <td>" + numberRenderer(titip) + "</td>\
                                            <td>" + numberRenderer(pelunasan) + "</td>\
                                            <td>" + numberRenderer(kekurangan) + "</td>\
                                            <td>" + numberRenderer(total) + "</td>\
                                            <td>" + keterangan + "</td>\
                                            </tr>");
                        $.ajax({
                            url: `{{ route('laporan.detail') }}`,
                            type: 'GET',
                            data: {
                                id: idT
                            },
                            success: function(response) {
                                $.each(response.detail, function(key, value) {
                                    $('table#tbSewa tr#' + value.id_transaksi + '').after("<tr class='th'>\
                                    <td colspan='3'>" + value.tipe + "</td>\
                                    <td colspan='3'>" + value.metode + "</td>\
                                    <td colspan='3'>" + numberRenderer(value.nominal) + "</td>\
                                    <td colspan='3'>" + value.date + "</td>\
                                   </tr>");
                                })
                            }
                        })
                    });

                    $.each(response.data1, function(key, value) {
                        let idT = value.id;
                        let lama_sewa = (value.lama_sewa == null) ? ('') : (value.lama_sewa);
                        let kepulangan = (value.kepulangan == null) ? ('') : (value.kepulangan);
                        let kepulangan_time = (value.kepulangan_time == null) ? ('') : (value.kepulangan_time);
                        let keberangkatan = (value.keberangkatan == null) ? ('') : (value.keberangkatan);
                        let keberangkatan_time = (value.keberangkatan_time == null) ? ('') : (value.keberangkatan_time);
                        let total = (value.total == null) ? (0) : (value.total);
                        let kekurangan = (value.kekurangan == null) ? (0) : (value.kekurangan);
                        let keterangan = (value.keterangan == null) ? ('') : (value.keterangan);
                        let dp = (value.dp == null) ? (0) : (value.dp);
                        let titip = (value.titip == null) ? (0) : (value.titip);
                        let pelunasan = (value.pelunasan == null) ? (0) : (value.pelunasan);
                        let paket = (value.paket == 'jam') ? ('Jam') : ('Hari');
                        $('#isi1').append("<tr id='" + value.id + "' class='th'>\
                                            <td>" + (key + 1) + "</td>\
                                            <td>" + value.no_kendaraan + "</td>\
                                            <td>" + value.nama + "</td>\
                                            <td>" + lama_sewa + ' ' + paket + "</td>\
                                            <td>" + keberangkatan + " " + keberangkatan_time + "</td>\
                                            <td>" + kepulangan + " " + kepulangan_time + "</td>\
                                            <td>" + numberRenderer(dp) + "</td>\
                                            <td>" + numberRenderer(titip) + "</td>\
                                            <td>" + numberRenderer(pelunasan) + "</td>\
                                            <td>" + numberRenderer(kekurangan) + "</td>\
                                            <td>" + numberRenderer(total) + "</td>\
                                            <td>" + keterangan + "</td>\
                                            </tr>");
                        $.ajax({
                            url: `{{ route('laporan.detail') }}`,
                            type: 'GET',
                            data: {
                                id: idT
                            },
                            success: function(response) {
                                $.each(response.detail, function(key, value) {
                                    $('table#dt tr#' + value.id_transaksi + '').after("<tr class='th'>\
                                    <td colspan='3'>" + value.tipe + "</td>\
                                    <td colspan='3'>" + value.metode + "</td>\
                                    <td colspan='3'>" + numberRenderer(value.nominal) + "</td>\
                                    <td colspan='3'>" + value.date + "</td>\
                                   </tr>");
                                })
                            }
                        });
                    });

                    var totalDP = 0;
                    var totalTrf = 0;
                    $.each(response.data2, function(key, value) {
                        let bukti = (value.file == null) ? ('') : (value.file);
                        let dp = (value.metode == 'cash') ? (value.nominal) : (0);
                        let trf = (value.metode == 'transfer') ? (value.nominal) : (0);
                        $('#isi2').append("<tr class='th'>\
                                            <td>" + (key + 1) + "</td>\
                                            <td>" + value.nama + "</td>\
                                            <td>" + value.tipe + "</td>\
                                            <td><a href ='{{ asset ('storage/buktiTrf')}}/" + bukti + "' target='_blank'>" + bukti + "</a></td>\
                                            <td>" + value.detail + "</td>\
                                            <td>" + numberRenderer(dp) + "</td>\
                                            <td>" + numberRenderer(trf) + "</td>\
                                            </tr></div>");
                        totalDP += dp;
                        totalTrf += trf;
                    });
                    $('#foot2').append("<tr>\
                    <td colspan='5' class='text-center'><b> Total </b></td>\
                    <td>Rp. " + numberRenderer(totalDP) + "</td>\
                    <td>Rp. " + numberRenderer(totalTrf) + "</td>\
                    </tr>")
                },
                complete: function() {
                    $('#tgl').prop('disabled', false);
                }
            })
        }

        load_table();

        $('#tgl').on('change', function() {
            $('#isi').empty();
            $('#isi1').empty();
            $('#isi2').empty();
            $('#foot2').empty();
            $('#shTgl').empty();
            load_table();
        })

    });
</script>
@endsection