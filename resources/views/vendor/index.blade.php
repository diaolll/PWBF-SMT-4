@extends('layouts.Template')

@section('content')
    <style>
        .table thead th { border-top: none; font-weight: 600; font-size: 0.85rem; }
        .table tbody td { font-size: 0.9rem; }
        .table tbody tr:hover { background: #f8f9fa; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-warning text-white me-2">
                <i class="mdi mdi-store"></i>
            </span> Master Vendor
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Modul 6</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vendor</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Vendor</h4>
                    <p class="card-description">Tambahkan unit kantin baru</p>

                    <form action="{{ route('admin.vendor.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nama Vendor</label>
                            <input type="text" name="nama_vendor" class="form-control" placeholder="Contoh: Kantin A" required>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary btn-rounded btn-fw">
                            <i class="mdi mdi-content-save"></i> Simpan Vendor
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Vendor</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Nama Vendor</th>
                                    <th width="100" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendors as $v)
                                <tr>
                                    <td><span class="badge badge-gradient-info">{{ $v->idvendor }}</span></td>
                                    <td><span class="font-weight-bold">{{ $v->nama_vendor }}</span></td>
                                    <td class="text-center"><span class="badge badge-gradient-success">Aktif</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
