@extends('layouts.Template')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Tambah Antrian Manual --}}
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Tambah Antrian Manual</h5>
        <form action="{{ route('antrian.tambah') }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="text" name="nama" required placeholder="Nama pelanggan" class="form-control">
            <button type="submit" class="btn btn-primary btn-sm px-4">Tambah</button>
        </form>
    </div>
</div>

<div class="row mb-4">
    {{-- Sedang Dipanggil --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-2" style="font-size: 13px;">
                    <i class="mdi mdi-circle text-success me-1" style="font-size: 10px;"></i>Sedang Dipanggil
                </p>
                <div id="dipanggilDisplay">
                    <p class="text-muted">Tidak ada yang dipanggil</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Aksi</h5>
                <div class="d-grid gap-2">
                    <form action="{{ route('antrian.panggil') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block w-100">
                            <i class="mdi mdi-bell-ring-outline me-1"></i> Panggil Berikutnya
                        </button>
                    </form>

                    <a href="{{ route('guest.index') }}" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="mdi mdi-ticket-outline me-1"></i> Ambil Tiket (Guest)
                    </a>

                    <a href="{{ route('papan.index') }}" target="_blank" class="btn btn-outline-secondary w-100">
                        <i class="mdi mdi-monitor me-1"></i> Buka Papan Antrian
                    </a>

                    <form action="{{ route('antrian.reset') }}" method="POST" onsubmit="return confirm('Yakin ingin mereset semua data antrian?');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block w-100">
                            <i class="mdi mdi-delete-outline me-1"></i> Reset Semua Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Antrian Menunggu --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    Menunggu <span id="menungguCount" class="text-muted fw-normal" style="font-size: 14px;">(0)</span>
                </h5>
                <div id="menungguList" style="max-height: 300px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>

    {{-- Antrian Terlambat --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    Terlambat <span id="terlambatCount" class="text-muted fw-normal" style="font-size: 14px;">(0)</span>
                </h5>
                <div id="terlambatList" style="max-height: 300px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const baseUrlTerlambat = '{{ url('/antrian/terlambat') }}';
    const baseUrlPanggilTerlambat = '{{ url('/antrian/panggil-terlambat') }}';
    const csrfToken = '{{ csrf_token() }}';

    function renderDipanggil(dipanggil) {
        const container = document.getElementById('dipanggilDisplay');
        if (!dipanggil) {
            container.innerHTML = '<p class="text-muted">Tidak ada yang dipanggil</p>';
            return;
        }
        container.innerHTML = `
            <div style="font-size: 72px; font-weight: 700; color: #10B981; line-height: 1;">
                ${String(dipanggil.nomor).padStart(3, '0')}
            </div>
            <div style="font-size: 20px; font-weight: 600; color: #1F2937; margin-top: 8px;">
                ${dipanggil.nama}
            </div>
        `;
    }

    function renderMenunggu(menunggu) {
        const container = document.getElementById('menungguList');
        document.getElementById('menungguCount').textContent = `(${menunggu.length})`;

        if (menunggu.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3" style="font-size: 13px;">Tidak ada antrian menunggu</p>';
            return;
        }

        container.innerHTML = menunggu.map(item => `
            <div class="d-flex justify-content-between align-items-center p-2 mb-2 rounded" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                <div>
                    <span class="fw-bold me-2" style="color: #92400E;">${String(item.nomor).padStart(3, '0')}</span>
                    <span style="font-size: 14px;">${item.nama}</span>
                </div>
                <form action="${baseUrlTerlambat}/${item.id}" method="POST" style="display:inline;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" class="btn btn-warning btn-sm" style="font-size: 11px; padding: 3px 10px;">Terlambat</button>
                </form>
            </div>
        `).join('');
    }

    function renderTerlambat(terlambat) {
        const container = document.getElementById('terlambatList');
        document.getElementById('terlambatCount').textContent = `(${terlambat.length})`;

        if (terlambat.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3" style="font-size: 13px;">Tidak ada antrian terlambat</p>';
            return;
        }

        container.innerHTML = terlambat.map(item => `
            <div class="d-flex justify-content-between align-items-center p-2 mb-2 rounded" style="background: #FEF2F2; border: 1px solid #FECACA; cursor: pointer;" ondblclick="panggilTerlambat(${item.id})" title="Double click untuk panggil">
                <div>
                    <span class="fw-bold me-2" style="color: #991B1B;">${String(item.nomor).padStart(3, '0')}</span>
                    <span style="font-size: 14px;">${item.nama}</span>
                </div>
                <span class="text-muted" style="font-size: 11px;">double click</span>
            </div>
        `).join('');
    }

    function panggilTerlambat(id) {
        if (confirm('Panggil antrian terlambat ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = baseUrlPanggilTerlambat + '/' + id;
            form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    async function fetchAntrianData() {
        try {
            const res = await fetch('/api/antrian');
            const data = await res.json();
            renderDipanggil(data.dipanggil);
            renderMenunggu(data.menunggu);
            renderTerlambat(data.terlambat);
        } catch (err) {
            console.log('Poll error:', err);
        }
    }

    fetchAntrianData();
    const pollInterval = setInterval(fetchAntrianData, 1500);
    window.addEventListener('beforeunload', () => clearInterval(pollInterval));
</script>

@endsection