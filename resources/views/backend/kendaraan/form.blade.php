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
                                    <select id="select2Pemilik" style="width: 100% !important;" name="id_pemilik">
                                        @if(isset($data->id_pemilik))
                                        <option value="{{ $data->id_pemilik }}">{{ $data->pemilik->nama }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Jenis">Jenis :</label>
                                <div class="col-sm-9">
                                    <select id="select2Jenis" style="width: 100% !important;" name="id_jenis">
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
                                <label class="control-label col-sm-3 align-self-center mb-0" for="no_kendaraan">Tahun :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="tahun" name="tahun" placeholder="Masukkan Tahun" value="{{ $data->tahun ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="warna">Warna :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="warna" name="warna" placeholder="Masukan Warna" value="{{ $data->warna ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="status">Status :</label>
                                <div class="col-sm-9">
                                    <select id="status" name="status" class="form-control">
                                        <option value="">.: Pilih :.</option>
                                        <option value="aktif" {{ isset($data->status) && $data->status == 'aktif' ? 'selected' : ''}}>Aktif</option>
                                        <option value="non-aktif" {{ isset($data->status) && $data->status == 'non-aktif' ? 'selected' : ''}}>Non Aktif</option>
                                    </select>
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
                        <img id="avatar" @if(isset($data['foto'])) src="{{ $data['foto'] != NULL ? asset("storage/kendaraan/".$data['foto']) : asset('images/no-content.svg') }}" @else src="{{ asset('images/no-content.svg') }}" @endif style="object-fit: cover; border: 1px solid #d9d9d9" class="mb-2 border-2 mx-auto" height="200px" width="200px" alt="">
                        <input class="form-control image" type="file" id="customFile1" name="foto" accept=".jpg, .jpeg, .png">
                        <p class="text-muted ms-75 mt-50"><small>Allowed JPG, JPEG or PNG. Max
                                size of
                                2MB</small></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Harga</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="select2Jenis">Harga:</label>
                            <div class="col-sm-6">
                                <select id="select2Harga" style="width: 100% !important;" name="id_harga">
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-success" type="button" id="addHarga"><i class="fas fa-plus"></i> Add</button>
                            </div>
                        </div>
                        <hr>
                        <div id="divHarga">
                            @if (isset($hargaBarang))
                            @foreach ($hargaBarang as $hb)
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <input type="hidden" name="harga[]" value="{{$hb->id}}" id="idHrg">
                                    <input type="text" class="form-control" id="name" value="{{$hb->nama}}" readonly>
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="harga" value="{{$hb->nominal}}" readonly>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" id="removeHarga" class="btn btn-danger" onclick="parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
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

        $('#addHarga').on('click', function() {

            var data = $('#select2Harga').select2('data');

            var new_harga = '<div class="row mb-2"><div class="col-sm-5"><input type="hidden" name="harga[]" value="' + data[0].id + '" id="idHrg"><input type="text" class="form-control" id="name" value="' + data[0].nama + '" readonly></div><div class="col-sm-5"><input type="text" class="form-control" id="harga"  value="' + data[0].nominal + '"  readonly></div><div class="col-sm-2"><button type="button" id="removeHarga" class="btn btn-danger" onclick="parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button></div></div>';

            var hasil = CallId(data[0].id);
            if (hasil == 'ada') {
                toastr.error('Harga Sudah Ada', 'Error !');
            } else {
                $('#divHarga').append(new_harga);
            }
        })

        function CallId(id) {
            var arr = $('input[id=idHrg]').map(function() {
                return this.value;
            }).get();

            for (let i = 0; i < arr.length; i++) {
                if (arr[i] == id) {
                    return 'ada';
                }
            }
        }

        $('#select2Harga').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Harga').parent(),
            placeholder: "Cari Harga",
            allowClear: true,
            ajax: {
                url: "{{ route('harga.select2') }}",
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

        $('#select2Pemilik').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Pemilik').parent(),
            placeholder: "Cari Pemilik",
            allowClear: true,
            ajax: {
                url: "{{ route('pemilik.select2') }}",
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

        $('#select2Jenis').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Jenis').parent(),
            placeholder: "Cari Jenis",
            allowClear: true,
            ajax: {
                url: "{{ route('jenis.select2') }}",
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
    });
</script>
@endsection