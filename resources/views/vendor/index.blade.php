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
            margin-bottom: 0.25rem;
        }

        .card-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .form-control {
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.4rem;
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

        .badge-id {
            background: var(--bg-soft);
            color: var(--text-muted);
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>

    <div class="page-header">
        <h2><i class="mdi mdi-store me-2"></i>Manajemen Vendor</h2>
    </div>

    <div class="row g-4">
        {{-- Form Tambah --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Vendor</h4>
                    <p class="card-desc mb-3">Tambahkan unit kantin baru</p>
                    <form action="{{ route('admin.vendor.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Vendor</label>
                            <input type="text" name="nama_vendor" class="form-control" placeholder="Contoh: Kantin A" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Vendor --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Daftar Vendor</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Nama Vendor</th>
                                <th width="80" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $v)
                            <tr>
                                <td><span class="badge-id">#{{ $v->idvendor }}</span></td>
                                <td>
                                    <span class="fw-bold">{{ $v->nama_vendor }}</span>
                                </td>
                                <td class="text-center"><span class="badge-active">Aktif</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection