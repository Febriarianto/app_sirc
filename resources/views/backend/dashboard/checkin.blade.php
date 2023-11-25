@extends('layouts.master')

@section('content')
<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Input Kode</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <input type="text" class="form-control" name="kode" id="kode" autofocus>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>No Kendaraan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Keberangkatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {


        var dt = $('#dt').DataTable({
            searching: false,
            paging: false,
            info: false,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
            url: `{{ route('dashboard.checkin') }}`,
            data: function(d) {
                let text =  $('#kode').val();
                const myArray = text.split("-");
                let id = myArray[1];
                d.kode = id;
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    render: function(data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'kendaraan',
                    name: 'kendaraan'
                },
                {
                    data: 'penyewa',
                    name: 'penyewa'
                },
                {
                    data: 'keberangkatan',
                    name: 'keberangkatan'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $('#kode').on('keyup', function() {
            dt.draw();
        })

    //     $('#kode').on('keyup', function() {
    //     dt.ajax.reload();
    // })
    });
</script>
@endsection