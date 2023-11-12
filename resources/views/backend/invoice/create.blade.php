@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ route('invoice.store') }}" method="POST" enctype="multipart/form-data">
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

                            {{$data->transaksi->id}}
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
                            <label class="control-label col-sm-3 align-self-center mb-0" for="select2Kendaraan">Nomor Kendaraan :</label>
                            <div class="col-sm-9">
                                {{-- <select id="select2Kendaraan" style="width: 100% !important;" name="id_kendaraan">
                                    @if(isset($data->id_kendaraan))
                                    <option value="{{ $data->id_kendaraan }}">{{ $data->kendaraan->no_kendaraan }}</option>
                                    @endif
                                </select> --}}
                                @if (isset($data))
                                    <input type="text" class="form-control" name="id_kendaraan" value="" readonly >
                                @endif
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
                        <!-- <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kepulangan">Paket :</label>
                        </div>
                        <div class="form-group row">
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
                        </div> -->
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="kepulangan">Paket :</label>
                            <div class="col-sm-9">
                                <select name="" id="" class="form-control">
                                    <option value="">- Pilih -</option>
                                    <option value="harian">Harian</option>
                                    <option value="mingguan">Mingguan</option>
                                    <option value="bulanan">Bulanan</option>
                                    <option value="tahunan">Tahunan</option>
                                </select>
                            </div>
                        </div>
                        <div class="div">
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga">Harga Paket :</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="harga" name="harga_paket" placeholder="Masukan Harga Paket" value="{{ $data->kepulangan ?? '0' }}">
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
                                <input type="number" class="form-control" id="lama_sewa" name="lama_sewa" placeholder="Masukan Lama Sewa" value="{{ $data->lama_sewa ?? '0' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="over_time">Biaya Over Time :</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="over_time" name="biaya_overtime" placeholder="Masukan Biaya Over Time" value="{{ $data->over_time ?? '0' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="biaya">Total Biaya :</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="biaya" name="total_biaya" placeholder="Masukan Total Biaya" value="{{ $data->biaya ?? '0' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="dp">DP:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="dp" name="dp" placeholder="Masukan DP" value="{{ $data->dp ?? '0' }}" >
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="sisa">Sisa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sisa" name="sisa" placeholder="Masukan Sisa" value="{{ $data->sisa ?? '0' }}" readonly>
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
                            <div id="fileTrf" class="col-sm-6" style="display:none;">
                                <input type="file" class="form-control" id="bukti_pelunasan" name="bukti_pelunasan" value="{{ $data->bukti_pelunasan ?? '' }}">
                                <label for="">{{ $data->bukti_pelunasan ?? '' }}</label>
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

        let hargaPaket = $('#harga'),
            lamaSewa = $('#lama_sewa'),
            overTime = $('#over_time'),
            totalBiaya = $('#biaya'),
            dp = $('#dp');

        var CalResult = function() {
            let calTotal = parseInt(hargaPaket.val()) * parseInt(lamaSewa.val()) + parseInt(overTime.val()),
                sisa = calTotal - parseInt(dp.val());

            totalBiaya.val(calTotal);
            $('#sisa').val(sisa);
        }

        CalResult();

        $("#harga").on("keyup", function() {
            console.log(CalResult());
        });
        $("#lama_sewa").on("keyup", function() {
            console.log(CalResult());
        });
        $("#over_time").on("keyup", function() {
            console.log(CalResult());
        });

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

        // $("#formStore").submit(function(e) {
        //     e.preventDefault();
        //     let form = $(this);
        //     let btnSubmit = form.find("[type='submit']");
        //     let btnSubmitHtml = btnSubmit.html();
        //     let url = form.attr("action");
        //     let data = new FormData(this);
        //     $.ajax({
        //         cache: false,
        //         processData: false,
        //         contentType: false,
        //         type: "POST",
        //         url: url,
        //         data: data,
        //         beforeSend: function() {
        //             btnSubmit.addClass("disabled").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop("disabled", "disabled");
        //         },
        //         success: function(response) {
        //             let errorCreate = $('#errorCreate');
        //             errorCreate.css('display', 'none');
        //             errorCreate.find('.alert-text').html('');
        //             btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
        //             if (response.status === "success") {
        //                 toastr.success(response.message, 'Success !');
        //                 setTimeout(function() {
        //                     if (response.redirect === "" || response.redirect === "reload") {
        //                         location.reload();
        //                     } else {
        //                         location.href = response.redirect;
        //                     }
        //                 }, 1000);
        //             } else {
        //                 toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
        //                 if (response.error !== undefined) {
        //                     errorCreate.removeAttr('style');
        //                     $.each(response.error, function(key, value) {
        //                         errorCreate.find('.alert-text').append('<span style="display: block">' + value + '</span>');
        //                     });
        //                 }
        //             }
        //         },
        //         error: function(response) {
        //             btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
        //             toastr.error(response.responseJSON.message, 'Failed !');
        //         }
        //     });
        // });

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
    });
</script>
@endsection