@extends('layouts.Template')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">Kunjungan Toko</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- ===== LIST TOKO ===== --}}
        <h4>List Toko</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Nama Toko</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Accuracy</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tokos as $toko)
                <tr>
                    <td>{{ $toko->barcode }}</td>
                    <td>{{ $toko->nama_toko }}</td>
                    <td>{{ $toko->latitude }}</td>
                    <td>{{ $toko->longitude }}</td>
                    <td>{{ $toko->accuracy }} m</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="cetakBarcode('{{ $toko->barcode }}', '{{ $toko->nama_toko }}')">
                            Cetak Barcode
                        </button>
                        <form method="POST"
                              action="{{ route('kunjungan-toko.toko.destroy', $toko->barcode) }}"
                              style="display:inline"
                              onsubmit="return confirm('Hapus toko ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Belum ada data toko.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- ===== INPUT TITIK AWAL ===== --}}
        <h4 class="mt-4">Input Titik Awal</h4>
        <form method="POST" action="{{ route('kunjungan-toko.toko.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" required maxlength="50">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="number" step="any" name="latitude" id="lat-toko" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="number" step="any" name="longitude" id="lng-toko" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Accuracy (m)</label>
                        <input type="number" step="any" name="accuracy" id="acc-toko" class="form-control" required>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-secondary" onclick="ambilLokasiToko()">📍 Ambil Lokasi Otomatis</button>
                    <span id="status-lokasi-toko" class="ml-2 text-muted"></span>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Toko</button>
                </div>
            </div>
        </form>

        {{-- ===== TITIK KUNJUNGAN ===== --}}
        <h4 class="mt-4">Titik Kunjungan</h4>
        <div class="form-group">
            <label>Barcode Toko</label>
            <div class="input-group">
                <input type="text" id="barcode-kunjungan" class="form-control" placeholder="Scan / input barcode (lalu Enter)">
                <button class="btn btn-primary" onclick="cariToko()">Cari Toko</button>
                <button class="btn btn-success" onclick="bukaScanner()">📷 Scan Kamera</button>
            </div>
        </div>

        {{-- Scanner Modal --}}
        <div id="scanner-modal" class="card mt-3" style="display:none">
            <div class="card-body">
                <h5>📷 Scan Barcode/QR Code</h5>
                <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                <button class="btn btn-secondary mt-3" onclick="tutupScanner()">Tutup Scanner</button>
            </div>
        </div>

        <div id="info-toko" class="card mt-3" style="display:none">
            <div class="card-body">
                <h5>Info Toko</h5>
                <p>Nama      : <span id="k-nama"></span></p>
                <p>Latitude  : <span id="k-lat"></span></p>
                <p>Longitude : <span id="k-lng"></span></p>
                <p>Accuracy  : <span id="k-acc"></span> m</p>
                <button class="btn btn-secondary" onclick="ambilLokasiSales()">📍 Ambil Lokasi Saya</button>
                <span id="status-lokasi-sales" class="ml-2 text-muted"></span>
            </div>
        </div>

        <div id="info-sales" class="card mt-3" style="display:none">
            <div class="card-body">
                <h5>Posisi Saya</h5>
                <p>Latitude  : <span id="s-lat"></span></p>
                <p>Longitude : <span id="s-lng"></span></p>
                <p>Accuracy  : <span id="s-acc"></span> m</p>
                <button class="btn btn-success" onclick="submitKunjungan()">✅ Cek Kunjungan</button>
            </div>
        </div>

        <div id="hasil-kunjungan" class="card mt-3" style="display:none">
            <div class="card-body">
                <h5>Hasil Kunjungan</h5>
                <p>Toko              : <span id="h-nama"></span></p>
                <p>Jarak Aktual      : <span id="h-jarak"></span> m</p>
                <p>Threshold Efektif : <span id="h-threshold"></span> m</p>
                <p>Status            : <strong id="h-status"></strong></p>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL CETAK BARCODE ===== --}}
<div id="modal-barcode" class="modal" tabindex="-1" role="dialog" style="display:none">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-nama-toko"></h5>
                <button type="button" class="close" onclick="tutupModal()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode-box" class="d-flex justify-content-center"></div>
                <p id="modal-barcode-text" class="mt-3" style="font-family:monospace; font-size:1.2em"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Print</button>
                <button type="button" class="btn btn-secondary" onclick="tutupModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- QRCode Library --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
{{-- HTML5-QRCode Scanner --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
// =====================
// State
// =====================
let tokoAktif   = null;
let posisiSales = null;

// Auto-cari saat Enter di input barcode (barcode scanner biasanya kirim Enter)
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('barcode-kunjungan').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cariToko();
        }
    });
});

// =====================
// Input Titik Awal — Ambil Lokasi Otomatis
// =====================
async function ambilLokasiToko() {
    document.getElementById('status-lokasi-toko').textContent = '⏳ Mengambil lokasi...';
    try {
        const pos = await getAccuratePosition(50, 20000);
        document.getElementById('lat-toko').value = pos.coords.latitude;
        document.getElementById('lng-toko').value = pos.coords.longitude;
        document.getElementById('acc-toko').value = pos.coords.accuracy;
        document.getElementById('status-lokasi-toko').textContent = '✅ Lokasi didapat. Accuracy: ' + pos.coords.accuracy.toFixed(1) + ' m';
    } catch (e) {
        document.getElementById('status-lokasi-toko').textContent = '❌ Gagal: ' + e.message;
    }
}

