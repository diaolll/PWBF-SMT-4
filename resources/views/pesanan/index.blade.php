@extends('layouts.Template')

@section('content')
    <style>
        .table thead th { border-top: none; font-weight: 600; font-size: 0.85rem; }
        .table tbody td { font-size: 0.9rem; }
        .table tbody tr:hover { background: #f8f9fa; }
        .customer-badge { width: 32px; height: 32px; background: linear-gradient(135deg, #716aca, #5a52b5); color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-receipt"></i>
            </span> Riwayat Transaksi
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Modul 6</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pesanan</li>
            </ul>
        </nav>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.vendor.qrcode') }}" class="btn btn-gradient-primary btn-rounded btn-fw">
            <i class="mdi mdi-qrcode"></i> Scan QR Customer
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
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
                            $badge = $status == 1 ? 'badge-gradient-success' : ($status == 2 ? 'badge-gradient-danger' : 'badge-gradient-warning');
                            $metode = match($p->metode_bayar) {
                                1 => 'QRIS', 2 => 'VA/Transfer', 3 => 'Mandiri Bill',
                                4 => 'Alfamart/Indomaret', 5 => 'GoPay', 6 => 'ShopeePay',
                                7 => 'Kartu Kredit', default => '−'
                            };
                        @endphp
                        <tr>
                            <td><span class="text-primary font-weight-bold">#{{ $p->order_id }}</span></td>
                            <td>
                                <span class="customer-badge">{{ strtoupper(substr($p->nama ?? 'G', 0, 1)) }}</span>
                                {{ $p->nama ?? 'Guest' }}
                            </td>
                            <td><span class="text-success font-weight-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</span></td>
                            <td class="text-muted small">{{ $metode }}</td>
                            <td class="text-center"><span class="badge {{ $badge }}">{{ $label }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('pesanan.detail', $p->order_id) }}" class="btn btn-gradient-primary btn-sm btn-rounded">
                                    <i class="mdi mdi-information-outline"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="mdi mdi-database-off" style="font-size: 2rem;"></i>
                                <p class="mt-2">Belum ada transaksi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
