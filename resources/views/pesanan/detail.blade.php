@extends('layouts.Template')

@section('content')
<div class="page-header mb-4">
    <h3 class="page-title text-dark fw-bold d-flex align-items-center">
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-white btn-sm border-0 shadow-sm me-3">
            <i class="mdi mdi-arrow-left text-primary"></i>
        </a>
        Detail Transaksi #{{ $pesanan->order_id }}
    </h3>
</div>

<div class="row">

    {{-- ================= LEFT: ITEM ================= --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0" style="border-radius: 20px;">
            <div class="card-body">

                <h4 class="mb-4">Item yang Dibeli</h4>

                <div class="table-responsive">
                    <table class="table table-borderless">

                        <thead>
                            <tr class="text-muted border-bottom">
                                <th>MENU</th>
                                <th class="text-center">QTY</th>
                                <th class="text-end">SUBTOTAL</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($pesanan->details as $d)
                            <tr class="border-bottom">

                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $d->menu && $d->menu->path_gambar ? asset('storage/'.$d->menu->path_gambar) : 'https://via.placeholder.com/60' }}"
                                             style="width:50px;height:50px;object-fit:cover"
                                             class="rounded me-3">

                                        <div>
                                            <b>{{ $d->menu->nama_menu ?? 'Menu Dihapus' }}</b>
                                            <br>
                                            <small class="text-muted">
                                                {{ $d->catatan ?? 'Tanpa catatan' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">x{{ $d->jumlah }}</td>

                                <td class="text-end">
                                    Rp {{ number_format($d->subtotal,0,',','.') }}
                                </td>

                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end pt-4">Total</td>
                                <td class="text-end pt-4 fw-bold text-success">
                                    Rp {{ number_format($pesanan->total,0,',','.') }}
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= RIGHT: STATUS ================= --}}
    @php
        $status = $pesanan->status_bayar;

        $label = match($status) {
            1 => 'TRANSAKSI LUNAS',
            2 => 'TRANSAKSI GAGAL',
            default => 'MENUNGGU PEMBAYARAN',
        };

        $color = match($status) {
            1 => 'success',
            2 => 'danger',
            default => 'warning',
        };

        $metode = match($pesanan->metode_bayar) {
            1 => 'QRIS',
            2 => 'VA / Bank Transfer',
            3 => 'Mandiri Bill',
            4 => 'Alfamart / Indomaret',
            5 => 'GoPay',
            6 => 'ShopeePay',
            7 => 'Kartu Kredit',
            default => 'Belum dibayar',
        };
    @endphp

    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius:20px;">
            <div class="card-body text-center">

                <h4 class="text-{{ $color }}">{{ $label }}</h4>

                <p class="text-muted">
                    @if($status == 1) Pembayaran berhasil
                    @elseif($status == 2) Transaksi gagal
                    @else Menunggu pembayaran
                    @endif
                </p>

                <hr>

                <p><b>Pelanggan:</b><br>{{ $pesanan->nama ?? 'Guest' }}</p>

                <p><b>Waktu:</b><br>
                    {{ \Carbon\Carbon::parse($pesanan->timestamp)->format('d M Y H:i') }}
                </p>

                <p><b>Metode:</b><br>{{ $metode }}</p>

                {{-- 🔥 BAYAR LAGI --}}
            @if($pesanan->status_bayar == 0)
            <button id="retry-button" class="btn btn-warning w-100 mt-3">
                Bayar Ulang
            </button>
            @endif

            </div>
        </div>
    </div>

</div>

{{-- ================= MIDTRANS SNAP ================= --}}
@if($pesanan->status_bayar == 0 && $pesanan->snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
</script>

<script>
document.getElementById('retry-button')?.addEventListener('click', function () {

    fetch("{{ url('/pesanan/retry/'.$pesanan->order_id) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(res => res.json())
    .then(data => {

        snap.pay(data.token);

    });

});
</script>
@endif

@endsection