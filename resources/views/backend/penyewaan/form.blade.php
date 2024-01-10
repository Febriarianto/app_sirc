@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST">
        @method($config['form']->method)
        @csrf
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
                <div class="row">
                    <div class="col-sm-6">
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
                                        <option value="{{ $data->id_penyewa }}">{{ $data->penyewa->nama }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="nik">NIK :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan Nik" value="{{ $data->nik ?? '' }}" readonly>
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
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="select2Kendaraan">Nomor Kendaraan :</label>
                            <div class="col-sm-9">
                                <select id="select2Kendaraan" style="width: 100% !important;" name="id_kendaraan">
                                    @if(isset($kendaraan->id))
                                    <option value="{{ $kendaraan->id }}">{{ $kendaraan->no_kendaraan }}</option>
                                    @elseif(isset($data->id_kendaraan))
                                    <option value="{{ $data->id_kendaraan }}">{{ $data->kendaraan->no_kendaraan }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keberangkatan">Tgl Keberangkatan :</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="keberangkatan" name="keberangkatan" placeholder="Masukan Tanggal Keberangkatan" value="{{ $data->keberangkatan ?? date('Y-m-d') }}" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Harga Sewa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="harga_sewa" name="harga_sewa" value="{{ $data->harga_sewa ?? '0'}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="divProses">
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="paket">Paket:</label>
                                <div class="col-sm-9">
                                    <select id="paket" name="paket" class="form-control">
                                        <option value="">.: Pilih Paket:.</option>
                                        <option value="jam" {{ isset($data->paket) && $data->paket == 'jam' ? 'selected' : ''}}>Jam</option>
                                        <option value="harian" {{ isset($data->paket) && $data->paket == 'harian' ? 'selected' : ''}}>Harian</option>
                                        <option value="mingguan" {{ isset($data->paket) && $data->paket == 'mingguan' ? 'selected' : ''}}>Mingguan</option>
                                        <option value="bulanan" {{ isset($data->paket) && $data->paket == 'bulanan' ? 'selected' : ''}}>Bulanan</option>
                                        <option value="tahunan" {{ isset($data->paket) && $data->paket == 'tahunan' ? 'selected' : ''}}>Tahunan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Lama Sewa:</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="lama_sewa" name="lama_sewa" value="{{ $data->lama_sewa ?? '0'}}">
                                </div>
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Hari</label>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Kota Tujuan:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="kota_tujuan" name="kota_tujuan" value="{{ $data->kota_tujuan ?? ''}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
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
                        </div>
                    </div>
                </div>
                <hr>
                <div class="div">
                    <button class="btn btn-info" type="button" id="add"> Tambah Pembayaran</button>
                </div>
                <hr>
                @if (isset($pembayaran))
                @foreach ($pembayaran as $p )
                <div class="form-group row">
                    <input type="hidden" value="{{$p->id}}" name="idP[]">
                    <div class="col-sm-3">
                        <label class="control-lab" for="harga_sewa">Jenis Pembayaran</label>
                        <select name='tipeP[]' id='tipe' class='form-control'>
                            <option value=''>.:Pilih:.</option>
                            <option value='dp' {{$p->tipe == 'dp' ? 'selected' : ''}}>DP</option>
                            <option value='pelunasan' {{$p->tipe == 'pelunasan' ? 'selected' : ''}}>Pelunasan</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-lab" for="nominal">Nominal</label>
                        <input type="text" class="form-control" id="nominal" name="nominalP[]" value="{{ $p->nominal ?? '0'}}">
                    </div>
                    <div class="col-sm-3">
                        <label class="control-lab" for="metodeP">Metode</label>
                        <select name='metodeP[]' id='metode' class='form-control'>
                            <option value=''>.:Pilih:.</option>
                            <option value='cash' {{$p->metode == 'cash' ? 'selected' : ''}}>Cash</option>
                            <option value='transfer' {{$p->metode == 'transfer' ? 'selected' : ''}}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-lab" for="fileP">File</label>
                        <input type="file" class="form-control" id="" name="fileP[]">
                        <a href="{{ asset ('storage/buktiTrf/'.$p->file)}}" target="_blank">{{$p->file}}</a>
                    </div>
                </div>
                @endforeach
                @endif
                <div id="divPay"></div>
                <div id="divWa" style="{{ isset($data) ? 'display:block;' : 'display:none;'}}">
                    <a href="https://api.whatsapp.com/send/?phone={{ isset($data) ? $data->penyewa->no_hp : ''}}&text=CV.ANDRA%20PRATAMA%0aConcept%20AutoRent%0a=========================%0aINVOICE%0a=========================%0aTgl%20:%{{ isset($data) ? $data->keberangkatan : ''}}%0aNo%20Kwitansi%20:%20{{ isset($data) ? $data->id : '' }}%0aNama%20:%20{{ isset($data) ? $data->penyewa->nama : ''}}%0a=========================%0aSewa%20Mobil%20%0aNo%20Kendaraan%20:%20{{ isset($data) ? $data->kendaraan->no_kendaraan : ''}}%0alama%20Sewa%20:%20{{ isset($data) ? $data->lama_sewa : '' }}%20Hari%0aTotal%20:%20Rp.%20{{ isset($data) ? number_format($data->biaya) : '' }}%0aUang%20Masuk%20:%20Rp.%20{{isset($data) ? number_format($data->biaya - $data->sisa) : '' }}%0aKurang%20:%20Rp.%20{{isset($data) ? number_format($data->sisa) : '' }}%0a=========================" id="wa" target="_blank" class="btn btn-success float-right"><svg xmlns="http://www.w3.org/2000/svg" height="26" width="22" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                        </svg></a>
                </div>
            </div>
            <div class="card-footer">
                <div class="btn-group float-right" role="group" aria-label="Basic outlined example">
                    <a onclick="history.back()" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-rotate-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan <i class="fa-solid fa-floppy-disk"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        let hargaPaket = $('#harga_sewa'),
            lamaSewa = $('#lama_sewa'),
            totalBiaya = $('#biaya'),
            dp = $('#dp');

        var CalResult = function() {
            var arr = $('input[id=nominal]').map(function() {
                return this.value;
            }).get();

            let sum = 0;

            arr.forEach(num => {
                sum += parseInt(num);
            })

            let calTotal = parseInt(hargaPaket.val()) * parseInt(lamaSewa.val()),
                sisa = calTotal - sum;

            totalBiaya.val(calTotal);

            $('#sisa').val(sisa);
        }

        CalResult();

        $("#lama_sewa").on("change", function() {
            CalResult();
        });

        $("#harga_sewa, #lama_sewa").on("keyup", function() {
            CalResult();
        });

        $('input[id=nominal]').on("keyup", function() {
            CalResult();
        });

        $('#add').on('click', add);

        function add() {
            var new_input = "<div class='form-group row'><div class='col-sm-3'><label class='control-lab' for='harga_sewa'>Jenis Pembayaran</label><select name='tipe[]' id='tipe' class='form-control'><option value=''>.:Pilih:.</option><option value='dp'>DP</option><option value='pelunasan'>Pelunasan</option></select></div><div class='col-sm-3'><label class='control-lab' for='nominal'>Nominal</label><input type='text' class='form-control' id='nominal' name='nominal[]' value='0'></div><div class='col-sm-3'><label class='control-lab' for='metode'>Metode</label><select name='metode[]' id='metode' class='form-control'><option value=''>.:Pilih:.</option><option value='cash'>Cash</option><option value='transfer'>Transfer</option></select></div><div class='col-sm-2'><label class='control-lab' for='file'>File</label><input type='file' class='form-control' id='file' name='file[]'></div><div class='col-sm-1'><label class='control-lab' for='harga_sewa'></label><br><button class='btn btn-danger btn-remove' type='button'><i class='fas fa-trash'></i></button></div></div>";
            $('#divPay').append(new_input);

            reload_function();

        }

        reload_function();

        function reload_function() {
            $('input[id=nominal]').on("keyup", function() {
                CalResult();
            });

            $('.btn-remove').click(function() {
                console.log(this);
                let parent = this.parentNode.parentNode
                parent.remove()

                CalResult();
            })
        }

        let nik2 = $('#select2Penyewa option:selected').text().trim();
        $.ajax({
            url: `{{ url ('backend/penyewa/getPenyewa')}}/` + nik2,
            success: function(response) {
                $('#nik').val(response.data.nik);
                $('#alamat').val(response.data.alamat);
                $('#no_hp').val(response.data.no_hp);
            }
        })

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
            let nama = $('#select2Penyewa option:selected').text().trim();
            $.ajax({
                url: `{{ url ('backend/penyewa/getPenyewa')}}/` + nama,
                success: function(response) {
                    $('#nik').val(response.data.nik);
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
    });
</script>
@endsection