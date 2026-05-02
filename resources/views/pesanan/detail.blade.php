@extends('layouts.Template')

@section('content')
    <style>
        .menu-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; }
        .detail-table { width: 100%; }
        .detail-table th { border-top: none; font-weight: 600; font-size: 0.85rem; border-bottom: 1px solid #eaeaec; }
        .detail-table td { border-bottom: 1px solid #eaeaec; }
        .detail-table tr:last-child td { border-bottom: none; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eaeaec; }
        .info-row:last-child { border-bottom: none; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-receipt"></i>
            </span> Detail Transaksi
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Pesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">#{{ $pesanan->order_id }}</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Item Pesanan</h4>
                    <div class="table-responsive">
                        <table class="table">
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
                                                <span class="font-weight-bold">{{ $d->menu->nama_menu ?? 'Menu dihapus' }}</span>
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
                                    <td colspan="2" class="text-end pt-3 font-weight-bold">Total</td>
                                    <td class="text-end pt-3">
                                        <span class="text-success font-weight-bold" style="font-size: 1.1rem;">
                                            Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @php
            $status = $pesanan->status_bayar;
            $label = $status == 1 ? 'TRANSAKSI LUNAS' : ($status == 2 ? 'TRANSAKSI GAGAL' : 'MENUNGGU PEMBAYARAN');
            $badge = $status == 1 ? 'badge-gradient-success' : ($status == 2 ? 'badge-gradient-danger' : 'badge-gradient-warning');
            $metode = match($pesanan->metode_bayar) {
                1 => 'QRIS', 2 => 'VA / Bank Transfer', 3 => 'Mandiri Bill',
                4 => 'Alfamart / Indomaret', 5 => 'GoPay', 6 => 'ShopeePay',
                7 => 'Kartu Kredit', default => 'Belum dibayar'
            };
        @endphp

        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge {{ $badge }}" style="font-size: 0.9rem; padding: 10px 20px;">{{ $label }}</span>
                    <p class="text-muted small mt-2 mb-4">
                        @if($status == 1) Pembayaran berhasil
                        @elseif($status == 2) Transaksi gagal
                        @else Menunggu pembayaran
                        @endif
                    </p>

                    <div class="text-start">
                        <div class="info-row">
                            <span class="text-muted">Pelanggan</span>
                            <span class="font-weight-bold">{{ $pesanan->nama ?? 'Guest' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-muted">Waktu</span>
                            <span>{{ \Carbon\Carbon::parse($pesanan->timestamp)->format('d M Y H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="text-muted">Metode</span>
                            <span>{{ $metode }}</span>
                        </div>
                    </div>

                    @if($pesanan->status_bayar == 0)
                    <button id="retry-button" class="btn btn-gradient-warning btn-rounded btn-fw mt-3">
                        <i class="mdi mdi-refresh"></i> Bayar Ulang
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
