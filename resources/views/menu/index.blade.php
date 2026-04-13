@extends('layouts.Template')

@section('content')
    <style>
        :root {
            --border-soft: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
            --accent: #3b82f6;
            --bg-soft: #f8fafc;
        }

        .page-header {
            background: white;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-soft);
        }

        .page-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: 12px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group-text {
            background: var(--accent);
            color: white;
            border: 1px solid var(--accent);
            border-radius: 8px 0 0 8px;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }

        .btn {
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--bg-soft);
        }

        .data-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .data-table td {
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--border-soft);
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .data-table tbody tr:hover {
            background: var(--bg-soft);
        }

        .menu-img {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
        }

        .vendor-badge {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .price-text {
            color: #166534;
            font-weight: 600;
        }
    </style>

    <div class="page-header">
        <h2><i class="mdi mdi-food me-2"></i>Master Menu</h2>
    </div>

    <div class="row g-4">
        {{-- Form Input --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Menu</h4>
                    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Menu</label>
                            <input type="text" name="nama_menu" class="form-control" placeholder="Nama makanan/minuman" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga" class="form-control" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vendor</label>
                            <select name="idvendor" class="form-select" required>
                                <option value="">Pilih Vendor</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted">Rasio 1:1 direkomendasikan</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-plus me-1"></i> Simpan Menu
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Menu --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Menu</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="60">Foto</th>
                                <th>Nama Menu</th>
                                <th>Vendor</th>
                                <th width="100" class="text-end">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $m)
                            <tr>
                                <td>
                                    <img src="{{ $m->path_gambar ? asset('storage/' . $m->path_gambar) : 'https://via.placeholder.com/60' }}" class="menu-img">
                                </td>
                                <td><span class="fw-bold">{{ $m->nama_menu }}</span></td>
                                <td><span class="vendor-badge">{{ $m->vendor->nama_vendor ?? '-' }}</span></td>
                                <td class="text-end"><span class="price-text">{{ number_format($m->harga, 0, ',', '.') }}</span></td>
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
@endsection