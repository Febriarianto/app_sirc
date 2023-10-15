@extends('layouts.master')

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST">
        @method($config['form']->method)
        @csrf
        <div class="row">
            <div class="col-sm-12">
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
                            <label class="form-label" for="name">Nama :</label>
                            <input type="text" class="form-control" value="{{ $data->name ?? '' }}" name="name" id="name" {{ isset($data) && $data->slug == 'super-admin' ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="accordion" id="accordionPanelsStayOpenExample">
                            <div class="accordion-item align-center">
                                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                                    <div class="accordion-body" style="background-color: white">
                                        <div class="table-responsive">
                                            {!! $permissions !!}
                                        </div>
                                    </div>
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
        $('.feature-all').on({
            click: function() {
                let checked = $(this).prop("checked");
                $(this).closest('table').find('.function-item').prop('checked', checked).trigger('change');
                $(this).closest('table').find('.menu-item').prop('checked', checked).trigger('change');
            }
        });
        $('.menu-item').on({
            click: function() {
                let checked = $(this).closest('table').find('.menu-item').length == $(this).closest('table').find('.menu-item:checked').length;
                $(this).closest('table').find('.feature-all').prop('checked', checked).trigger('change');
            }
        });
        $('.function-item').change(function() {
            let checked = $(this).closest('td').find('.function-item').length == $(this).closest('td').find('.function-item:checked').length;
            let checked_menu = $(this).closest('td').find('.function-item:checked').length > 0;
            $(this).closest('tr').find('.function-all').prop('checked', checked).trigger('change');
            $(this).closest('tr').find('.menu-item').prop('checked', checked_menu).trigger('change');
        });
        $('.function-all').on({
            click: function() {
                let checked = $(this).prop("checked");
                $(this).closest('tr').find('.function-item').prop('checked', checked).trigger('change');
            },
            change: function() {
                let checked = $(this).closest('table').find('.function-all').length == $(this).closest('table').find('.function-all:checked').length;
                $(this).closest('table').find('.feature-all').prop('checked', checked).trigger('change');
            }
        });
        $('.function-item').trigger('change');

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