@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ route('penyewaan.store', $data->id) }}" method="POST">
        @method('POST')
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
                            <input type="hidden" name="id_transaksi" value="{{ $data->id }}">
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Penyewa">Penyewa :</label>
                                <div class="col-sm-9">
                                    <select id="select2Penyewa" style="width: 100% !important;" name="id_penyewa" disabled>
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
                                <select id="select2Kendaraan" style="width: 100% !important;" name="id_kendaraan" disabled>
                                    @if(isset($data->id_kendaraan))
                                    <option value="{{ $data->id_kendaraan }}">{{ $data->kendaraan->no_kendaraan }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keberangkatan">Tgl Keberangkatan :</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="keberangkatan" name="keberangkatan" placeholder="Masukan Tanggal Keberangkatan" value="{{ $data->keberangkatan ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kepulangan">Tgl Kepulangan :</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="kepulangan" name="kepulangan" placeholder="Masukan Tanggal Kepulangan" value="{{ $data->kepulangan ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="lama_sewa">Lama Sewa :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="lama_sewa" name="lama_sewa" value="{{ $data->lama_sewa }}">
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
                                <input type="text" class="form-control" id="dp" name="dp" placeholder="Masukan Nominal DP" value="{{ $data->dp ?? '' }}" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="metode_dp">Metode DP :</label>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="cash" name="metode_dp" value="cash" class="custom-control-input" {{ isset($data) && $data->metode_dp == "cash" ? 'checked' : '' }} disabled>
                                    <label class="custom-control-label" for="cash">Cash</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="transfer" name="metode_dp" value="transfer" class="custom-control-input" {{ isset($data) && $data->metode_dp == "transfer" ? 'checked' : '' }} disabled>
                                    <label class="custom-control-label" for="transfer">Transfer</label>
                                </div>
                            </div>
                            <div id="fileTrf" class="col-sm-6" style="display:none;">
                                <input type="file" class="form-control" id="bukti_dp" name="bukti_dp" value="{{ $data->bukti_dp ?? '' }}">
                                <label for="">{{ $data->bukti_dp ?? '' }}</label>
                            </div>
                        </div>

                           
                        
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="paket">Paket:</label>
                            <div class="col-sm-9">
                                <select id="paket" name="paket" class="form-control">
                                    <option value="">.: Pilih Paket:.</option>
                                    <option value="harian" {{ $data->paket === 'harian' ? 'selected' : '' }}>Harian</option>
                                    <option value="jam" {{ $data->paket === 'jam' ? 'selected' : '' }}>Jam</option>
                                    <option value="tahunan" {{ $data->paket === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                    <option value="bulanan" {{ $data->paket === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="mingguan" {{ $data->paket === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Harga Sewa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="harga_sewa" name="harga_sewa" value="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="over_time">Biaya Overtime:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="over_time" name="over_time" value="">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="total_bayar">Total Bayar:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="total_bayar" name="biaya" value="">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="control-label col-sm-6 align-self-center mb-0" for="metode_pelunasan">Metode Pelunasan :</label>
                            </div>
                            <div class="row">
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
                                <div id="fileTrfPelunasan" class="col-sm-6" style="{{ isset($data) && $data->metode_pelunasan == 'transfer' ? '' : 'display:none;' }}">
                                    <input type="file" class="form-control" id="bukti_pelunasan" name="bukti_pelunasan" value="{{ $data->bukti_pelunasan ?? '' }}">
                                    <label for="">{{ $data->bukti_pelunasan ?? '' }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="total_bayar">Sisa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="total_bayar" name="sisa">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kota_tujuan">Kota Tujuan :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="kota_tujuan" name="kota_tujuan" value="{{ $data->kota_tujuan }}">
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

        if ($('#transfer').is(':checked')) {
            let divFile = document.querySelector('input[name="bukti_dp"]').parentNode;
            divFile.style.display = "";
        }

        if ($('#cash').is(':checked')) {
            let divFile = document.querySelector('input[name="bukti_dp"]').parentNode;
            divFile.style.display = "none";
        }

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
                    console.log("Server Response:", response);
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
                        console.error("AJAX Error:", response);
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

        let radioFile = document.querySelectorAll('input[name="metode_pelunasan"]');

        radioFile.forEach(el => {
            el.addEventListener('change', () => {
                let divFile = document.querySelector('input[name="bukti_pelunasan"]').parentNode;

                if (el.checked && el.value == 'transfer') {
                    divFile.style.display = "";
                } else {
                    divFile.style.display = 'none';
                }
            })
        });

        let keberangkatanInput = document.getElementById('keberangkatan');
        let kepulanganInput = document.getElementById('kepulangan');
        let lamaSewaInput = document.getElementById('lama_sewa');

        keberangkatanInput.addEventListener('change', hitungLamaSewa);
        kepulanganInput.addEventListener('change', hitungLamaSewa);

        function hitungLamaSewa() {
            let tanggalKeberangkatan = new Date(keberangkatanInput.value);
            let tanggalKepulangan = new Date(kepulanganInput.value);

            let selisihHari = Math.ceil((tanggalKepulangan - tanggalKeberangkatan) / (1000 * 60 * 60 * 24));

            lamaSewaInput.value = selisihHari;

            if (selisihHari < 0) {
                lamaSewaInput.value = 0;
            }
        }

        document.addEventListener('DOMContentLoaded', hitungLamaSewa);

       
        let statusDropdown = document.getElementById('status');
        let kotaTujuanLabel = document.querySelector('label[for="kota_tujuan"]');
        let kotaTujuanInput = document.getElementById('kota_tujuan');

        document.addEventListener('DOMContentLoaded', function() {
            kotaTujuanLabel.style.display = 'block';
            if (statusDropdown.value !== 'batal') {
                kotaTujuanInput.style.display = 'none';
            }
        });

       
        statusDropdown.addEventListener('change', function() {
            if (statusDropdown.value === 'batal') {
                kotaTujuanInput.value = ''; 
                kotaTujuanInput.style.display = 'none';
            } else {
                kotaTujuanInput.style.display = 'block';
            }
        });


        document.getElementById('paket').addEventListener('change', function() {
        let paket = this.value;
        let hargaSewaInput = document.getElementById('harga_sewa');
        let hargaSewa = '';

        if (paket === 'harian') {
            hargaSewa = "{{ $data->kendaraan->jenis->harga_24 }}";
        } else if (paket === 'jam') {
            hargaSewa = "{{ $data->kendaraan->jenis->harga_12 }}";
        }

        hargaSewaInput.value = hargaSewa;
    });

    let radioMetodePelunasan = document.querySelectorAll('input[name="metode_pelunasan"]');

        radioMetodePelunasan.forEach(el => {
            el.addEventListener('change', () => {
                let divFilePelunasan = document.getElementById('fileTrfPelunasan');
                let inputBuktiPelunasan = document.querySelector('input[name="bukti_pelunasan"]');

                if (el.checked && el.value == 'transfer') {
                    divFilePelunasan.style.display = "";
                    inputBuktiPelunasan.setAttribute('required', 'required');
                } else {
                    divFilePelunasan.style.display = 'none';
                    inputBuktiPelunasan.removeAttribute('required');
                }
            });
        });


    });
</script>
@endsection