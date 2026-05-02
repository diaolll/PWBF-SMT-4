@extends('layouts.Template')

@section('content')
    <style>
        .customer-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
        .table thead th { border-top: none; border-bottom-width: 1px; font-weight: 600; font-size: 0.85rem; }
        .table tbody td { font-size: 0.9rem; }
        .table tbody tr:hover { background: #f8f9fa; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-account-multiple"></i>
            </span> Data Customer
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Modul 7</a></li>
                <li class="breadcrumb-item active" aria-current="page">Customer</li>
            </ul>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('customer.tambah1') }}" class="btn btn-gradient-primary btn-rounded btn-fw">
                <i class="mdi mdi-camera"></i> Tambah (BLOB)
            </a>
            <a href="{{ route('customer.tambah2') }}" class="btn btn-gradient-info btn-rounded btn-fw">
                <i class="mdi mdi-image"></i> Tambah (File)
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th width="60">Foto</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Provinsi</th>
                            <th>Kota</th>
                            <th>Kecamatan</th>
                            <th>Kelurahan/Kodepos</th>
                            <th width="60">Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>
                                @if($c->foto_path)
                                    <img src="{{ asset('storage/' . $c->foto_path) }}" class="customer-img">
                                @elseif($c->foto_blob)
                                    <img src="data:image/png;base64,{{ base64_encode($c->foto_blob) }}" class="customer-img">
                                @else
                                    <span class="text-muted">−</span>
                                @endif
                            </td>
                            <td>{{ $c->nama }}</td>
                            <td class="text-muted">{{ $c->alamat ?? '−' }}</td>
                            <td>{{ $c->provinsi ?? '−' }}</td>
                            <td>{{ $c->kota ?? '−' }}</td>
                            <td>{{ $c->kecamatan ?? '−' }}</td>
                            <td>{{ $c->kodepos_kelurahan ?? '−' }}</td>
                            <td>
                                @if($c->foto_path)
                                    <span class="badge badge-gradient-success">File</span>
                                @else
                                    <span class="badge badge-gradient-info">BLOB</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="mdi mdi-account-off" style="font-size: 2rem;"></i>
                                <p class="mt-2">Belum ada data customer</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
