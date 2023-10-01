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
            </div>
        </div>
    </div>
@endsection
