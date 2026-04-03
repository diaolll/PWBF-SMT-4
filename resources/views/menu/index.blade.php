@extends('layouts.Template')

@section('content')
<div class="page-header">
    <h3 class="page-title fw-bold">
        <span class="page-title-icon bg-gradient-info text-white me-2 shadow-sm">
            <i class="mdi mdi-food"></i>
        </span> Master Menu Makanan
    </h3>
</div>

<div class="row">
    {{-- Form Input Menu (Susunan ke bawah) --}}
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body">
                <h4 class="card-title mb-4">Input Menu Baru</h4>
                <form class="forms-sample" action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="fw-bold">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control form-control-lg border-light" 
                               style="background: #f8f9fa; border-radius: 10px;" placeholder="Masukkan nama makanan/minuman..." required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text bg-info text-white border-0" style="border-radius: 10px 0 0 10px;">Rp</span>
                            <input type="number" name="harga" class="form-control form-control-lg border-light" 
                                   style="background: #f8f9fa; border-radius: 0 10px 10px 0;" placeholder="0" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold">Vendor Pemilik</label>
                        <select name="idvendor" class="form-select form-select-lg border-light" 
                                style="background: #f8f9fa; border-radius: 10px;" required>
                            <option value="">Pilih Vendor...</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-bold">Foto Menu</label>
                        <input type="file" name="gambar" class="form-control border-light" 
                               style="background: #f8f9fa; border-radius: 10px;" accept="image/*">
                        <small class="text-muted mt-1 d-block italic">*Gunakan foto rasio 1:1 untuk hasil terbaik</small>
                    </div>

                    <button type="submit" class="btn btn-gradient-info btn-lg w-100 fw-bold shadow-sm" style="border-radius: 10px;">
                        <i class="mdi mdi-plus-circle me-2"></i>Simpan Menu
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Katalog Menu --}}
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body">
                <h4 class="card-title">Katalog Menu Saat Ini</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Foto</th>
                                <th class="border-0">Nama Menu</th>
                                <th class="border-0">Vendor</th>
                                <th class="border-0 text-end pe-4">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $m)
                            <tr>
                                <td class="py-3">
                                    <img src="{{ $m->path_gambar ? asset('storage/' . $m->path_gambar) : 'https://via.placeholder.com/60' }}" 
                                         class="rounded-3 shadow-sm" style="width: 55px; height: 55px; object-fit: cover;">
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark d-block">{{ $m->nama_menu }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline-info px-2 py-1" style="font-size: 0.75rem;">
                                        {{ $m->vendor->nama_vendor ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    Rp {{ number_format($m->harga, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada menu terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection