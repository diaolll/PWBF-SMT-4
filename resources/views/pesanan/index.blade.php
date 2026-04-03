@extends('layouts.Template')

@section('content')
<div class="page-header">
    <h3 class="page-title d-flex align-items-center">
        <span class="page-title-icon bg-gradient-success text-white me-2">
            <i class="mdi mdi-receipt"></i>
        </span>
        Riwayat Transaksi
    </h3>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        {{-- ================= HEADER ================= --}}
                        <thead class="text-muted border-bottom">
                            <tr>
                                <th>ORDER ID</th>
                                <th>PELANGGAN</th>
                                <th>TOTAL</th>
                                <th>METODE</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>

                        {{-- ================= BODY ================= --}}
                        <tbody>
                            @forelse($pesanan as $p)

                            @php
                                $status = $p->status_bayar;

                                // STATUS
                                $label = match($status) {
                                    1 => 'LUNAS',
                                    2 => 'GAGAL',
                                    default => 'PENDING',
                                };

                                $badge = match($status) {
                                    1 => 'badge-gradient-success',
                                    2 => 'badge-gradient-danger',
                                    default => 'badge-gradient-warning',
                                };

                                // METODE
                                $metode = match($p->metode_bayar) {
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

                            <tr>
                                {{-- ORDER ID --}}
                                <td class="fw-bold text-info">
                                    #{{ $p->order_id }}
                                </td>

                                {{-- PELANGGAN --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-gradient-info text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width:30px; height:30px;">
                                            {{ strtoupper(substr($p->nama ?? 'G', 0, 1)) }}
                                        </div>
                                        {{ $p->nama ?? 'Guest' }}
                                    </div>
                                </td>

                                {{-- TOTAL --}}
                                <td class="text-success fw-bold">
                                    Rp {{ number_format($p->total, 0, ',', '.') }}
                                </td>

                                {{-- METODE --}}
                                <td>
                                    <span class="text-muted small">
                                        <i class="mdi mdi-credit-card-outline me-1"></i>
                                        {{ $metode }}
                                    </span>
                                </td>

                                {{-- STATUS --}}
                                <td class="text-center">
                                    <label class="badge {{ $badge }} text-white px-3 py-2">
                                        {{ $label }}
                                    </label>
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <a href="{{ route('pesanan.detail', $p->order_id) }}"
                                       class="btn btn-sm btn-outline-info">
                                        Rincian
                                        <i class="mdi mdi-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-database-off-outline d-block mb-2" style="font-size: 30px;"></i>
                                    Belum ada transaksi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection