@extends('layouts.Template')

@section('content')
    <style>
        .table thead th { border-top: none; font-weight: 600; font-size: 0.85rem; }
        .table tbody td { font-size: 0.9rem; }
        .table tbody tr:hover { background: #f8f9fa; }
    </style>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-package-variant-closed"></i>
            </span> Manajemen Barang
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Barang</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Input Barang</h4>
                    <p class="card-description">Tambah barang baru ke sistem</p>

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
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="harga" class="form-control" required placeholder="Harga">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary btn-rounded btn-fw">
                            <i class="mdi mdi-plus-circle"></i> Simpan Barang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('barang.pdf') }}" method="POST" target="_blank">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Daftar Barang</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('barang.scan') }}" class="btn btn-gradient-primary btn-rounded">
                                    <i class="mdi mdi-barcode-scan"></i> Scan Barcode
                                </a>
                                <button type="submit" class="btn btn-gradient-danger btn-rounded">
                                    <i class="mdi mdi-printer"></i> Cetak PDF
                                </button>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded mb-3">
                            <label class="mb-2">Pengaturan PDF</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Kolom Mulai (X)</label>
                                    <input type="number" name="x" class="form-control" value="1" min="1" max="5">
                                </div>
                                <div class="col-md-6">
                                    <label>Baris Mulai (Y)</label>
                                    <input type="number" name="y" class="form-control" value="1" min="1" max="8">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="checkAll"></th>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangs as $item)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $item->id_barang }}" class="item-checkbox"></td>
                                        <td><span class="badge badge-gradient-info">{{ $item->id_barang }}</span></td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-success font-weight-bold">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('barang.edit', $item->id_barang) }}" class="btn btn-gradient-warning btn-sm btn-rounded">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-gradient-danger btn-sm btn-rounded" onclick="if(confirm('Yakin hapus barang ini?')) fetch('{{ route('barang.destroy', $item->id_barang) }}', {method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
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
        document.getElementById('checkAll').addEventListener('change', function() {
            document.querySelectorAll('.item-checkbox').forEach(c => c.checked = this.checked);
        });
    </script>
@endsection
