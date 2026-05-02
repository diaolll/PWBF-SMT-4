@extends('layouts.Template')

@section('content')
    <style>
        .scanner-wrapper { position: relative; max-width: 500px; margin: 0 auto; }
        #reader { border-radius: 10px; overflow: hidden; min-height: 300px; background: #000; }
        .scanner-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; }
        .scan-line { position: absolute; width: 100%; height: 3px; background: linear-gradient(90deg, transparent, #00d0b1, transparent); animation: scanMove 2s ease-in-out infinite; }
        @keyframes scanMove { 0%, 100% { top: 10%; } 50% { top: 85%; } }
        .corner-bracket { position: absolute; width: 30px; height: 30px; border: 2px solid #00d0b1; }
        .corner-bracket.tl { top: 15px; left: 15px; border-right: none; border-bottom: none; }
        .corner-bracket.tr { top: 15px; right: 15px; border-left: none; border-bottom: none; }
        .corner-bracket.bl { bottom: 15px; left: 15px; border-right: none; border-top: none; }
        .corner-bracket.br { bottom: 15px; right: 15px; border-left: none; border-top: none; }
        .result-box { display: none; }
        .result-box.show { display: block; }
        .history-item { transition: all 0.2s; cursor: pointer; }
        .history-item:hover { background: #f8f9fa; }
        .history-time { font-size: 0.75rem; color: #6c757d; }
        .scan-count { background: #00d0b1; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-barcode-scan"></i>
            </span> Barcode Scanner
            <span class="scan-count" id="scanCount">0 Scan</span>
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Scanner</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        {{-- Scanner Section --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Scanner Barcode</h4>
                    <p class="card-description">Arahkan barcode ke kamera</p>

                    <div class="scanner-wrapper">
                        <div id="reader"></div>
                        <div class="scanner-overlay" id="scannerOverlay">
                            <div class="corner-bracket tl"></div>
                            <div class="corner-bracket tr"></div>
                            <div class="corner-bracket bl"></div>
                            <div class="corner-bracket br"></div>
                            <div class="scan-line"></div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-gradient-primary btn-rounded" id="startBtn" onclick="startScanner()" style="display: none;">
                            <i class="mdi mdi-play"></i> Mulai Scan
                        </button>
                    </div>

                    {{-- Manual Input --}}
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small text-center mb-2">atau input manual untuk testing:</p>
                        <div class="input-group">
                            <input type="text" id="manualInput" class="form-control" placeholder="Masukkan kode barang (contoh: BRG26022801)">
                            <button class="btn btn-gradient-primary" type="button" onclick="manualScan()">Cari</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Result Section --}}
        <div class="col-md-6">
            <div class="card result-box" id="resultCard">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge badge-gradient-success" style="font-size: 0.9rem; padding: 8px 16px;">
                            <i class="mdi mdi-check-circle"></i> Berhasil Scan!
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ID Barang</label>
                        <input type="text" class="form-control" id="resultId" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="resultNama" readonly>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="text" class="form-control text-success" style="font-weight: 600;" id="resultHarga" readonly>
                    </div>

                    <button type="button" class="btn btn-gradient-primary btn-rounded btn-fw" onclick="startScanner()">
                        <i class="mdi mdi-refresh"></i> Scan Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scan History Section --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-history"></i> Riwayat Scan
                        </h4>
                        <button type="button" class="btn btn-gradient-danger btn-sm" onclick="clearHistory()">
                            <i class="mdi mdi-delete"></i> Hapus Riwayat
                        </button>
                    </div>

                    <div id="historyList">
                        <p class="text-muted text-center py-3">Belum ada riwayat scan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Beep Sound --}}
    <audio id="beepSound" src="{{ asset('assets/sound/beep.mp3') }}" preload="auto"></audio>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let isScanning = false;
    let lastScanTime = 0;
    let scanHistory = [];

    document.addEventListener('DOMContentLoaded', function() {
        html5QrCode = new Html5Qrcode("reader");
        loadHistory();
        startScanner();
    });

    function startScanner() {
        document.getElementById('resultCard').classList.remove('show');
        document.getElementById('startBtn').style.display = 'none';
        document.getElementById('scannerOverlay').style.display = 'block';

        if (!isScanning) {
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 300, height: 200 } },
                onScanSuccess
            ).then(() => {
                isScanning = true;
            }).catch(err => {
                console.log('Scanner error:', err);
                document.getElementById('startBtn').style.display = 'inline-block';
            });
        }
    }

    function onScanSuccess(decodedText) {
        const now = Date.now();
        if (now - lastScanTime < 2000) return;
        lastScanTime = now;

        if (decodedText.startsWith('http')) return;

        playBeep();
        stopScanner();
        showLoadingResult(decodedText);
        fetchBarangData(decodedText);
    }

    function stopScanner() {
        if (isScanning && html5QrCode) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                document.getElementById('scannerOverlay').style.display = 'none';
                document.getElementById('startBtn').style.display = 'inline-block';
            });
        }
    }

    // ================= AUDIO FIX (SAFARI SAFE) =================
    function playBeep() {
        const beep = document.getElementById('beepSound');

        try {
            beep.currentTime = 0;
            beep.muted = false;
            const p = beep.play();

            if (p !== undefined) {
                p.catch(() => {
                    const fallback = new Audio('/assets/sound/beep.mp3');
                    fallback.play().catch(() => {});
                });
            }
        } catch {}
    }

    document.addEventListener('click', unlockAudio, { once: true });
    document.addEventListener('touchstart', unlockAudio, { once: true });

    function unlockAudio() {
        const beep = document.getElementById('beepSound');
        beep.play().then(() => {
            beep.pause();
            beep.currentTime = 0;
        }).catch(() => {});
    }

    // ================= API FIX =================
    function fetchBarangData(code) {
        fetch('/api/barang/' + code, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            console.log("API:", data);

            // FLEXIBLE PARSER (INI KUNCI FIX)
            let barang = null;

            if (data.data) barang = data.data;
            else if (data.id_barang) barang = data;
            else if (data.result) barang = data.result;

            if (barang && (barang.id_barang || barang.id)) {
                displayBarangResult(barang);
                addToHistory(barang);
            } else {
                showNotFound(code);
            }
        })
        .catch(() => showError(code));
    }

    function displayBarangResult(barang) {
        document.getElementById('resultId').value =
            barang.id_barang || barang.id || '-';

        document.getElementById('resultNama').value =
            barang.nama || barang.nama_barang || 'Tidak ada';

        const harga = barang.harga || barang.price || 0;

        document.getElementById('resultHarga').value =
            'Rp ' + Number(harga).toLocaleString('id-ID');
    }

    function showLoadingResult(code) {
        document.getElementById('resultId').value = code;
        document.getElementById('resultNama').value = 'Memuat...';
        document.getElementById('resultHarga').value = '...';
        document.getElementById('resultCard').classList.add('show');
    }

    function showNotFound(code) {
        document.getElementById('resultNama').value = 'Barang tidak ditemukan';
        document.getElementById('resultHarga').value = 'Rp 0';
    }

    function showError(code) {
        document.getElementById('resultNama').value = 'Error server';
        document.getElementById('resultHarga').value = 'Rp 0';
    }

    // ================= HISTORY FIX =================
    function addToHistory(barang) {
        const historyItem = {
            id_barang: barang.id_barang || barang.id,
            nama: barang.nama || barang.nama_barang,
            harga: barang.harga || barang.price || 0,
            scanTime: new Date().toISOString()
        };

        // FIX BUG LU (INI PENTING)
        scanHistory = scanHistory.filter(h => h.id_barang !== historyItem.id_barang);

        scanHistory.unshift(historyItem);
        if (scanHistory.length > 50) scanHistory = scanHistory.slice(0, 50);

        saveHistory();
        renderHistory();
    }

    function loadHistory() {
        const stored = localStorage.getItem('barang_scan_history');
        scanHistory = stored ? JSON.parse(stored) : [];
        renderHistory();
    }

    function saveHistory() {
        localStorage.setItem('barang_scan_history', JSON.stringify(scanHistory));
        updateScanCount();
    }

    function updateScanCount() {
        document.getElementById('scanCount').textContent = scanHistory.length + ' Scan';
    }

    function renderHistory() {
        const container = document.getElementById('historyList');
        updateScanCount();

        if (!scanHistory.length) {
            container.innerHTML = '<p class="text-muted text-center py-3">Belum ada riwayat scan</p>';
            return;
        }

        container.innerHTML = '<div class="row">' + scanHistory.map(h => {
            const time = new Date(h.scanTime).toLocaleString('id-ID');

            return `
                <div class="col-md-6 mb-2">
                    <div class="card history-item" onclick="showHistoryDetail('${h.id_barang}')">
                        <div class="card-body p-3">
                            <h6>${h.id_barang}</h6>
                            <p>${h.nama}</p>
                            <p class="text-success">Rp ${Number(h.harga).toLocaleString('id-ID')}</p>
                            <small>${time}</small>
                        </div>
                    </div>
                </div>
            `;
        }).join('') + '</div>';
    }

    function showHistoryDetail(id) {
        const item = scanHistory.find(h => h.id_barang === id);
        if (item) {
            stopScanner();
            displayBarangResult(item);
            document.getElementById('resultCard').classList.add('show');
        }
    }

    function clearHistory() {
        if (confirm('Hapus semua riwayat scan?')) {
            scanHistory = [];
            saveHistory();
            renderHistory();
        }
    }

    function manualScan() {
        const code = document.getElementById('manualInput').value.trim();
        if (!code) return;

        playBeep();
        showLoadingResult(code);
        fetchBarangData(code);
        document.getElementById('manualInput').value = '';
    }

    document.getElementById('manualInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') manualScan();
    });
</script>
@endsection
