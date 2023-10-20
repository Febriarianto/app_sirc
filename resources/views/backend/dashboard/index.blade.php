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

    <section class="col-lg-12 connectedSortable ui-sortable">

        <div class="card">
            <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Grafik Penyewaan Permobil
                </h3>
            </div>
            <div class="card-body">
                <div class="tab-content p-0">

                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="revenue-chart-canvas" height="600" style="height: 300px; display: block; width: 304px;" width="608" class="chartjs-render-monitor"></canvas>
                    </div>
                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                        <canvas id="sales-chart-canvas" height="0" style="height: 0px; display: block; width: 0px;" class="chartjs-render-monitor" width="0"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
@endsection