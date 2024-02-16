@extends('layouts.master')

@section('content')
<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pilih Tanggal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <input type="date" id="tAwal" class="form-control">
                </div>
                <div class="col-3">
                    <input type="date" id="tAhir" class="form-control">
                </div>
                <div class="col-6">
                    <select id="select2Kendaraan" style="width: 100% !important;" name="id_kendaraan">
                    </select>
                    <input type="hidden" id="jenis">
                    <input type="hidden" id="pemilik">
                    <input type="hidden" id="warna">
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
        $('#select2Kendaraan').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Kendaraan').parent(),
            placeholder: "Cari Kendaraan",
            allowClear: true,
            ajax: {
                url: "{{ route('kendaraan.select2') }}",
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
                    text: '<i class="fas fa-print"></i> Print',
                    title: function() {
                        var nopol = $('#select2Kendaraan').select2('data');
                        if (nopol[0] == null) {
                            alert("Harap Pilih Kendaraan")
                        } else {
                            var judul = '<h6> Laporan Bulanan <br> Pemilik : ' + $('#pemilik').val() + ' <br> No Kendaraan : ' + nopol[0].text + ' Jenis : ' + $('#jenis').val() + ' ' + $('#warna').val() + ' ( Periode : ' + $('#tAwal').val() + ' s/d ' + $('#tAhir').val() + ')<br><h6><hr>';
                            return judul
                        }
                    },
                },
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-info',
                    footer: true,
                    text: '<i class="fas fa-download"></i> Excel',
                    title: 'Laporan Bulanan',
                    messageTop: function() {
                        var nopol = $('#select2Kendaraan').select2('data');
                        if (nopol[0] == null) {
                            alert("Harap Pilih Kendaraan")
                        } else {
                            var judul = 'Pemilik : ' + $('#pemilik').val() + ' No Kendaraan : ' + nopol[0].text + ' Jenis : ' + $('#jenis').val() + ' ' + $('#warna').val() + ' ( Periode : ' + $('#tAwal').val() + ' s/d ' + $('#tAhir').val() + ')';
                            return judul
                        }
                    },
                }
            ],
            order: [
                [3, 'ASC']
            ],
            ajax: {
                url: `{{ route('laporan.bulanan') }}`,
                data: function(d) {
                    d.tAwal = $('#tAwal').val();
                    d.tAhir = $('#tAhir').val();
                    d.kendaraan = $('#select2Kendaraan').val();
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
                    .column(4)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                console.log(api.column(4).data());

                // Total over this page
                pageTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(4).footer().innerHTML =
                    'Rp. ' + numberRenderer(pageTotal);
            }
        });

        function load_title() {
            var nopol = $('#select2Kendaraan').select2('data');
            $.ajax({
                url: `{{ route('laporan.judul') }}`,
                type: 'GET',
                data: {
                    id: nopol[0].id,
                },
                success: function(response) {
                    $('#jenis').val(response.jenis);
                    $('#pemilik').val(response.pemilik);
                    $('#warna').val(response.warna);
                }
            })
        }

        $('#select2Kendaraan').on('change', function() {
            load_title();
            dt.draw();
        });

        $('#tAwal, #tAhir').on('change', function() {
            dt.draw();
        })

    });
</script>
@endsection