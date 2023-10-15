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
                                <h4 class="card-title">Setting </h4>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <a href="{{ route('setting.edit',$data->id) }}" class="btn btn-primary float-right">
                                    <i class="fas fa-edit"></i> Update
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
                                    <th>Field</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Logo</td>
                                    <td><img @if(isset($data['logo'])) src="{{ $data['logo'] != NULL ? asset("storage/images/setting/".$data['logo']) : asset('images/no-content.svg') }}" @else src="{{ asset('images/no-content.svg') }}" @endif alt="" style="max-width:50px; max-height: 50px;"></td>
                                </tr>
                                <tr>
                                    <td>Favicon</td>
                                    <td><img @if(isset($data['favicon'])) src="{{ $data['favicon'] != NULL ? asset("storage/images/setting/".$data['favicon']) : asset('images/no-content.svg') }}" @else src="{{ asset('images/no-content.svg') }}" @endif alt="" style="max-width:50px; max-height: 50px;"></td>
                                </tr>
                                <tr>
                                    <td>Title</td>
                                    <td>{{$data->title}}</td>
                                </tr>
                                <tr>
                                    <td>Deskripsi</td>
                                    <td>{{$data->deskripsi}}</td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>{{$data->alamat}}</td>
                                </tr>
                                <tr>
                                    <td>Maps</td>
                                    <td>{{$data->maps}}</td>
                                </tr>
                                <tr>
                                    <td>Telp</td>
                                    <td>{{$data->telp}}</td>
                                </tr>
                                <tr>
                                    <td>Fax</td>
                                    <td>{{$data->fax}}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{$data->email}}</td>
                                </tr>
                                <tr>
                                    <td>Facebook</td>
                                    <td>{{$data->facebook}}</td>
                                </tr>
                                <tr>
                                    <td>Instagram</td>
                                    <td>{{$data->instagram}}</td>
                                </tr>
                                <tr>
                                    <td>Youtube</td>
                                    <td>{{$data->youtube}}</td>
                                </tr>
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

@endsection