@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST">
        @method($config['form']->method)
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-12">
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
                            <!-- <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Penyewa">Status :</label>
                                <div class="col-sm-9">
                                    <select id="selectStatus" class="form-control" name="status">
                                        <option value="">.: Pilih :.</option>
                                        <option value="pengeluaran" {{isset($data->status) &&  $data->status == 'pengeluaran' ? 'selected' : ''}}>Pengeluaran</option>
                                        <option value="pemasukan" {{isset($data->status) &&  $data->status == 'pemasukan' ? 'selected' : ''}}>Pemasukan</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="detail">Keterangan :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="detail" name="detail" placeholder="Masukkan Detail" value="{{ $data->detail ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="alamat">Nominal :</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="nominal" name="nominal" placeholder="Masukkan Nominal" value="{{ $data->nominal ?? '0' }}">
                                </div>
                            </div>
                            <!-- <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="select2Penyewa">Metode :</label>
                                <div class="col-sm-9">
                                    <select id="selectStatus" class="form-control" name="metode">
                                        <option value="">.: Pilih :.</option>
                                        <option value="cash" {{isset($data->metode) &&  $data->metode == 'cash' ? 'selected' : ''}}>Cash</option>
                                        <option value="transfer" {{isset($data->metode) &&  $data->metode == 'transfer' ? 'selected' : ''}}>Transfer</option>
                                    </select>
                                </div>
                            </div> -->
                            <hr>
                            <p class="text-danger">*Jika Ada</p>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="file">File Bukti :</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="file" name="file">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="file"></label>
                                <div class="col-sm-9">
                                    <a href="{{ isset($data) ? asset('storage/buktiTrf/'.$data->file) : '' }} " target="_blank">{{ isset($data) ? $data->file : '' }}</a>
                                </div>
                            </div>
                            <hr>
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