@extends('layouts.Template')

@section('content')
    <style>
        .menu-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; }
        .table thead th { border-top: none; font-weight: 600; font-size: 0.85rem; }
        .table tbody td { font-size: 0.9rem; }
        .table tbody tr:hover { background: #f8f9fa; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-food"></i>
            </span> Master Menu
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Modul 6</a></li>
                <li class="breadcrumb-item active" aria-current="page">Menu</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Menu</h4>
                    <p class="card-description">Input menu makanan/minuman</p>

                    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Nama Menu</label>
                            <input type="text" name="nama_menu" class="form-control" placeholder="Nama makanan/minuman" required>
                        </div>
                        <div class="form-group">
                            <label>Harga</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="harga" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Vendor</label>
                            <select name="idvendor" class="form-control" required>
                                <option value="">Pilih Vendor</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Foto</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted">Rasio 1:1 direkomendasikan</small>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary btn-rounded btn-fw">
                            <i class="mdi mdi-plus-circle"></i> Simpan Menu
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Menu</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">Foto</th>
                                    <th>Nama Menu</th>
                                    <th>Vendor</th>
                                    <th width="120" class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menus as $m)
                                <tr>
                                    <td>
                                        <img src="{{ $m->path_gambar ? asset('storage/' . $m->path_gambar) : 'https://via.placeholder.com/60' }}" class="menu-img">
                                    </td>
                                    <td><span class="font-weight-bold">{{ $m->nama_menu }}</span></td>
                                    <td><span class="badge badge-gradient-info">{{ $m->vendor->nama_vendor ?? '-' }}</span></td>
                                    <td class="text-end"><span class="text-success font-weight-bold">Rp {{ number_format($m->harga, 0, ',', '.') }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada menu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
