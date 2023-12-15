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
                                    <th rowspan="2">Total Sewa</th>
                                    <th rowspan="2">Keberangkatan</th>
                                    <th rowspan="2">Kepulangan</th>
                                    <th colspan="3">Pembayaran</th>
                                    <th rowspan="2">Keterangan</th>
                                    <th rowspan="2">Total</th>
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
                        return data + " " + full.paket;
                    }
                },
                {
                    data: 'keberangkatan',
                    name: 'keberangkatan'
                },
                {
                    data: 'kepulangan',
                    name: 'kepulangan',
                },
                {
                    data: 'dp',
                    name: 'dp',
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.metode_pelunasan == 'transfer') {
                            return full.sisa
                        } else {
                            return "-";
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        if (full.metode_pelunasan == 'cash') {
                            return full.sisa
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
                    data: 'total',
                    name: 'total',
                },

            ],
        });

        $('#tgl').on('change', function() {
            dt.draw();
        })

    });
</script>
@endsection