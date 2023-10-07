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
    </script>
@endsection
