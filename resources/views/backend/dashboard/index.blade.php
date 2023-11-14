@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{$data['countCar']}}</h3>
                <p>Mobil</p>
            </div>
            <div class="icon">
                <i class="fas fa-car"></i>
            </div>
            <a href="{{route('kendaraan.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                <p>Check Out</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{route('dashboard.checkin')}}" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
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

        $('#select2Kendaraan').on('change', function() {
            $.ajax({
                url: "{{ route('dashboard.graph') }}",
                dataType: "json",
                type: "GET",
                success: function(response) {
                    const xValues = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'];
                    const yValues = [];

                    for (let i = 1; i < 13; i++) {
                        yValues.push(response[i])
                    }

                    console.log(yValues, xValues);

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
                                        max: 20
                                    }
                                }],
                            }
                        }
                    });
                }
            })
        });
    })
</script>
@endsection