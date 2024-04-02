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
                <div class="card-body" id="printArea">
                    <div class="text-center">
                        <h4>Laporan Detail Penyewaan Harian</h4>
                        <h6 class="mb-2 float-left">Pembuat Laporan : {{ Auth()->user()->name }}</h6>
                        <div class="float-right mb-2" id="shTgl">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <h5>Laporan Mobil Disewa</h5>
                        <table id="dt" class="table table-bordered w-100">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">No Kendaraan</th>
                                    <th rowspan="2">Nama Pelanggan</th>
                                    <th rowspan="2">Lama Sewa</th>
                                    <th rowspan="2">Berangkat</th>
                                    <th rowspan="2">Harga Sewa</th>
                                    <th colspan="3">Pembayaran</th>
                                    <th rowspan="2">Kurang</th>
                                </tr>
                                <tr>
                                    <th>DP</th>
                                    <th>Titip</th>
                                    <th>Pelunasan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <hr>
                    <h5>Laporan Mobil Pulang</h5>
                    <div class="table-responsive">
                        <table id="dtB" class="table table-bordered w-100">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">No Kendaraan</th>
                                    <th rowspan="2">Nama Pelanggan</th>
                                    <th rowspan="2">Lama Sewa</th>
                                    <th rowspan="2">Berangkat</th>
                                    <th rowspan="2">Pulang</th>
                                    <th rowspan="2">Harga Sewa</th>
                                    <th colspan="3">Pembayaran</th>
                                    <th rowspan="2">Diskon</th>
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
        $('#shTgl').html('Tanggal : ' + today);

        var numberRenderer = $.fn.dataTable.render.number('.', ',', 0, '').display;

        var dt = $('#dt').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('laporan.harian') }}`,
                data: function(d) {
                    d.param = 'dt';
                    d.tgl = $('#tgl').val();
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
                    data: 'no_kendaraan',
                    name: 'no_kendaraan'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'durasi',
                    name: 'durasi'
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
                    data: 'harga_sewa',
                    name: 'harga_sewa',
                    render: function(data, type, full, meta) {
                        if (full.harga_sewa == null) {
                            return '0';
                        } else {
                            return 'Rp. ' + numberRenderer(full.harga_sewa);
                        }
                    }
                },
                {
                    data: 'dp',
                    name: 'dp',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.dp);
                    }
                },
                {
                    data: 'titip',
                    name: 'titip',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.titip);
                    }

                },
                {
                    data: 'pelunasan',
                    name: 'pelunasan',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.pelunasan);
                    }
                },
                {
                    data: 'kekurangan',
                    name: 'kekurangan',
                    render: function(data, type, full, meta) {
                        var sudah = parseInt(full.dp) + parseInt(full.titip) + parseInt(full.pelunasan)
                        var jumlah = parseInt(full.harga_sewa) - sudah;
                        return 'Rp. ' + numberRenderer(jumlah);
                    }
                },
            ],
            rowCallback: function(row, data) {
                let api = this.api();
                var header = '<table width="100%" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                var body = '';
                var end = '</table>';
                $.ajax({
                    url: `{{ route('laporan.detail') }}`,
                    type: 'GET',
                    data: {
                        id: data.id
                    },
                    success: function(response) {
                        console.log(response);
                        $.each(response.detail, function(key, value) {
                            body += "<tr><td>" + value.tipe + "</td>\
                            <td>" + value.metode + "</td>\
                            <td>" + numberRenderer(value.nominal) + "</td>\
                            <td>" + value.date + "</td>";
                            '</tr>'
                        })
                        console.log(body);
                        api.row(row).child(header + body + end).show();
                    }
                })
                $(row).addClass('shown');
            },
        });

        var dtB = $('#dtB').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('laporan.harian') }}`,
                data: function(d) {
                    d.param = 'dtB';
                    d.tgl = $('#tgl').val();
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
                    data: 'no_kendaraan',
                    name: 'no_kendaraan'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'durasi',
                    name: 'durasi'
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
                    data: 'harga_sewa',
                    name: 'harga_sewa',
                    render: function(data, type, full, meta) {
                        if (full.harga_sewa == null) {
                            return '0';
                        } else {
                            return 'Rp. ' + numberRenderer(full.harga_sewa);
                        }
                    }
                },
                {
                    data: 'dp',
                    name: 'dp'
                },
                {
                    data: 'titip',
                    name: 'titip'
                },
                {
                    data: 'pelunasan',
                    name: 'pelunasan'
                },
                {
                    data: 'diskon',
                    name: 'diskon',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.diskon);
                    }
                },
                {
                    data: 'kekurangan',
                    name: 'kekurangan',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.kekurangan);
                    }
                },
                {
                    data: 'total',
                    name: 'total',
                    render: function(data, type, full, meta) {
                        return 'Rp. ' + numberRenderer(full.keterangan);
                    }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
            ],
            rowCallback: function(row, data) {
                let api = this.api();
                var header = '<table width="100%" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                var body = '';
                var end = '</table>';
                $.ajax({
                    url: `{{ route('laporan.detail') }}`,
                    type: 'GET',
                    data: {
                        id: data.id
                    },
                    success: function(response) {
                        console.log(response);
                        $.each(response.detail, function(key, value) {
                            body += "<tr><td>" + value.tipe + "</td>\
                            <td>" + value.metode + "</td>\
                            <td>" + numberRenderer(value.nominal) + "</td>\
                            <td>" + value.date + "</td>";
                            '</tr>'
                        })
                        console.log(body);
                        api.row(row).child(header + body + end).show();
                    }
                })
                $(row).addClass('shown');
            },
        });

        $('#tgl').on('change', function() {
            $('#shTgl').html('Tanggal : ' + $(this).val());
            dt.draw();
            dtB.draw();
        })

    });
</script>
@endsection