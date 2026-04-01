@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $data['countNotAvail'] . " / " . $data['countAvail'] }}</h3>
                <p>Mobil Digunakan / Mobil Tersedia</p>
            </div>
            <div class="icon">
                <i class="fas fa-car"></i>
            </div>
            <a href="{{ route('kendaraan.status') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{$data['countPemesanan']}}</h3>
                <p>Pemesanan</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <a href="{{route('pemesanan.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{$data['countPenyewa']}}</h3>
                <p>Pelanggan</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="{{route('penyewa.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <p>Check In</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{route('dashboard.checkin')}}" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mobil Akan Kembali</h3>
            </div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Filter Tanggal Kepulangan</label>
                    </div>
                    <div class="col-md-8">
                        <input type="date" id="filter_tanggal" class="form-control">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="table-kembali">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Plat</th>
                                <th>Nama Penyewa</th>
                                <th>No HP</th>
                                <th>Estimasi Kepulangan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <div class="header-title">
                    <div class="row">
                        <div class="col-sm-6 col-lg-6">
                            <h4 class="card-title">List Data Pemesanan</h4>
                        </div>
                        <div class="col-sm-6 col-lg-6">
                            <a href="{{ route('kendaraan.status') }}" class="btn btn-primary float-right">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt" class="table table-bordered w-100">
                        <thead>
                            <tr>
                                <th>No Inv</th>
                                <th>Nama Pemesan</th>
                                <th>Alamat</th>
                                <th>No Hp</th>
                                <th>Tanggal Berangkat</th>
                                <th>Estimasi Jam</th>
                                <th>NO Kendaraan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row">

    <section class="col-lg-12 connectedSortable ui-sortable">

        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Grafik Penyewaan Mobil Tahun {{ date('Y');}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="">Pilih Mobil</label>
                    </div>
                    <div class="col-6">
                        <select id="select2Kendaraan" style="width: 100% !important;" name="id_kendaraan">
                        </select>
                    </div>
                </div>
                <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                    <canvas id="revenue-chart-canvas" style="height:100%; display: block; width: 100%;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>

    </section>
</div>
@endsection
@section('script')

<!-- 🔥 TAMBAHAN WAJIB -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.dataTables.min.css">
<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

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

        var tableKembali = $('#table-kembali').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard') }}",
                data: function(d) {
                    d.tanggal = $('#filter_tanggal').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
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
                    data: 'no_hp',
                    name: 'no_hp'
                },
                {
                    data: 'estimasi_kepulangan',
                    name: 'estimasi_kepulangan',
                    render: function(data, type, full) {
                        return full.estimasi_kepulangan + " " + full.keberangkatan_time;
                    }
                }
            ]
        });

        $('#dt').DataTable({

            responsive: true,
            serverSide: true,
            processing: true,

            dom: "<'row'<'col-sm-2'l><'col-sm-2'B><'col-sm-8'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-6'i><'col-sm-6'p>>",

            buttons: [{
                extend: 'print',
                footer: true,
                text: 'Print',
                title: function() {
                    return 'Daftar Pemesanan Mobil';
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            }],

            // 🔥 SORT BERDASARKAN TANGGAL RAW
            order: [
                [4, 'asc']
            ],

            ajax: {
                url: `{{ route('pemesanan.index') }}`
            },

            // 🔥 GROUPING PER TANGGAL (FIX PAGINATION)
            rowGroup: {
                dataSrc: function(row) {
                    return row.estimasi_tgl ?? row.keberangkatan ?? '1970-01-01';
                },
                startRender: function(rows, group) {

                    let label = moment(group).format('DD MMMM YYYY');

                    return $('<tr/>')
                        .append(`
                        <td colspan="8" style="background:#343a40;color:#fff;font-weight:bold;">
                            📅 ${label} (${rows.count()} pesanan)
                        </td>
                    `);
                }
            },

            columns: [{
                    data: 'no_inv',
                    name: 'no_inv'
                },
                {
                    data: 'penyewa.nama',
                    name: 'penyewa.nama'
                },
                {
                    data: 'penyewa.alamat',
                    name: 'penyewa.alamat'
                },
                {
                    data: 'penyewa.no_hp',
                    name: 'penyewa.no_hp'
                },
                {
                    data: 'estimasi_tgl',
                    name: 'estimasi_tgl',
                    render: function(data, type, full) {

                        let tgl = full.estimasi_tgl ?? full.keberangkatan ?? '1970-01-01';

                        // 🔥 PENTING: untuk sorting & pagination
                        if (type === 'sort' || type === 'type') {
                            return tgl;
                        }

                        return moment(tgl).format('YYYY-MM-DD');
                    }
                },
                {
                    data: 'estimasi_time',
                    name: 'estimasi_time'
                },
                {
                    data: 'kendaraan.no_kendaraan',
                    name: 'kendaraan.no_kendaraan'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
            ],

            // 🔥 BIAR TABEL STABIL
            drawCallback: function() {
                $('#dt tbody tr').css('vertical-align', 'middle');
            },

            // 🔥 DELETE FIX (ANTI DOUBLE CLICK BUG)
            rowCallback: function(row, data) {
                let api = this.api();

                $(row).find('.btn-delete').off('click').on('click', function() {

                    let pk = $(this).data('id');
                    let url = `{{ route("pemesanan.index") }}/` + pk;

                    Swal.fire({
                        title: "Anda Yakin ?",
                        text: "Data tidak dapat dikembalikan setelah di hapus!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Tidak, Batalkan",
                    }).then((result) => {

                        if (result.isConfirmed) {

                            $.ajax({
                                url: url,
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                error: function() {
                                    toastr.error('Gagal hapus data', 'Failed!');
                                },
                                success: function(response) {

                                    if (response.status === "success") {
                                        toastr.success(response.message, 'Success!');
                                        api.ajax.reload(null, false);
                                    } else {
                                        toastr.error(response.message ?? "Error", 'Failed!');
                                    }

                                }
                            });

                        }

                    });

                });
            }

        });

        // reload saat filter berubah
        $('#filter_tanggal').on('change', function() {
            tableKembali.ajax.reload();
        });

        function load_graph() {
            var nopol = $('#select2Kendaraan').select2('data');
            $.ajax({
                url: "{{ route('dashboard.graph') }}",
                dataType: "json",
                data: {
                    nopol: (nopol.length !== 1) ? null : nopol[0].id
                },
                type: "GET",
                success: function(response) {
                    const xValues = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'];
                    const yValues = [];

                    for (let i = 1; i < 13; i++) {
                        yValues.push(response[i]);
                    }

                    new Chart("revenue-chart-canvas", {
                        type: "line",
                        data: {
                            labels: xValues,
                            datasets: [{
                                fill: false,
                                lineTension: 0,
                                backgroundColor: "rgba(0,0,255,1.0)",
                                borderColor: "rgba(0,0,255,0.1)",
                                data: yValues
                            }]
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        min: 0,
                                        max: Math.max.apply(Math, yValues)
                                    }
                                }],
                            }
                        }
                    });
                }
            });
        };

        load_graph();

        $('#select2Kendaraan').on('change', function() {
            load_graph();
        });
    })
</script>
@endsection