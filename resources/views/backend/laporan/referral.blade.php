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
                    <input type="month" class="form-control" name="bulan" id="bulan">
                </div>
                <div class="col-6">
                    <select id="select2Referral" style="width: 100% !important;" name="id_referral">
                    </select>
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
                                    <th colspan="2">Tanggal dan Waktu</th>
                                    <th rowspan="2">Kredit</th>
                                    <th rowspan="2">Komisi</th>
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
    $(document).ready(function() {
        $('#select2Referral').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Referral').parent(),
            placeholder: "Cari Referral",
            allowClear: true,
            ajax: {
                url: "{{ route('referral.select2') }}",
                dataType: "json",
                cache: true,
                data: function(e) {
                    return {
                        q: e.term || '',
                        page: e.page || 1
                    }
                },
            },
        });
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
                className: 'green glyphicon glyphicon-print',
                text: 'Print',
                title: ' ',
                orientation: 'landscape'
            }],
            ajax: {
                url: `{{ route('laporan.referral') }}`,
                data: function(d) {
                    d.bulan = $('#bulan').val();
                    d.referral = $('#select2Referral').val();
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
                    data: 'keberangkatan',
                    name: 'keberangkatan'
                },
                {
                    data: 'kepulangan',
                    name: 'kepulangan'
                },
                {
                    data: 'biaya',
                    name: 'biaya'
                },
                {
                    data: 'komisi',
                    name: 'komisi',
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
                    .column(4)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over all pages
                totalKomisi = api
                    .column(5)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotalKomisi = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(4).footer().innerHTML =
                    'Rp. ' + pageTotal;

                // Update footer
                api.column(5).footer().innerHTML =
                    'Rp. ' + pageTotalKomisi;
            }
        });

        $('#select2Referral').on('change', function() {
            dt.draw();
        });

        $('#bulan').on('change', function() {
            dt.draw();
        })

    });
</script>
@endsection