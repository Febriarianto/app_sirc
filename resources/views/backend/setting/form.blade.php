@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST">
        @method($config['form']->method)
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-header justify-content-between">
                        <div class="header-title">
                            <div class="row">
                                <div class="col-sm-6 col-lg-6">
                                    <h4 class="card-title">{{ $config['title'] }}</h4>
                                </div>
                                <div class="col-sm-6 col-lg-6">
                                    <div class="btn-group float-right" role="group" aria-label="Basic outlined example">
                                        <a onclick="history.back()" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-rotate-left"></i> Kembali</a>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan <i class="fa-solid fa-floppy-disk"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div id="errorCreate" class="mb-3" style="display:none;">
                                <div class="alert alert-danger" role="alert">
                                    <div class="alert-icon"><i class="flaticon-danger text-danger"></i></div>
                                    <div class="alert-text">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="title">Title </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Masukkan Title" value="{{ $data->title ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="deskripsi">Deskripsi </label>
                                <div class="col-sm-9">
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" cols="30" rows="3">{{ $data->deskripsi ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="alamat">Alamat </label>
                                <div class="col-sm-9">
                                    <textarea name="alamat" id="alamat" class="form-control" cols="30" rows="3">{{ $data->alamat ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="maps">Maps </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="maps" name="maps" placeholder="Masukkan Kordinat Maps" value="{{ $data->maps ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="telp">Telp </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="telp" name="telp" placeholder="Masukkan No Telphone" value="{{ $data->telp ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="fax">Fax </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="fax" name="fax" placeholder="Masukkan No Fax" value="{{ $data->fax ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="email">Email </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan Email" value="{{ $data->email ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="facebook">Facebook </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="facebook" name="facebook" placeholder="Masukkan Nama Facebook" value="{{ $data->facebook ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="instagram">Instagram </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="instagram" name="instagram" placeholder="Masukkan Nama Instagram" value="{{ $data->instagram ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="youtube">Youtube </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="youtube" name="youtube" placeholder="Masukkan Nama Youtube" value="{{ $data->youtube ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-3 text-center">
                <div class="card">
                    <div class="card-body">
                        <label class="mb-2 text-bold d-block">Logo</label>
                        <img id="avatar" @if(isset($data['logo'])) src="{{ $data['logo'] != NULL ? asset("storage/images/setting/".$data['logo']) : asset('images/no-content.svg') }}" @else src="{{ asset('images/no-content.svg') }}" @endif style="object-fit: cover; border: 1px solid #d9d9d9" class="mb-2 border-2 mx-auto" height="100px" width="100px" alt="">
                        <input class="form-control image" type="file" id="customFile1" name="logo" accept=".jpg, .jpeg, .png">
                        <p class="text-muted ms-75 mt-50"><small>Allowed JPG, JPEG or PNG. Max
                                size of
                                2MB</small></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-3 text-center">
                <div class="card">
                    <div class="card-body">
                        <label class="mb-2 text-bold d-block">Favicon</label>
                        <img id="avatar" @if(isset($data['favicon'])) src="{{ $data['favicon'] != NULL ? asset("storage/images/setting/".$data['favicon']) : asset('images/no-content.svg') }}" @else src="{{ asset('images/no-content.svg') }}" @endif style="object-fit: cover; border: 1px solid #d9d9d9" class="mb-2 border-2 mx-auto" height="100px" width="100px" alt="">
                        <input class="form-control image" type="file" id="customFile1" name="favicon" accept=".jpg, .jpeg, .png">
                        <p class="text-muted ms-75 mt-50"><small>Allowed JPG, JPEG or PNG. Max
                                size of
                                2MB</small></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        $("#formStore").submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btnSubmit = form.find("[type='submit']");
            let btnSubmitHtml = btnSubmit.html();
            let url = form.attr("action");
            let data = new FormData(this);
            $.ajax({
                cache: false,
                processData: false,
                contentType: false,
                type: "POST",
                url: url,
                data: data,
                beforeSend: function() {
                    btnSubmit.addClass("disabled").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop("disabled", "disabled");
                },
                success: function(response) {
                    let errorCreate = $('#errorCreate');
                    errorCreate.css('display', 'none');
                    errorCreate.find('.alert-text').html('');
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    if (response.status === "success") {
                        toastr.success(response.message, 'Success !');
                        setTimeout(function() {
                            if (response.redirect === "" || response.redirect === "reload") {
                                location.reload();
                            } else {
                                location.href = response.redirect;
                            }
                        }, 1000);
                    } else {
                        toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
                        if (response.error !== undefined) {
                            errorCreate.removeAttr('style');
                            $.each(response.error, function(key, value) {
                                errorCreate.find('.alert-text').append('<span style="display: block">' + value + '</span>');
                            });
                        }
                    }
                },
                error: function(response) {
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    toastr.error(response.responseJSON.message, 'Failed !');
                }
            });
        });

        $(".image").change(function() {
            let thumb = $(this).parent().find('img');
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    thumb.attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection