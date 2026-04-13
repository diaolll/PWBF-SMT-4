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

        .action-bar {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .btn-action {
            padding: 0.6rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid var(--border-soft);
            background: white;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-action:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-action.primary {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .btn-action.primary:hover {
            background: #2563eb;
            color: white;
        }

        .data-table-wrapper {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--bg-soft);
        }

        .data-table th {
            padding: 0.85rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .data-table th.text-center {
            text-align: center;
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

        .customer-img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        .badge-type {
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .badge-type.file {
            background: #dcfce7;
            color: #166534;
        }

        .badge-type.blob {
            background: #dbeafe;
            color: #1e40af;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            color: #166534;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>

    <div class="page-header">
        <h2><i class="mdi mdi-account-multiple me-2"></i>Data Customer</h2>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="action-bar">
        <a href="{{ route('customer.tambah1') }}" class="btn-action primary">
            <i class="mdi mdi-camera me-1"></i> Tambah (BLOB)
        </a>
        <a href="{{ route('customer.tambah2') }}" class="btn-action primary">
            <i class="mdi mdi-image me-1"></i> Tambah (File)
        </a>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th width="70">Foto</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Provinsi</th>
                    <th>Kota</th>
                    <th>Kecamatan</th>
                    <th>Kelurahan/Kodepos</th>
                    <th width="70" class="text-center">Tipe</th>
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
                    <td class="text-center">
                        @if($c->foto_path)
                            <span class="badge-type file">File</span>
                        @else
                            <span class="badge-type blob">BLOB</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">Belum ada data customer</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection