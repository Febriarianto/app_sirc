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
                                <label class="control-label col-sm-3 align-self-center mb-0" for="nik">NIK :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan NIK" value="{{ $data->nik ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="nama">Nama :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" value="{{ $data->nama ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="alamat">Alamat :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Alamat" value="{{ $data->alamat ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="no_hp">No Hp :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Masukan No Hp" value="{{ $data->no_hp ?? '' }}" readonly>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="kota_tujuan">Kota Tujuan :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="kota_tujuan" name="kota_tujuan" placeholder="Masukan Kota Tujuan" value="{{ $data->kota_tujuan ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="nomor_kendaraan">Nomor Kendaraan :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nomor_kendaraan" name="nomor_kendaraan" placeholder="Masukan Nomor Kendaraan" value="{{ $data->no_hp ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kendaraan">Kendaraan :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="kendaraan" name="kendaraan" placeholder="Masukan Kendaraan" value="{{ $data->kendaraan ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keberangkatan">Tgl Keberangkatan :</label>
                            <div class="col-sm-9">
                                <input type="datetime-local" class="form-control" id="keberangkatan" name="keberangkatan" placeholder="Masukan Tanggal Keberangkatan" value="{{ $data->keberangkatan ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kepulangan">Tgl Kepulangan :</label>
                            <div class="col-sm-9">
                                <input type="datetime-local" class="form-control" id="kepulangan" name="kepulangan" placeholder="Masukan Tanggal Kepulangan" value="{{ $data->kepulangan ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kepulangan">Paket :</label>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="tahunan" name="paket" value="tahunan" class="custom-control-input">
                                    <label class="custom-control-label" for="tahunan">Tahunan</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="bulanan" name="paket" value="bulanan" class="custom-control-input">
                                    <label class="custom-control-label" for="bulanan">Bulanan</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="mingguan" name="paket" value="mingguan" class="custom-control-input">
                                    <label class="custom-control-label" for="mingguan">Mingguan</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="harian" name="paket" value="harian" class="custom-control-input">
                                    <label class="custom-control-label" for="harian">Harian</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="lama_sewa">Lama Sewa :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="lama_sewa" name="lama_sewa" placeholder="Masukan Lama Sewa" value="{{ $data->lama_sewa ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="over_time">Biaya Over Time :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="over_time" name="over_time" placeholder="Masukan Biaya Over Time" value="{{ $data->over_time ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="biaya">Total Biaya :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="biaya" name="biaya" placeholder="Masukan Total Biaya" value="{{ $data->biaya ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="dp">DP:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="dp" name="dp" placeholder="Masukan DP" value="{{ $data->dp ?? '' }}" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="sisa">Sisa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sisa" name="sisa" placeholder="Masukan Sisa" value="{{ $data->sisa ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="metode_pelunasan">Metode Pelunasan :</label>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="cash" name="metode_pelunasan" value="cash" class="custom-control-input">
                                    <label class="custom-control-label" for="cash">Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="transfer" name="metode_pelunasan" value="transfer" class="custom-control-input">
                                    <label class="custom-control-label" for="transfer">Transfer</label>
                                </div>
                            </div>
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