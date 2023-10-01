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

    <!-- Modal Edit -->
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Data Produk</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-edit" action="#" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="form-group">
                            <label for="name_edit">Nama Produk</label>
                            <input type="text" id="name_edit" class="form-control" placeholder="Nama Produk" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="min_price_edit">Harga Awal</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input id="min_price_edit" type="text" class="form-control float-right" placeholder="Harga Awal" name="min_price" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_price_edit">Harga Batas</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input id="max_price_edit" type="text" class="form-control float-right" placeholder="Harga Batas" name="max_price" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan <i id="spinner-edit" style="display: none" class="fas fa-spinner fa-spin"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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

            $('#min_price, #max_price, #min_price_edit, #max_price_edit').inputmask("currency", {
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

        $('#datatable').on('click', '.btn-edit', function () {
            let id = $(this).data('id')
            let link = '{{ route('products.show', ['product' => ':product']) }}'
            link = link.replace(':product', id)

            $('#form-edit').attr('action', link)

            $.ajax({
                url: link,
                type: 'GET',
                dataType: 'json',
            })
            .done(function(data) {
                $.each(data, function(index, el) {
                    $('#'+index+'_edit').val(el);
                });
            })
            .always(function() {
                $('#modal-edit').modal('show');
            });
        })

        $('#form-edit').submit(function (e) {
            e.preventDefault()

            $('#spinner-edit').show();
            let url = $(this).attr('action')
            $.ajax({
                url: url,
                type: 'PATCH',
                dataType: 'json',
                data: $(this).serializeArray(),
            })
            .done(function(data) {
                table.ajax.reload();
                $('#modal-edit').modal('hide');
            })
            .fail(function(data) {
                console.log(data)
            })
            .always(function() {
                $('#spinner-edit').hide();
            });
        })

        $('#datatable').on('click', '.btn-delete', function () {
            let id = $(this).data('id')
            let link = '{{ route('products.destroy', ['product' => ':product']) }}'
            link = link.replace(':product', id)

            Swal.fire({
                title: 'Anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batalkan',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: link,
                        type: 'DELETE',
                        dataType: 'json',
                    })
                    .done(function(data) {
                        table.ajax.reload();

                        Swal.fire(
                            'Data terhapus!',
                            'Data berhasil dihapus.',
                            'success'
                        )
                    })
                }
            })
        })
    </script>
@stop
