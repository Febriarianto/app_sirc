@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST">
        @method($config['form']->method)
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-8">
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
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Pemilik">Pemilik :</label>
                                <div class="col-sm-9">
                                    <select id="select2Pemilik" style="width: 100% !important;" name="role_id">
                                        @if(isset($data->id_pemilik))
                                        <option value="{{ $data->id_pemilik }}">{{ $data->pemilik->nama }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Jenis">Jenis :</label>
                                <div class="col-sm-9">
                                    <select id="select2Jenis" style="width: 100% !important;" name="role_id">
                                        @if(isset($data->id_jenis))
                                        <option value="{{ $data->id_jenis }}">{{ $data->jenis->nama }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="no_kendaraan">No Kendaraan :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="no_kendaraan" name="no_kendaraan" placeholder="Masukkan No Kendaraan" value="{{ $data->no_kendaraan ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="no_kendaraan">No Kendaraan :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="no_kendaraan" name="no_kendaraan" placeholder="Masukkan No Kendaraan" value="{{ $data->no_kendaraan ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="email">Email :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Masukan Email" value="{{ $data->email ?? '' }}" {{ (isset($data) ? 'readonly' : '') }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <label class="mb-2 text-bold d-block">Foto</label>
                        <img id="avatar" @if(isset($data['image'])) src="{{ $data['image'] != NULL ? asset("storage/images/original/".$data['image']) : asset('images/no-content.svg') }}" @else src="{{ asset('images/no-content.svg') }}" @endif style="object-fit: cover; border: 1px solid #d9d9d9" class="mb-2 border-2 mx-auto" height="200px" width="200px" alt="">
                        <input class="form-control image" type="file" id="customFile1" name="image" accept=".jpg, .jpeg, .png">
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
        $('#select2Active').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        $('#select2Role').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Role').parent(),
            placeholder: "Cari Role",
            allowClear: true,
            ajax: {
                url: "{{ route('roles.select2') }}",
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

        $.each(
            $('.showPass'),
            function(x) {
                $(this).click(function(y) {
                    let text = $(this).parent("div").find('input');
                    let i = $(this).find('i').toggleClass("fa-eye fa-eye-slash");
                    if (text.attr("type") == 'password') {
                        text.attr("type", 'text')
                    } else {
                        text.attr("type", 'password')
                    }
                })
            }
        );
    });
</script>
@endsection