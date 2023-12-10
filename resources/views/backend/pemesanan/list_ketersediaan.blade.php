@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <div class="header-title">
                        <div class="row">
                            <div class="col-sm-6 col-lg-6">
                                <form action="{{ route('kendaraan.status') }}" method="get">
                                    <div class="row">
                                        <div class="col">
                                            <select name="jenis" id="jenis" class="custom-select form-control">
                                                <option value="">All</option>
                                                @foreach($jenis as $j)
                                                <option value="{{$j->id}}" {{ $j->id == $id_jenis ? 'selected' : ''}}>{{$j->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <input type="date" id="cf-3" name="tgl" class="form-control" value="{{ isset($tanggal) ? $tanggal : ''}}">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary mb-2">Cari</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-hover text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No Kendaraan</th>
                                    <th>Foto</th>
                                    <th>Jenis</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th class="text-center" colspan="2">Aksi</th>
                                </tr>
                            </thead>

                            @foreach($kendaraan as $k)
                            <tr>
                                <td>{{$k->no_kendaraan}}</td>
                                <td>
                                    <img src="{{ asset ('storage/kendaraan').'/'.$k->foto }}" class="img-thumbnail" alt="Image" width="100px" height="70px">
                                </td>
                                <td>{{$k->nama}}</td>
                                <td>{{$k->tahun}}</td>
                                <td>
                                    <span class="badge {{$k->tanggal != null ? 'badge-danger' : 'badge-success'}} ">{{$k->tanggal != null ? 'Tidak Tersedia' : 'Tersedia'}}</span></h3>
                                </td>
                                <td>
                                    <a href="{{ route('pemesanan.createId', ['id_kendaraan' => $k->id, 'tanggal' => $tanggal]) }}"><span class="btn {{$k->tanggal != null ? '' : 'btn-xs btn-success'}} ">{{$k->tanggal != null ? '' : 'Booking'}}</span></h3></a>
                                    @if (isset($tanggal) && $tanggal == date('Y-m-d'))
                                    <a href="{{ route('penyewaan.createId', ['id_kendaraan' => $k->id, 'tanggal' => $tanggal]) }}"><span class="btn {{$k->tanggal != null ? '' : 'btn-xs btn-primary'}} ">{{$k->tanggal != null ? '' : 'Sewa'}}</span></h3></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </table>
                    </div>
                    <div class="float-right">
                        <nav>
                            <ul class="pagination">
                                {{ $kendaraan->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(function() {
        var dtToday = new Date();
        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();
        var maxDate = year + '-' + month + '-' + day;
        $('#cf-3').attr('min', maxDate);
    });
</script>
@endsection