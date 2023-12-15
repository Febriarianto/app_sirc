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
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Penyewa">Penyewa :</label>
                                <div class="col-sm-9">
                                    <select id="select2Penyewa" style="width: 100% !important;" name="id_penyewa">
                                        @if(isset($data->id_penyewa))
                                        <option value="{{ $data->id_penyewa }}">{{ $data->penyewa->nik }}</option>
                                        @endif
                                    </select>
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
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="select2Kendaraan">Nomor Kendaraan :</label>
                            <div class="col-sm-9">
                                <select id="select2Kendaraan" style="width: 100% !important;" name="id_kendaraan">
                                    @if(isset($kendaraan->id))
                                    <option value="{{ $kendaraan->id }}">{{ $kendaraan->no_kendaraan }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keberangkatan">Berangkat :</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="keberangkatan" name="keberangkatan" placeholder="Masukan Tanggal Keberangkatan" value="{{ $data->keberangkatan ?? $tanggal }}" {{ isset($data->keberangkatan) ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kepulangan">Pulang :</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="kepulangan" name="kepulangan" placeholder="Masukan Tanggal Kepulangan" value="{{ $data->kepulangan ?? '' }}">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kota_tujuan">Kota Tujuan :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="kota_tujuan" name="kota_tujuan" value="{{ $data->kota_tujuan ?? ''}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="dp">DP:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="dp" name="dp" placeholder="Masukan Nominal DP" value="{{ $data->dp ?? '0' }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="metode_dp">Metode DP :</label>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="cash" name="metode_dp" value="cash" class="custom-control-input" {{ isset($data) && $data->metode_dp == "cash" ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="cash">Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="transfer" name="metode_dp" value="transfer" class="custom-control-input" {{ isset($data) && $data->metode_dp == "transfer" ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="transfer">Transfer</label>
                                </div>
                            </div>
                            <div id="fileTrfDP" class="col-sm-6" style="{{ isset($data) && $data->metode_dp == 'transfer' ? '' : 'display:none;' }}">
                                <input type="file" class="form-control" id="bukti_dp" name="bukti_dp" value="{{ $data->bukti_dp ?? '' }}">
                                <a href="{{ isset($data->bukti_dp) ? asset ('storage/buktiDP/'.$data->bukti_dp) : '' }}" target="_blank">{{ $data->bukti_dp ?? '' }}</a>
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="paket">Paket:</label>
                            <div class="col-sm-9">
                                <select id="paket" name="paket" class="form-control">
                                    <option value="">.: Pilih Paket:.</option>
                                    <option value="jam" {{ isset ($dataTransaksi->paket) && $dataTransaksi->paket === 'jam' ? 'selected' : '' }}>Jam</option>
                                    <option value="harian" {{ isset ($dataTransaksi->paket) && $dataTransaksi->paket === 'harian' ? 'selected' : '' }}>Harian</option>
                                    <option value="mingguan" {{ isset ($dataTransaksi->paket) && $dataTransaksi->paket === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="bulanan" {{ isset ($dataTransaksi->paket) && $dataTransaksi->paket === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="tahunan" {{ isset ($dataTransaksi->paket) && $dataTransaksi->paket === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" id="harga_harian" value="{{ isset($dataTransaksi->jenis->harga_24) ? $dataTransaksi->jenis->harga_24 : '' }}">
                        <input type="hidden" id="harga_jam" value="{{ isset($dataTransaksi->jenis->harga_12) ? $dataTransaksi->jenis->harga_12 : '' }}">


                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Harga Sewa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="harga_sewa" name="harga_sewa" value="{{ $data->harga_sewa ?? '0'}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="biaya">Total Biaya:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="biaya" name="biaya" value="{{ $data->biaya ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="sisa">Sisa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sisa" name="sisa" value="{{ $data->sisa ?? ''}}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-6 align-self-center mb-0" for="metode_pelunasan">Metode Pelunasan :</label>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="cash_pelunasan" name="metode_pelunasan" value="cash" class="custom-control-input" {{ isset($data) && $data->metode_pelunasan == "cash" ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="cash_pelunasan">Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="transfer_pelunasan" name="metode_pelunasan" value="transfer" class="custom-control-input" {{ isset($data) && $data->metode_pelunasan == "transfer" ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="transfer_pelunasan">Transfer</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="lainnya" name="metode_pelunasan" value="lainnya" class="custom-control-input" {{ isset($data) && $data->metode_pelunasan == "lainnya" ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="lainnya">Lainnya</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <div id="fileTrfPelunasan" style="{{ isset($data) && $data->metode_pelunasan == 'transfer' ? '' : 'display:none;' }}">
                                    <input type="file" class="form-control" id="bukti_pelunasan" name="bukti_pelunasan" value="{{ $data->bukti_pelunasan ?? '' }}">
                                    <a href="{{ isset($data->bukti_pelunasan) ? asset ('storage/buktiPelunasan/'.$data->bukti_pelunasan) : '' }}" target="_blank">{{ $data->bukti_pelunasan ?? '' }}</a>
                                </div>
                            </div>
                            <div class="col">
                                <div id="inputKeterangan" style="{{ isset($data) && $data->metode_pelunasan == 'lainnya' ? '' : 'display:none;' }}">
                                    <input type="input" class="form-control" id="keterangan" name="keterangan" value="{{ $data->keterangan?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group float-right" role="group" aria-label="Basic outlined example">
                            <a onclick="history.back()" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-rotate-left"></i> Kembali</a>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan <i class="fa-solid fa-floppy-disk"></i></button>
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

        let nik2 = $('#select2Penyewa option:selected').text().trim();
        $.ajax({
            url: `{{ url ('backend/penyewa/getPenyewa')}}/` + nik2,
            success: function(response) {
                $('#nama').val(response.data.nama);
                $('#alamat').val(response.data.alamat);
                $('#no_hp').val(response.data.no_hp);
            }
        })

        let hargaPaket = $('#harga_sewa'),
            totalBiaya = $('#biaya'),
            dp = $('#dp');

        var CalResult = function() {
            let calTotal = parseInt(hargaPaket.val()),
                sisa = calTotal - parseInt(dp.val());

            totalBiaya.val(calTotal);

            console.log(calTotal);
            $('#sisa').val(sisa);
        }

        CalResult();

        $("#harga_sewa").on("keyup", function() {
            console.log(CalResult());
        });

        $("#dp").on("keyup", function() {
            console.log(CalResult());
        });

        $('#select2Active').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        $('#select2Penyewa').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#select2Penyewa').parent(),
            placeholder: "Cari Penyewa",
            allowClear: true,
            ajax: {
                url: "{{ route('penyewa.select2') }}",
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

        $('#select2Penyewa').on('change', function() {
            let nik = $('#select2Penyewa option:selected').text().trim();
            $.ajax({
                url: `{{ url ('backend/penyewa/getPenyewa')}}/` + nik,
                success: function(response) {
                    $('#nama').val(response.data.nama);
                    $('#alamat').val(response.data.alamat);
                    $('#no_hp').val(response.data.no_hp);
                }
            })
        });

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

        let radioFileDP = document.querySelectorAll('input[name="metode_dp"]');

        radioFileDP.forEach(el => {
            el.addEventListener('change', () => {
                let divFileDP = document.querySelector('input[name="bukti_dp"]').parentNode;
                if (el.checked && el.value == 'transfer') {
                    divFileDP.style.display = "";
                } else {
                    divFileDP.style.display = 'none';
                }
            })
        });


        let radioFile = document.querySelectorAll('input[name="metode_pelunasan"]');

        radioFile.forEach(el => {
            el.addEventListener('change', () => {
                let divFile = document.querySelector('input[name="bukti_pelunasan"]').parentNode;
                let divKeterangan = document.querySelector('input[name="keterangan"]').parentNode;

                if (el.checked && el.value == 'transfer') {
                    divFile.style.display = "";
                    divKeterangan.style.display = 'none';
                } else if (el.checked && el.value == 'lainnya') {
                    divKeterangan.style.display = "";
                    divFile.style.display = 'none';
                } else {
                    divFile.style.display = 'none';
                    divKeterangan.style.display = 'none';
                }
            })
        });
        document.getElementById('paket').addEventListener('change', function() {
            CalResult();
        });
    });
</script>
@endsection