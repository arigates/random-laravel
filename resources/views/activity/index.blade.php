@extends('adminlte::page')

@section('title', 'Kegiatan')

@section('content_header')
    <h1>Kegiatan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Kegiatan</h3>
                    <a href="{{ route('activities.create') }}" class="btn btn-primary" style="position: absolute;top: 4px;right: 10px;color: white">
                        Tambah +
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="datatable">
                        <thead>
                        <tr>
                            <th data-data="description" data-name="description">Deskripsi</th>
                            <th data-data="date" data-name="date" data-description="date">Tanggal</th>
                            <th data-data="budget" data-name="budget" data-description="budget">Budget</th>
                            <th data-data="document" data-name="document" data-description="document">Dokumen</th>
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
@endsection

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
                ajax: '{{ route('activities.data') }}',
                aLengthMenu: [
                    [10, 50, 100, 200, -1], //for pagination
                    [10, 50, 100, 200, "All"]
                ],
            })
        })

        $('#datatable').on('click', '.btn-delete', function () {
            let id = $(this).data('id')
            let link = '{{ route('activities.destroy', ['activity' => ':activity']) }}'
            link = link.replace(':activity', id)

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
                    .fail(function(data) {
                        if (data.status === 400) {
                            let message = data['responseJSON']['message'];
                            Swal.fire(
                                'Error!',
                                message,
                                'error'
                            )
                        }
                    })
                }
            })
        })
    </script>
@endsection
