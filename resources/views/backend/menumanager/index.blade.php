@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="alert alert-warning text-center" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                Konfigurasi yang salah dapat menyebabkan pengguna tidak dapat akses masuk ke halaman
            </div>
        </div>
        <div class="col-sm-6 col-lg-6">
            <form id="changeHierarchy" class="formStore" action="{{ route('menu-manager.changeHierarchy') }}">
                @method('POST')
                @csrf
                <div class="card">
                    <div class="card-header justify-content-between">
                        <div class="header-title">
                            <div class="row">
                                <h4 class="card-title">Struktur Menu </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dd" id="menuList">
                            {!! $sortable !!}
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <input type="hidden" id="output" name="hierarchy" />
                        <button type="submit" class="btn btn-sm btn-warning" style="display: none"><i class="fa-solid fa-floppy-disk"></i> Ubah
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-6 col-lg-6">
            <form id="formMenumanager" action="{{ $config['form']->action }}">
                @method($config['form']->method)
                @csrf
                <div class="card">
                    <div class="card-header justify-content-between ">
                        <div class="header-title">
                            <div class="row">
                                <h4 class="card-title">Tambah Hak Akses </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="errorCreate" class="mb-3" style="display:none;">
                            <div class="alert alert-danger" role="alert">
                                <div class="alert-icon"><i class="flaticon-danger text-danger"></i></div>
                                <div class="alert-text">
                                </div>
                            </div>
                        </div>
                        <fieldset class="mb-3">
                            <h5 class="font-size-14 mb-2">Pilih Menu <span class="text-danger">*</span></h5>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="type" class="form-check-input" value="module" id="exampleRadio1" {{ (!isset($data) ? 'checked' : ($data->type == 'module' ? 'checked' : '' ))  }}>
                                <label class="form-check-label" for="exampleRadio1">Module</label>
                            </div>
                            <div class="mb-3 form-check form-check-inline">
                                <input type="radio" name="type" class="form-check-input" value="header" id="exampleRadio2" {{ (isset($data->type) && $data->type == 'header') ? 'checked' : '' }}>
                                <label class="form-check-label" for="exampleRadio2">Header</label>
                            </div>
                            <div class="mb-3 form-check form-check-inline">
                                <input type="radio" name="type" class="form-check-input" value="line" id="exampleRadio3" {{ (isset($data->type) && $data->type == 'line') ? 'checked' : '' }}>
                                <label class="form-check-label" for="exampleRadio3">Line</label>
                            </div>
                            <div class="mb-3 form-check form-check-inline">
                                <input type="radio" name="type" class="form-check-input" value="static" id="exampleRadio4" {{ (isset($data->type) && $data->type == 'static') ? 'checked' : '' }}>
                                <label class="form-check-label" for="exampleRadio4">Static</label>
                            </div>
                        </fieldset>
                        <div class="mb-3" style="{{ (isset($data) && $data->type == 'line') ? 'display:none;' : '' }}">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="ex: Menu Manager" value="{{ (isset($data) ?? $data->title) ? $data->title : '' }}" />
                        </div>
                        <div class="mb-3" style="{{ (isset($data) && ($data->type == 'line' || $data->type == 'header' || $data->type == 'static' )) ? 'display:none;' : '' }}">
                            <label>Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="ex: menu-manager" value="{{ (isset($data) ?? $data->slug) ? $data->slug : '' }}" />
                        </div>
                        <div class="mb-3" style="{{ (isset($data) && ($data->type == 'line' || $data->type == 'header' || $data->type == 'static')) ? 'display:none;' : '' }}">
                            <label>Path Url</label>
                            <input type="text" name="path_url" class="form-control" placeholder="ex: /backend/dashboard" value="{{ (isset($data) ?? $data->path_url) ? $data->path_url : '' }}" />
                        </div>
                        <div class="mb-3" style="{{ (isset($data) && $data->type == 'line') ? 'display:none;' : '' }}">
                            <label>Icon</label>
                            <input type="text" name="icon" class="form-control" placeholder="ex: fas fa-address-card" value="{{ (isset($data) ?? $data->icon) ? $data->icon : '' }}" />
                        </div>
                    </div>
                    <div class="card-footer justify-content-between border-top">
                        <button type="submit" class="btn btn-primary float-end">Simpan <i class="fa-solid fa-floppy-disk"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        let radioCreate = document.querySelectorAll('input[name="type"]');

        $('#menuList').nestable({
            maxDepth: 2
        }).on('change', function() {
            let json_values = window.JSON.stringify($(this).nestable('serialize'));
            $("#output").val(json_values);
            $("#changeHierarchy [type='submit']").fadeIn();
        }).nestable('collapseAll');

        radioCreate.forEach(el => {
            el.addEventListener('change', () => {
                let title = document.querySelector('input[name="title"]').parentNode;
                let path_url = document.querySelector('input[name="path_url"]').parentNode;
                let icon = document.querySelector('input[name="icon"]').parentNode;
                let slug = document.querySelector('input[name="slug"]').parentNode;
                if (el.checked && el.value === 'header' || el.checked && el.value === 'static') {
                    // document.querySelector('#createPage').style.display = '';
                    title.style.display = '';
                    path_url.style.display = 'none';
                    icon.style.display = '';
                    slug.style.display = 'none';
                    title.value = '';
                    path_url.value = '';
                    icon.value = '';
                    slug.value = '';
                } else if (el.checked && el.value === 'line') {
                    title.style.display = 'none';
                    path_url.style.display = 'none';
                    icon.style.display = 'none';
                    slug.style.display = 'none';
                    title.value = '';
                    path_url.value = '';
                    icon.value = '';
                    slug.value = '';
                } else {
                    // document.querySelector('#createPage').style.display = 'none';
                    title.style.display = '';
                    path_url.style.display = '';
                    icon.style.display = '';
                    slug.style.display = '';
                    title.children.value = '';
                    path_url.value = '';
                    icon.value = '';
                    slug.value = '';
                }
            });
        });


        $("#formMenumanager").submit(function(e) {
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

        $("#changeHierarchy").submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btnSubmit = form.find("[type='submit']");
            let btnSubmitHtml = btnSubmit.html();
            let url = form.attr("action");
            let data = new FormData(this);
            $.ajax({
                beforeSend: function() {
                    btnSubmit.addClass("disabled").html("<i class='bx bx-hourglass bx-spin font-size-16 align-middle me-2'></i> Loading ...").prop("disabled", "disabled");
                },
                cache: false,
                processData: false,
                contentType: false,
                type: "POST",
                url: url,
                data: data,
                success: function(response) {
                    let errorCreate = $('#errorCreate');
                    errorCreate.css('display', 'none');
                    errorCreate.find('.alert-text').html('');
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    if (response.status === "success") {
                        toastr.success(response.message, 'Success !');
                        setTimeout(function() {
                            if (!response.redirect || response.redirect === "reload") {
                                location.reload();
                            } else {
                                location.href = response.redirect;
                            }
                        }, 1000);
                    } else {
                        $.each(response.error, function(key, value) {
                            errorCreate.css('display', 'block');
                            errorCreate.find('.alert-text').append('<span style="display: block">' + value + '</span>');
                        });
                        toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
                    }
                },
                error: function(response) {
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    toastr.error(response.responseJSON.message, 'Failed !');
                }
            });
        });

        $(".btn-delete").click(function(e) {
            let pk = $(this).data('id'),
                url = `{{ route("menu-manager.index") }}/` + pk;
            console.log(pk);
            Swal.fire({
                title: "Anda Yakin ?",
                text: "Data tidak dapat dikembalikan setelah di hapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Tidak, Batalkan",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        error: function(response) {
                            toastr.error(response, 'Failed !');
                        },
                        success: function(response) {
                            if (response.status === "success") {
                                toastr.success(response.message, 'Success !');
                                location.reload();
                            } else {
                                toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection