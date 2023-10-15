@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-6 col-lg-6">
            <form id="changeHierarchy" class="formStore" action="{{ route('pages.changeHierarchy') }}">
                @method('POST')
                @csrf
                <div class="card">
                    <div class="card-header justify-content-between">
                        <div class="header-title">
                            <div class="row">
                                <h4 class="card-title">Struktur Menu Pages </h4>
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
                                <h4 class="card-title">Tambah Pages </h4>
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
                        <div class="mb-3">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="ex: Menu Manager" value="{{ (isset($data) ?? $data->title) ? $data->title : '' }}" />
                        </div>
                        <div class="mb-3">
                            <label>Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="ex: menu-manager" value="{{ (isset($data) ?? $data->slug) ? $data->slug : '' }}" />
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

        $('#menuList').nestable({
            maxDepth: 2
        }).on('change', function() {
            let json_values = window.JSON.stringify($(this).nestable('serialize'));
            $("#output").val(json_values);
            $("#changeHierarchy [type='submit']").fadeIn();
        }).nestable('collapseAll');

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
                url = `{{ route("pages.index") }}/` + pk;
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