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
                                <input type="date" class="form-control" id="keberangkatan" name="keberangkatan" placeholder="Masukan Tanggal Keberangkatan" value="{{ $data->keberangkatan ?? '' }}" disabled>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Harga Sewa:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="harga_sewa" name="harga_sewa" value="{{ $data->harga_sewa ?? '0'}}" disabled>
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
                                    <select id="paket" name="paket" class="form-control" disabled>
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
                                    <input type="number" class="form-control" id="lama_sewa" name="lama_sewa" value="{{ $data->lama_sewa ?? '0'}}" disabled>
                                </div>
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Hari</label>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Kota Tujuan:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="kota_tujuan" name="kota_tujuan" value="{{ $data->kota_tujuan ?? ''}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center mb-0" for="harga_sewa">Over Time:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="over_time" name="over_time" value="{{ $data->over_time ?? '0'}}">
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
                        <input type="text" class="form-control" id="nominal" name="nominalP[]" value="{{ $p->nominal ?? '0'}}" readonly>
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
                        <input type="file" class="form-control" id="" name="fileP[]" readonly>
                        <a href="{{ asset ('storage/buktiTrf/'.$p->file)}}" target="_blank">{{$p->file}}</a>
                    </div>
                </div>
                @endforeach
                @endif
                <div id="divPay"></div>
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
            overTime = $('#over_time'),
            dp = $('#dp');

        var CalResult = function() {
            var arr = $('input[id=nominal]').map(function() {
                return this.value;
            }).get();

            let sum = 0;

            arr.forEach(num => {
                sum += parseInt(num);
            })

            let calTotal = parseInt(hargaPaket.val()) * parseInt(lamaSewa.val()) + parseInt(overTime.val()),
                sisa = calTotal - sum;

            totalBiaya.val(calTotal);

            $('#sisa').val(sisa);
        }

        CalResult();

        $("#lama_sewa").on("change", function() {
            CalResult();
        });

        $("#harga_sewa, #lama_sewa, #over_time").on("keyup", function() {
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
            disabled: true,
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
            disabled: true,
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