// =====================
// Titik Kunjungan — Cari Toko by Barcode
// =====================
async function cariToko() {
    const barcode = document.getElementById('barcode-kunjungan').value.trim();
    if (!barcode) return alert('Masukkan barcode terlebih dahulu.');

    const res = await fetch(`/kunjungan-toko/toko/${barcode}`);
    if (!res.ok) return alert('Toko tidak ditemukan!');

    tokoAktif = await res.json();

    document.getElementById('k-nama').textContent = tokoAktif.nama_toko;
    document.getElementById('k-lat').textContent  = tokoAktif.latitude;
    document.getElementById('k-lng').textContent  = tokoAktif.longitude;
    document.getElementById('k-acc').textContent  = tokoAktif.accuracy;

    document.getElementById('info-toko').style.display    = 'block';
    document.getElementById('info-sales').style.display   = 'none';
    document.getElementById('hasil-kunjungan').style.display = 'none';
    posisiSales = null;
}

// =====================
// Titik Kunjungan — Ambil Lokasi Sales (Lampiran 1)
// =====================
async function ambilLokasiSales() {
    document.getElementById('status-lokasi-sales').textContent = '⏳ Mengambil lokasi, harap tunggu...';
    try {
        const pos = await getAccuratePosition(50, 20000);
        posisiSales = pos.coords;

        document.getElementById('s-lat').textContent = posisiSales.latitude;
        document.getElementById('s-lng').textContent = posisiSales.longitude;
        document.getElementById('s-acc').textContent = posisiSales.accuracy.toFixed(1);

        document.getElementById('status-lokasi-sales').textContent = '✅ Lokasi didapat.';
        document.getElementById('info-sales').style.display = 'block';
    } catch (e) {
        document.getElementById('status-lokasi-sales').textContent = '❌ Gagal: ' + e.message;
    }
}

// =====================
// Submit Cek Kunjungan
// =====================
async function submitKunjungan() {
    if (!tokoAktif || !posisiSales) return alert('Data belum lengkap.');

    const res = await fetch('{{ route("kunjungan-toko.cek") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            barcode:         tokoAktif.barcode,
            latitude_sales:  posisiSales.latitude,
            longitude_sales: posisiSales.longitude,
            accuracy_sales:  posisiSales.accuracy,
        })
    });

    const data = await res.json();
    const warna = data.status === 'diterima' ? 'green' : 'red';

    document.getElementById('h-nama').textContent      = data.nama_toko;
    document.getElementById('h-jarak').textContent     = data.jarak_aktual;
    document.getElementById('h-threshold').textContent = data.threshold_efektif;
    document.getElementById('h-status').textContent    = data.status.toUpperCase();
    document.getElementById('h-status').style.color    = warna;

    const hasilEl = document.getElementById('hasil-kunjungan');
    hasilEl.style.display     = 'block';
    hasilEl.style.borderColor = warna;
}

// =====================
// Cetak Barcode (QR)
// =====================
function cetakBarcode(barcode, namaToko) {
    document.getElementById('qrcode-box').innerHTML = '';
    document.getElementById('modal-nama-toko').textContent  = namaToko;
    document.getElementById('modal-barcode-text').textContent = barcode;
    document.getElementById('modal-barcode').style.display  = 'block';

    new QRCode(document.getElementById('qrcode-box'), {
        text: barcode,
        width: 200,
        height: 200
    });
}

function tutupModal() {
    document.getElementById('modal-barcode').style.display = 'none';
}

// =====================
// Barcode/QR Scanner dengan Kamera
// =====================
let html5QrCode = null;

function bukaScanner() {
    document.getElementById('scanner-modal').style.display = 'block';

    html5QrCode = new Html5Qrcode("reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrCode.start(
        { facingMode: "environment" }, // Kamera belakang
        config,
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        console.error("Scanner error:", err);
        alert("Gagal membuka kamera. Pastikan izin kamera diberikan.");
        tutupScanner();
    });
}

function onScanSuccess(decodedText, decodedResult) {
    // Barcode terdeteksi!
    playBeep();
    document.getElementById('barcode-kunjungan').value = decodedText;
    tutupScanner();
    cariToko(); // Otomatis cari toko setelah scan
}

function playBeep() {
    const audio = new Audio('{{ asset('assets/sound/beep.mp3') }}');
    audio.play().catch(e => console.log('Audio play failed:', e));
}

function onScanFailure(error) {
    // Jangan alert setiap failure, scanner terus mencoba
}

function tutupScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            html5QrCode = null;
        }).catch(err => console.error("Gagal stop scanner:", err));
    }
    document.getElementById('scanner-modal').style.display = 'none';
}

// =====================
// Lampiran 1 — getAccuratePosition
// =====================
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult  = null;
        const startTime = Date.now();

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }

                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error('Timeout, tidak dapat posisi'));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}
</script>
@endpush