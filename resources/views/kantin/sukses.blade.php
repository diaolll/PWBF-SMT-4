@extends('layouts.Template')

@section('content')
    <style>
        .success-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .success-icon i { font-size: 3rem; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #eaeaec; }
        .detail-row:last-child { border-bottom: none; }
        .qr-box { border: 2px dashed #eaeaec; border-radius: 12px; padding: 20px; display: inline-block; }
        .qr-box img { border-radius: 8px; max-width: 180px; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-check-circle"></i>
            </span> Pembayaran Berhasil
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('kantin.index') }}">Kantin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sukses</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body text-center">
                    <div class="success-icon">
                        <i class="mdi mdi-check"></i>
                    </div>

                    <h3 class="card-title mb-1">Pembayaran Berhasil!</h3>
                    <p class="text-muted mb-4">Terima kasih {{ $pesanan->nama ?? 'Customer' }}, pesanan sedang diproses</p>

                    @if($pesanan)
                    {{-- Order Details --}}
                    <div class="bg-light p-4 rounded mb-4">
                        <div class="detail-row">
                            <span class="text-muted">Order ID</span>
                            <span class="font-weight-bold">{{ $pesanan->order_id ?? '-' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="text-muted">Nama</span>
                            <span>{{ $pesanan->nama ?? 'Customer' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="text-muted">Total</span>
                            <span class="text-success font-weight-bold" style="font-size: 1.2rem;">Rp {{ number_format($pesanan->total ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Items --}}
                    @if($pesanan->details && $pesanan->details->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-start mb-3">Item yang Dibeli</h6>
                        <div class="bg-light p-3 rounded">
                            @foreach($pesanan->details as $detail)
                            <div class="detail-row">
                                <span>{{ $detail->menu->nama ?? 'Menu' }}</span>
                                <span>{{ $detail->jumlah }}x × Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endif

                    {{-- QR Code --}}
                    <div class="qr-box mb-4">
                        <p class="text-muted small mb-3">Scan QR Code untuk melihat detail pesanan</p>
                        @if($qrBase64 ?? false)
                        <img id="qrImage" src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code">
                        <p class="text-muted small mt-2">atau tunjukkan ke kasir</p>
                        @else
                        <p class="text-danger small">QR Code tidak tersedia</p>
                        @endif
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('kantin.orders') }}" class="btn btn-gradient-primary btn-rounded">
                            <i class="mdi mdi-history"></i> Pesanan Saya
                        </a>
                        <a href="{{ route('kantin.index') }}" class="btn btn-gradient-light btn-rounded">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($pesanan)
            saveOrderToStorage();
            @endif
        });

        function saveOrderToStorage() {
            const orderData = {
                order_id: '{{ $pesanan->order_id ?? '' }}',
                idpesanan: '{{ $pesanan->idpesanan ?? '' }}',
                nama: '{{ $pesanan->nama ?? 'Customer' }}',
                total: {{ $pesanan->total ?? 0 }},
                status_bayar: {{ $pesanan->status_bayar ?? 0 }},
                metode_bayar: {{ $pesanan->metode_bayar ?? 0 }},
                qrBase64: document.getElementById('qrImage')?.src || '',
                timestamp: new Date().toISOString(),
                items: [
                    @if($pesanan->details && $pesanan->details->count() > 0)
                        @foreach($pesanan->details as $detail)
                        { nama: '{{ $detail->menu->nama ?? 'Menu' }}', jumlah: {{ $detail->jumlah ?? 0 }}, subtotal: {{ $detail->subtotal ?? 0 }} },
                        @endforeach
                    @endif
                ]
            };
            let orders = JSON.parse(localStorage.getItem('kantin_orders') || '[]');
            // Remove duplicate and add new
            orders = orders.filter(o => o.order_id !== orderData.order_id);
            orders.unshift(orderData);
            if (orders.length > 20) orders = orders.slice(0, 20);
            localStorage.setItem('kantin_orders', JSON.stringify(orders));
        }
    </script>
@endsection
