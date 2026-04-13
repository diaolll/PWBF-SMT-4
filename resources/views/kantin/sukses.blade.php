@extends('layouts.Template')

@section('content')
    <style>
        :root {
            --border-soft: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
            --success: #10b981;
            --success-light: #d1fae5;
        }

        .success-page {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .success-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            background: var(--success-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .success-icon i {
            font-size: 2.5rem;
            color: var(--success);
        }

        .success-title {
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .success-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .success-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-soft);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .detail-value {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .detail-value.total {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--success);
        }

        /* Item List Styles */
        .items-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .items-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0;
            border-bottom: 1px dashed var(--border-soft);
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            font-size: 0.9rem;
            color: var(--text-dark);
            flex: 1;
        }

        .item-qty {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-right: 1rem;
        }

        .item-price {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .empty-items {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 1rem;
        }

        .qr-section {
            border: 1px dashed var(--border-soft);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .qr-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }

        .qr-section img {
            border-radius: 8px;
            max-width: 160px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--text-dark);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #334155;
            color: white;
        }
    </style>

    <div class="success-page">
        <div class="success-card">
            <div class="success-icon">
                <i class="mdi mdi-check"></i>
            </div>

            <h3 class="success-title">Pembayaran Berhasil</h3>
            <p class="success-subtitle">Terima kasih {{ $pesanan->nama ?? 'Customer' }}, pesanan sedang diproses</p>

            @if($pesanan)
            {{-- Order Details --}}
            <div class="success-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value">{{ $pesanan->order_id ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nama</span>
                    <span class="detail-value">{{ $pesanan->nama ?? 'Customer' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total</span>
                    <span class="detail-value total">Rp {{ number_format($pesanan->total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Items Purchased --}}
            <div class="items-section">
                <p class="items-title">Item yang Dibeli</p>
                @if($pesanan->details && $pesanan->details->count() > 0)
                    @foreach($pesanan->details as $detail)
                        <div class="item-row">
                            <span class="item-name">{{ $detail->menu->nama ?? 'Menu' }}</span>
                            <span class="item-qty">{{ $detail->jumlah }}x</span>
                            <span class="item-price">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="empty-items">Tidak ada item dalam pesanan</p>
                @endif
            </div>
            @endif

            <div class="qr-section">
                <p class="qr-label">Scan QR Code untuk melihat detail pesanan</p>
                @if($qrBase64 ?? false)
                    <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code">
                    <p class="text-muted small mt-2">atau tunjukkan ke kasir</p>
                @else
                    <p class="text-danger small">QR Code tidak tersedia</p>
                @endif
            </div>

            <a href="{{ route('kantin.index') }}" class="btn-back">
                <i class="mdi mdi-arrow-left"></i>
                Kembali ke Kantin
            </a>
        </div>
    </div>
@endsection