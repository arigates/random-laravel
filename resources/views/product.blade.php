@extends('adminlte::page')

@section('title', 'Produk')

@section('content_header')
    <h1>Produk</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Produk</h3>
                    <button class="btn btn-primary" id="btn-insert" style="position: absolute;top: 4px;right: 10px;color: white">
                        Tambah +
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="datatable">
                        <thead>
                        <tr>
                            <th data-data="name" data-name="name">Nama</th>
                            <th data-data="min_price" data-name="min_price" data-description="min_price">Harga Awal</th>
                            <th data-data="max_price" data-name="max_price" data-description="max_price">Harga Batas</th>
                            <th data-data="action" data-description="action" data-sortable="false">Aksi</th>
                        </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Insert -->
    <div class="modal fade" id="modal-insert">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Data Produk</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-insert" action="{{ route('products.store') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nama Produk</label>
                            <input type="text" id="name" class="form-control" placeholder="Nama Produk" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="min_price">Harga Awal</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input id="min_price" type="text" class="form-control float-right" placeholder="Harga Awal" name="min_price" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_price">Harga Batas</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input id="max_price" type="text" class="form-control float-right" placeholder="Harga Batas" name="max_price" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan <i id="spinner-insert" style="display: none" class="fas fa-spinner fa-spin"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('/vendor/maskInput/jquery.mask.min.js') }}"></script>
    <script type="text/javascript">
        let table = null
        $(document).ready(function(){
            table = $('#datatable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: '{{ route('products.data') }}',
                aLengthMenu: [
                    [10, 50, 100, 200, -1], //for pagination
                    [10, 50, 100, 200, "All"]
                ],
            })

            $('#min_price, #max_price').inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                digits: 0,
                autoGroup: true,
                prefix: '', //Space after $, this will not truncate the first character.
                rightAlign: false,
            });
        })

        $('#btn-insert').click(function () {
            $("#form-insert")[0].reset()
            $('#modal-insert').modal('show')
        })

        $('#form-insert').submit(function (e) {
            e.preventDefault()

            $('#spinner-insert').show();
            let url = $(this).attr('action')
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: $(this).serializeArray(),
            })
            .done(function(data) {
                table.ajax.reload();
                $('#modal-insert').modal('hide');
            })
            .fail(function(data) {
                console.log(data)
            })
            .always(function() {
                $('#spinner-insert').hide();
                // $('#modal-insert').modal('hide');
            });
        })
    </script>
@stop
