@extends('layouts.Template')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-package-variant-closed"></i>
        </span> Manajemen Barang
    </h3>
</div>

<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Input Barang</h4>
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama Barang">
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-gradient-primary text-white">Rp</span>
                            </div>
                            <input type="number" name="harga" class="form-control" required placeholder="Harga">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary w-100">Simpan Barang</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                {{-- KUNCI UTAMA: Tag Form ini harus membungkus koordinat dan tabel --}}
                <form action="{{ route('barang.pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Daftar Barang</h4>
                        <button type="submit" class="btn btn-gradient-danger btn-icon-text">
                            <i class="mdi mdi-printer btn-icon-prepend"></i> Cetak PDF Terpilih
                        </button>
                    </div>

                    <div class="row mb-4 p-3 bg-light rounded mx-0">
                        <div class="col-6 col-md-4">
                            <label class="small font-weight-bold">Kolom Mulai (X: 1-5)</label>
                            <input type="number" name="x" class="form-control form-control-sm" value="1" min="1" max="5">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small font-weight-bold">Baris Mulai (Y: 1-8)</label>
                            <input type="number" name="y" class="form-control form-control-sm" value="1" min="1" max="8">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="tableBarang">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkAll"></th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangs as $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $item->id_barang }}" class="item-checkbox">
                                    </td>
                                    <td><label class="badge badge-gradient-info">{{ $item->id_barang }}</label></td>
                                    <td>{{ $item->nama }}</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('barang.edit', $item->id_barang) }}" class="btn btn-inverse-warning btn-sm border-0">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            {{-- Tombol hapus pakai form kecil atau script --}}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script sederhana Check All
    document.getElementById('checkAll').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection