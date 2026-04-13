@extends('layouts.Template')

@section('content')
    <style>
        :root {
            --border-soft: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
            --accent: #3b82f6;
            --bg-soft: #f8fafc;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        .page-header {
            background: white;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
        }

        .page-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .btn-back {
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            background: white;
            color: var(--text-dark);
            text-decoration: none;
            margin-right: 1rem;
            transition: all 0.2s;
        }

        .btn-back:hover {
            border-color: var(--accent);
            color: var(--accent);
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

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-soft);
        }

        .detail-table th.text-center, .detail-table td.text-center {
            text-align: center;
        }

        .detail-table th.text-end, .detail-table td.text-end {
            text-align: right;
        }

        .detail-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-soft);
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .detail-table tr:last-child td {
            border-bottom: none;
        }

        .menu-img {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .status-success { background: #dcfce7; color: #166534; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-danger { background: #fee2e2; color: #991b1b; }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-soft);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .info-value {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-dark);
            text-align: right;
        }

        .btn-retry {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background: var(--warning);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
        }

        .btn-retry:hover {
            background: #d97706;
        }
    </style>

    <div class="page-header">
        <a href="{{ route('admin.pesanan.index') }}" class="btn-back">
            <i class="mdi mdi-arrow-left"></i>
        </a>
        <h2>Detail Transaksi #{{ $pesanan->order_id }}</h2>
    </div>

    <div class="row g-4">
        {{-- Item Pesanan --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Item Pesanan</h4>
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->details as $d)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $d->menu && $d->menu->path_gambar ? asset('storage/'.$d->menu->path_gambar) : 'https://via.placeholder.com/60' }}" class="menu-img me-2">
                                        <div>
                                            <span class="fw-bold">{{ $d->menu->nama_menu ?? 'Menu dihapus' }}</span>
                                            @if($d->catatan)
                                            <div class="text-muted small">{{ $d->catatan }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">x{{ $d->jumlah }}</td>
                                <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end pt-3 fw-bold">Total</td>
                                <td class="text-end pt-3 fw-bold" style="color: var(--success);">
                                    Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Status & Info --}}
        @php
            $status = $pesanan->status_bayar;
            $label = $status == 1 ? 'TRANSAKSI LUNAS' : ($status == 2 ? 'TRANSAKSI GAGAL' : 'MENUNGGU PEMBAYARAN');
            $statusClass = $status == 1 ? 'status-success' : ($status == 2 ? 'status-danger' : 'status-warning');
            $metode = match($pesanan->metode_bayar) {
                1 => 'QRIS', 2 => 'VA / Bank Transfer', 3 => 'Mandiri Bill',
                4 => 'Alfamart / Indomaret', 5 => 'GoPay', 6 => 'ShopeePay',
                7 => 'Kartu Kredit', default => 'Belum dibayar'
            };
        @endphp

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <span class="status-badge {{ $statusClass }}">{{ $label }}</span>
                    <p class="text-muted small mb-3">
                        @if($status == 1) Pembayaran berhasil
                        @elseif($status == 2) Transaksi gagal
                        @else Menunggu pembayaran
                        @endif
                    </p>

                    <div class="text-start mt-4">
                        <div class="info-row">
                            <span class="info-label">Pelanggan</span>
                            <span class="info-value">{{ $pesanan->nama ?? 'Guest' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Waktu</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($pesanan->timestamp)->format('d M Y H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Metode</span>
                            <span class="info-value">{{ $metode }}</span>
                        </div>
                    </div>

                    @if($pesanan->status_bayar == 0)
                    <button id="retry-button" class="btn-retry">
                        <i class="mdi mdi-refresh me-1"></i> Bayar Ulang
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap --}}
    @if($pesanan->status_bayar == 0 && $pesanan->snap_token)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
    document.getElementById('retry-button')?.addEventListener('click', function () {
        fetch("{{ url('/pesanan/retry/'.$pesanan->order_id) }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        })
        .then(res => res.json())
        .then(data => snap.pay(data.token));
    });
    </script>
    @endif
@endsection