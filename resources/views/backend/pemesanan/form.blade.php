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
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keberangkatan">Tgl Berangkat :</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="keberangkatan" name="keberangkatan" placeholder="Masukan Tanggal Keberangkatan" value="{{ $data->keberangkatan ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keberangkatan">Estimasi Jam :</label>
                            <div class="col-sm-9">
                                <input type="time" class="form-control" id="estimasi_time" name="estimasi_time" placeholder="Masukan Estimasi Jam" value="{{ $data->estimasi_time ?? '' }}">
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
                            <option value='titip' {{$p->tipe == 'titip' ? 'selected' : ''}}>Titip</option>
                            <option value='pelunasan' {{$p->tipe == 'pelunasan' ? 'selected' : ''}}>Pelunasan</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-lab" for="harga_sewa">Nominal</label>
                        <input type="text" class="form-control" id="harga_sewa" name="nominalP[]" value="{{ $p->nominal ?? '0'}}">
                    </div>
                    <div class="col-sm-3">
                        <label class="control-lab" for="harga_sewa">Metode</label>
                        <select name='metodeP[]' id='metode' class='form-control'>
                            <option value=''>.:Pilih:.</option>
                            <option value='cash' {{$p->metode == 'cash' ? 'selected' : ''}}>Cash</option>
                            <option value='transfer' {{$p->metode == 'transfer' ? 'selected' : ''}}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-lab" for="harga_sewa">File</label>
                        <input type="file" class="form-control" id="" name="fileP[]">
                        <a href="{{ asset ('storage/buktiTrf/'.$p->file)}}" target="_blank">{{$p->file}}</a>
                    </div>
                </div>
                @endforeach
                @endif
                <div id="divPay"></div>
            </div>
            <div class="card-footer">
                <div class="btn-group float-right" role="group" aria-label="Basic outlined example">
                    <a href="{{route('pemesanan.index')}}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-rotate-left"></i> Kembali</a>
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

        $('#add').on('click', add);

        function add() {
            var new_input = "<div class='form-group row'><div class='col-sm-3'><label class='control-lab' for='harga_sewa'>Jenis Pembayaran</label><select name='tipe[]' id='tipe' class='form-control'><option value=''>.:Pilih:.</option><option value='dp'>DP</option><option value='titip' >Titip</option><option value='pelunasan'>Pelunasan</option></select></div><div class='col-sm-3'><label class='control-lab' for='nominal'>Nominal</label><input type='text' class='form-control' id='nominal' name='nominal[]'></div><div class='col-sm-3'><label class='control-lab' for='metode'>Metode</label><select name='metode[]' id='metode' class='form-control'><option value=''>.:Pilih:.</option><option value='cash'>Cash</option><option value='transfer'>Transfer</option></select></div><div class='col-sm-2'><label class='control-lab' for='file'>File</label><input type='file' class='form-control' id='file' name='file[]'></div><div class='col-sm-1'><label class='control-lab' for='harga_sewa'></label><br><button class='btn btn-danger' onclick='return this.parentNode.parentNode.remove();'><i class='fas fa-trash'></i></button></div></div>";
            $('#divPay').append(new_input);
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
                        Swal.fire({
                            title: "Success",
                            text: "Data Sudah Tersimpan!",
                            icon: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ya",
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.value) {
                                location.href = `{{route('pemesanan.index')}}`
                            }
                        });
                        setTimeout(function() {
                            if (response.redirect === "" || response.redirect === "reload") {
                                location.reload();
                            } else {
                                window.open(response.redirect, '_blank');
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