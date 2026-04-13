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

        .order-id {
            color: var(--accent);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .customer-badge {
            width: 28px;
            height: 28px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .price-text {
            color: var(--success);
            font-weight: 600;
        }

        .badge-status {
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .btn-detail {
            padding: 0.4rem 0.85rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid var(--border-soft);
            background: white;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-detail:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
    </style>

    <div class="page-header">
        <h2><i class="mdi mdi-receipt me-2"></i>Riwayat Transaksi</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    @php
                        $status = $p->status_bayar;
                        $label = $status == 1 ? 'LUNAS' : ($status == 2 ? 'GAGAL' : 'PENDING');
                        $badge = $status == 1 ? 'badge-success' : ($status == 2 ? 'badge-danger' : 'badge-warning');
                        $metode = match($p->metode_bayar) {
                            1 => 'QRIS', 2 => 'VA/Transfer', 3 => 'Mandiri Bill',
                            4 => 'Alfamart/Indomaret', 5 => 'GoPay', 6 => 'ShopeePay',
                            7 => 'Kartu Kredit', default => '−'
                        };
                    @endphp
                    <tr>
                        <td><span class="order-id">#{{ $p->order_id }}</span></td>
                        <td>
                            <span class="customer-badge">{{ strtoupper(substr($p->nama ?? 'G', 0, 1)) }}</span>
                            {{ $p->nama ?? 'Guest' }}
                        </td>
                        <td><span class="price-text">Rp {{ number_format($p->total, 0, ',', '.') }}</span></td>
                        <td class="text-muted small">{{ $metode }}</td>
                        <td class="text-center"><span class="badge-status {{ $badge }}">{{ $label }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('pesanan.detail', $p->order_id) }}" class="btn-detail">
                                Detail <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="mdi mdi-database-off d-block mb-2" style="font-size: 2rem;"></i>
                                Belum ada transaksi
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection