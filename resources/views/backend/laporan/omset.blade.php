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
                <!-- <div class="card-header">
                    <button class="btn btn-success" onclick="printdiv('printArea')">PRINT</button>
                </div> -->
                <div id="printArea">
                    <div class="card-body">
                        <!-- <div class="text-center">
                            <h4>Laporan Omset</h4>
                            <h6 class="mb-2 float-left">Pembuat Laporan : {{ Auth()->user()->name }}</h6>
                        </div> -->
                        <div class="table-responsive">
                            <table id="dt" class="table table-bordered w-100">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <!-- <th rowspan="2">Nama Kosumen</th> -->
                                        <th>No Kendaraan</th>
                                        <!-- <th colspan="2">Tanggal dan Waktu</th> -->
                                        <th>Kredit</th>
                                    </tr>
                                    <!-- <tr>
                                        <th>Tgl Berangkat</th>
                                        <th>Tgl Pulang</th>
                                    </tr> -->
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <!-- <th></th>
                                        <th></th>
                                        <th></th> -->
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
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        var numberRenderer = $.fn.dataTable.render.number('.', ',', 0, '').display;

        var dt = $('#dt').DataTable({
            // "columnDefs": [{
            //     "visible": false,
            //     "targets": [1, 2, 3, 4]
            // }],
            // "drawCallback": function(settings) {
            //     var api = this.api();
            //     var rows = api.rows({
            //         page: 'all'
            //     }).nodes();
            //     var last = '-';
            //     var last_ang = 0;
            //     var last_ang2 = 0;

            //     // Remove the formatting to get integer data for summation
            //     var intVal = function(i) {
            //         return typeof i === 'string' ?
            //             i.replace(/[\$,]/g, '') * 1 :
            //             typeof i === 'number' ?
            //             i : 0;
            //     };

            //     var array = [];
            //     api.column(2, {
            //         page: 'all'
            //     }).data().each(function(group, i) {

            //         group_assoc = group.replace(/\s+/g, "_");

            //         if (group !== last) {
            //             $(rows).eq(i).before(
            //                 '<tr class="group"><td colspan=""><span style="font-weight:bold">' + group + '</span></td><td class="' + group_assoc + '"></td></tr>'
            //             );
            //             last = group;
            //         }

            //         array[i] = {
            //             title: group_assoc,
            //             nominal: intVal(api.column(5).data()[i])
            //         };
            //     });

            //     var result = [];
            //     array.reduce(function(res, value) {
            //         if (!res[value.title]) {
            //             res[value.title] = {
            //                 Id: value.title,
            //                 nominal: 0
            //             };
            //             result.push(res[value.title])
            //         }
            //         res[value.title].nominal += value.nominal;
            //         return res;
            //     }, {});

            //     $.each(result, function(index, value) {
            //         console.log(value)
            //         $("." + value.Id).html("<span style='font-weight:bold'>Rp. " + numberRenderer(value.nominal) + "</span>");
            //     });
            // },
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
                title: function() {
                    var judul = '<h6> Laporan Omset <br> Pembuat Laporan : {{ Auth()->user()->name }} ( Periode : ' + $('#tAwal').val() + ' s/d ' + $('#tAhir').val() + ')<h6>';
                    return judul;
                }
            }],
            ajax: {
                url: `{{ route('laporan.omset') }}`,
                data: function(d) {
                    d.tAwal = $('#tAwal').val();
                    d.tAhir = $('#tAhir').val();
                }
            },
            columns: [{
                    data: 'biaya',
                    name: 'biaya',
                    render: function(data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                // {
                //     data: 'nama',
                //     name: 'nama'
                // },
                {
                    data: 'no_kendaraan',
                    name: 'no_kendaraan'
                },
                // {
                //     data: 'id',
                //     name: 'id',
                //     render: function(data, type, full, meta) {
                //         return full.keberangkatan + " " + full.keberangkatan_time;
                //     }
                // },
                // {
                //     data: 'id',
                //     name: 'id',
                //     render: function(data, type, full, meta) {
                //         return full.kepulangan + " " + full.kepulangan_time;
                //     }
                // },
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
                    .column(2)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(2, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(2).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotal);
            }
        });

        $('#tAwal, #tAhir').on('change', function() {
            dt.draw();
        })

    });
</script>
@endsection