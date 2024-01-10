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
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">No Kendaraan</th>
                                    <th rowspan="2">Nama Pelanggan</th>
                                    <th rowspan="2">Lama Sewa</th>
                                    <th rowspan="2">Keberangkatan</th>
                                    <th rowspan="2">Kepulangan</th>
                                    <th colspan="3">Pembayaran</th>
                                    <th rowspan="2">Keterangan</th>
                                    <th rowspan="2">Penerima</th>
                                </tr>
                                <tr>
                                    <th>DP</th>
                                    <th>Transfer</th>
                                    <th>Cash</th>
                                </tr>
                            </thead>
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

        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear() + "-" + (month) + "-" + (day);

        $('#tgl').val(today);

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
                title: 'Laporan Harian ',
                customize: function(win) {

                    var last = null;
                    var current = null;
                    var bod = [];

                    var css = '@page { size: landscape; }',
                        head = win.document.head || win.document.getElementsByTagName('head')[0],
                        style = win.document.createElement('style');

                    style.type = 'text/css';
                    style.media = 'print';

                    if (style.styleSheet) {
                        style.styleSheet.cssText = css;
                    } else {
                        style.appendChild(win.document.createTextNode(css));
                    }
                    head.appendChild(style);
                }
            }],
            ajax: {
                url: `{{ route('laporan.harian') }}`,
                data: function(d) {
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
                    data: 'kendaraan',
                    name: 'kendaraan'
                },
                {
                    data: 'penyewa',
                    name: 'penyewa'
                },
                {
                    data: 'lama_sewa',
                    name: 'lama_sewa',
                    render: function(data, type, full, meta) {
                        return full.lama_sewa;
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.keberangkatan == null && full.keberangkatan_time == null) {
                            return "-"
                        } else if (full.keberangkatan_time == null) {
                            return full.keberangkatan
                        } else {
                            return full.keberangkatan + " " + full.keberangkatan_time;
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.kepulangan == null && full.kepulangan_time == null) {
                            return "-"
                        } else if (full.kepulangan_time == null) {
                            return full.kepulangan
                        } else {
                            return full.kepulangan + " " + full.kepulangan_time;
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.tipe == "dp") {
                            return full.nominal
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.metode == 'transfer' && full.tipe == "pelunasan") {
                            return full.nominal
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.metode == 'cash' && full.tipe == "pelunasan") {
                            return full.nominal
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                },
                {
                    data: 'penerima',
                    name: 'penerima',
                },

            ],
        });

        $('#tgl').on('change', function() {
            dt.draw();
        })

    });
</script>
@endsection