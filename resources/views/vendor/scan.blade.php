@extends('layouts.Template')

@section('content')
    <style>
        .scanner-wrapper { position: relative; max-width: 500px; margin: 0 auto; }
        #reader { border-radius: 10px; overflow: hidden; min-height: 300px; background: #000; }
        .scanner-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; }
        .scan-line { position: absolute; width: 100%; height: 3px; background: linear-gradient(90deg, transparent, #716aca, transparent); animation: scanMove 2s ease-in-out infinite; }
        @keyframes scanMove { 0%, 100% { top: 10%; } 50% { top: 85%; } }
        .corner-bracket { position: absolute; width: 30px; height: 30px; border: 2px solid #716aca; }
        .corner-bracket.tl { top: 15px; left: 15px; border-right: none; border-bottom: none; }
        .corner-bracket.tr { top: 15px; right: 15px; border-left: none; border-bottom: none; }
        .corner-bracket.bl { bottom: 15px; left: 15px; border-right: none; border-top: none; }
        .corner-bracket.br { bottom: 15px; right: 15px; border-left: none; border-top: none; }
        .result-box { display: none; }
        .result-box.show { display: block; }
        .order-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #eaeaec; }
        .order-item:last-child { border-bottom: none; }
        .history-item { transition: all 0.2s; cursor: pointer; }
        .history-item:hover { background: #f8f9fa; }
        .history-time { font-size: 0.75rem; color: #6c757d; }
        .status-lunas { background: linear-gradient(135deg, #10b981, #059669); }
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-gagal { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .scan-count { background: #716aca; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-qrcode"></i>
            </span> QR Scanner (Vendor)
            <span class="scan-count" id="scanCount">0 Scan</span>
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Pesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">QR Scanner</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        {{-- Scanner Section --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Scanner QR Code</h4>
                    <p class="card-description">Scan QR Code customer</p>

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

                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small text-center mb-2">atau input Order ID:</p>
                        <div class="input-group">
                            <input type="text" id="manualInput" class="form-control" placeholder="Masukkan Order ID (contoh: INV-53)">
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
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="card-title mb-0">Hasil Scan</h4>
                        <span class="badge text-white" id="statusBadge">LUNAS</span>
                    </div>

                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" class="form-control" id="resultOrderId" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Customer</label>
                        <input type="text" class="form-control" id="resultNama" readonly>
                    </div>
                    <div class="form-group">
                        <label>Status Bayar</label>
                        <input type="text" class="form-control" id="resultStatusBayar" readonly>
                    </div>
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <input type="text" class="form-control" id="resultMetode" readonly>
                    </div>
                    <div class="form-group">
                        <label>Total</label>
                        <input type="text" class="form-control text-primary" style="font-weight: 600;" id="resultTotal" readonly>
                    </div>

                    <h6 class="mt-4 mb-3">Menu yang Dipesan</h6>
                    <div class="bg-light p-3 rounded" id="itemsList">
                        <p class="text-muted text-center mb-0">Memuat...</p>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-gradient-primary btn-rounded" onclick="startScanner()">
                            <i class="mdi mdi-refresh"></i> Scan Lagi
                        </button>
                        <a href="#" id="detailLink" class="btn btn-gradient-info btn-rounded" target="_blank">
                            <i class="mdi mdi-open-in-new"></i> Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scan History Section --}}
    <div class="row mt-4" id="historySection">
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

        // Metode pembayaran mapping
        const metodeMap = {
            0: 'Belum dibayar',
            1: 'QRIS',
            2: 'VA / Bank Transfer',
            3: 'Mandiri Bill',
            4: 'Alfamart / Indomaret',
            5: 'GoPay',
            6: 'ShopeePay',
            7: 'Kartu Kredit'
        };

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
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    () => {}
                ).then(() => {
                    isScanning = true;
                }).catch(err => {
                    console.log('Error starting scanner:', err);
                    document.getElementById('startBtn').style.display = 'inline-block';
                });
            }
        }

        function onScanSuccess(decodedText) {
            const now = Date.now();
            if (now - lastScanTime < 2000) return;
            lastScanTime = now;

            // Extract Order ID
            let orderId = decodedText;
            if (decodedText.includes('/pesanan/detail/')) {
                orderId = decodedText.split('/pesanan/detail/')[1].split('/')[0].split('?')[0];
            } else if (decodedText.includes('http')) {
                const parts = decodedText.split('/').filter(p => p);
                orderId = parts[parts.length - 1];
            }

            // a. Play beep
            playBeep();

            // b. Stop scanner
            stopScanner();

            // c. Show result and fetch data
            showLoadingResult(orderId);
            fetchOrderData(orderId);
        }

        function playBeep() {
            const beep = document.getElementById('beepSound');
            beep.currentTime = 0;
            // Set volume and play
            beep.volume = 1.0;
            // Play with retry
            const playPromise = beep.play();
            if (playPromise !== undefined) {
                playPromise.catch(() => {
                    // Fallback: create new audio element
                    const fallback = new Audio('/assets/sound/beep.mp3');
                    fallback.volume = 1.0;
                    fallback.play().catch(() => {});
                });
            }
        }

        // Initialize audio on first user interaction
        function initAudio() {
            const beep = document.getElementById('beepSound');
            beep.play().then(() => {
                beep.pause();
                beep.currentTime = 0;
            }).catch(() => {});
            document.removeEventListener('click', initAudio);
            document.removeEventListener('touchstart', initAudio);
        }

        // Listen for first interaction
        document.addEventListener('click', initAudio, { once: true });
        document.addEventListener('touchstart', initAudio, { once: true });

        function stopScanner() {
            if (isScanning && html5QrCode) {
                html5QrCode.stop().then(() => {
                    isScanning = false;
                    document.getElementById('scannerOverlay').style.display = 'none';
                    document.getElementById('startBtn').style.display = 'inline-block';
                }).catch(() => {});
            }
        }

        function showLoadingResult(orderId) {
            document.getElementById('resultOrderId').value = orderId;
            document.getElementById('resultNama').value = 'Memuat...';
            document.getElementById('resultTotal').value = '...';
            document.getElementById('resultStatusBayar').value = '...';
            document.getElementById('resultMetode').value = '...';
            document.getElementById('resultCard').classList.add('show');
            document.getElementById('itemsList').innerHTML = '<p class="text-muted text-center mb-0">Memuat...</p>';
        }

        function fetchOrderData(orderId) {
            fetch('/api/pesanan/' + orderId)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data) {
                        displayOrderResult(data.data);
                        addToHistory(data.data);
                    } else {
                        showNotFound(orderId);
                    }
                })
                .catch(() => {
                    showError(orderId);
                });
        }

        function displayOrderResult(p) {
            document.getElementById('resultOrderId').value = p.order_id || '-';
            document.getElementById('resultNama').value = p.nama || 'Customer';
            document.getElementById('resultTotal').value = 'Rp ' + (p.total || 0).toLocaleString('id-ID');

            const s = p.status_bayar || 0;
            const m = p.items?.[0]?.metode_bayar || 0;

            let st = 'BELUM BAYAR', sc = 'status-pending';
            if (s == 1) { st = 'LUNAS'; sc = 'status-lunas'; }
            else if (s == 2) { st = 'GAGAL'; sc = 'status-gagal'; }

            document.getElementById('resultStatusBayar').value = st;
            document.getElementById('resultMetode').value = metodeMap[m] || 'Belum dibayar';

            const statusBadge = document.getElementById('statusBadge');
            statusBadge.textContent = st;
            statusBadge.className = 'badge text-white ' + sc;

            // Update detail link
            document.getElementById('detailLink').href = '/pesanan/detail/' + (p.order_id || '');

            // Display items
            const itemsList = document.getElementById('itemsList');
            if (p.items && p.items.length > 0) {
                itemsList.innerHTML = p.items.map(i => {
                    const hargaSatuan = i.harga ? 'Rp ' + i.harga.toLocaleString('id-ID') : '';
                    return `
                        <div class="order-item">
                            <div>
                                <div class="font-weight-bold">${i.nama}</div>
                                <small class="text-muted">${hargaSatuan} x ${i.jumlah}</small>
                            </div>
                            <span class="text-success">${i.subtotal}</span>
                        </div>
                    `;
                }).join('');
            } else {
                itemsList.innerHTML = '<p class="text-muted text-center mb-0">Tidak ada item</p>';
            }
        }

        function showNotFound(orderId) {
            document.getElementById('resultNama').value = 'Tidak ditemukan';
            document.getElementById('resultTotal').value = 'Rp 0';
            document.getElementById('resultStatusBayar').value = '-';
            document.getElementById('resultMetode').value = '-';
            document.getElementById('statusBadge').textContent = 'NOT FOUND';
            document.getElementById('statusBadge').className = 'badge text-white status-gagal';
            document.getElementById('itemsList').innerHTML = '<p class="text-muted text-center mb-0">Pesanan tidak ditemukan</p>';
            document.getElementById('detailLink').href = '#';
        }

        function showError(orderId) {
            document.getElementById('resultNama').value = 'Error koneksi';
            document.getElementById('resultTotal').value = 'Rp 0';
            document.getElementById('resultStatusBayar').value = '-';
            document.getElementById('resultMetode').value = '-';
            document.getElementById('itemsList').innerHTML = '<p class="text-danger text-center mb-0">Gagal memuat data</p>';
        }

        function addToHistory(order) {
            const historyItem = {
                ...order,
                scanTime: new Date().toISOString()
            };

            // Remove duplicate order_id if exists
            scanHistory = scanHistory.filter(h => h.order_id !== order.order_id);
            // Add new scan to beginning
            scanHistory.unshift(historyItem);
            // Keep only last 20 scans
            if (scanHistory.length > 20) scanHistory = scanHistory.slice(0, 20);

            saveHistory();
            renderHistory();
        }

        function loadHistory() {
            const stored = localStorage.getItem('vendor_scan_history');
            scanHistory = stored ? JSON.parse(stored) : [];
            renderHistory();
        }

        function saveHistory() {
            localStorage.setItem('vendor_scan_history', JSON.stringify(scanHistory));
            updateScanCount();
        }

        function renderHistory() {
            const container = document.getElementById('historyList');
            updateScanCount();

            if (scanHistory.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">Belum ada riwayat scan</p>';
                return;
            }

            container.innerHTML = '<div class="row">' + scanHistory.map(h => {
                const s = h.status_bayar || 0;
                let sc = 'status-pending', st = 'PENDING';
                if (s == 1) { sc = 'status-lunas'; st = 'LUNAS'; }
                else if (s == 2) { sc = 'status-gagal'; st = 'GAGAL'; }

                const time = new Date(h.scanTime).toLocaleString('id-ID', {
                    day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
                });

                return `
                    <div class="col-md-6 mb-2">
                        <div class="card history-item" onclick="showHistoryDetail('${h.order_id}')">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge text-white ${sc} mb-1">${st}</span>
                                        <h6 class="mb-1">${h.order_id}</h6>
                                        <p class="mb-0 text-muted">${h.nama || 'Customer'}</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0 text-success font-weight-bold">Rp ${(h.total || 0).toLocaleString('id-ID')}</p>
                                        <small class="history-time">${time}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('') + '</div>';
        }

        function showHistoryDetail(orderId) {
            const item = scanHistory.find(h => h.order_id === orderId);
            if (item) {
                stopScanner();
                displayOrderResult(item);
                document.getElementById('resultCard').classList.add('show');
                document.getElementById('detailLink').href = '/pesanan/detail/' + orderId;
            }
        }

        function clearHistory() {
            if (confirm('Hapus semua riwayat scan?')) {
                scanHistory = [];
                saveHistory();
                renderHistory();
            }
        }

        function updateScanCount() {
            document.getElementById('scanCount').textContent = scanHistory.length + ' Scan';
        }

        function manualScan() {
            const orderId = document.getElementById('manualInput').value.trim();
            if (!orderId) return;

            lastScanTime = Date.now();
            playBeep();
            showLoadingResult(orderId);
            fetchOrderData(orderId);
            document.getElementById('manualInput').value = '';
        }

        document.getElementById('manualInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') manualScan();
        });
    </script>
@endsection
