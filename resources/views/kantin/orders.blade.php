@extends('layouts.Template')

@section('content')
    <style>
        .order-card { transition: all 0.2s; cursor: pointer; }
        .order-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(113, 106, 202, 0.15); }
        .qr-thumbnail { width: 70px; height: 70px; border: 2px solid #eaeaec; border-radius: 10px; padding: 5px; background: white; }
        .qr-thumbnail img { width: 100%; height: 100%; object-fit: contain; }
        .modal-backdrop { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s; }
        .modal-backdrop.show { opacity: 1; visibility: visible; }
        .modal-content { background: white; border-radius: 15px; padding: 25px; max-width: 400px; width: 90%; transform: scale(0.9); transition: all 0.3s; }
        .modal-backdrop.show .modal-content { transform: scale(1); }
        .modal-qr img { max-width: 200px; border-radius: 10px; border: 2px solid #eaeaec; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eaeaec; }
        .info-row:last-child { border-bottom: none; }
        .status-lunas { background: linear-gradient(135deg, #10b981, #059669); }
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-gagal { background: linear-gradient(135deg, #ef4444, #dc2626); }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-history"></i>
            </span> Pesanan Saya
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('kantin.index') }}">Kantin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pesanan Saya</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div id="ordersList"></div>
        </div>
    </div>

    {{-- QR Code Modal --}}
    <div class="modal-backdrop" id="qrModal">
        <div class="modal-content">
            <h4 class="card-title text-center mb-4">
                <i class="mdi mdi-qrcode"></i> QR Code Pesanan
            </h4>
            <div class="text-center mb-3">
                <img id="modalQrImage" src="" alt="QR Code" class="modal-qr">
            </div>
            <div class="bg-light p-3 rounded">
                <div class="info-row">
                    <span class="text-muted">Order ID</span>
                    <span class="font-weight-bold" id="modalOrderId">-</span>
                </div>
                <div class="info-row">
                    <span class="text-muted">Total</span>
                    <span class="text-success font-weight-bold" id="modalTotal">-</span>
                </div>
                <div class="info-row">
                    <span class="text-muted">Tanggal</span>
                    <span id="modalDate">-</span>
                </div>
                <div class="info-row">
                    <span class="text-muted">Status</span>
                    <span class="badge text-white" id="modalStatus">-</span>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-gradient-primary btn-rounded btn-fw" onclick="closeModal()">
                    <i class="mdi mdi-close"></i> Tutup
                </button>
                <a href="#" id="modalDetailLink" class="btn btn-gradient-info btn-rounded btn-fw" target="_blank">
                    <i class="mdi mdi-open-in-new"></i> Detail
                </a>
            </div>
        </div>
    </div>

    <script>
        let orders = [];

        document.addEventListener('DOMContentLoaded', function() {
            const stored = localStorage.getItem('kantin_orders');
            orders = stored ? JSON.parse(stored) : [];
            renderOrders();
        });

        function renderOrders() {
            const c = document.getElementById('ordersList');
            if (orders.length === 0) {
                c.innerHTML = `
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="mdi mdi-basket-off text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h3>Belum Ada Pesanan</h3>
                            <p class="text-muted">Anda belum memiliki riwayat pesanan</p>
                            <a href="{{ route('kantin.index') }}" class="btn btn-gradient-success btn-rounded btn-fw">
                                <i class="mdi mdi-store"></i> Ke Kantin
                            </a>
                        </div>
                    </div>`;
                return;
            }

            c.innerHTML = '<div class="row">' + orders.map((o, index) => {
                // Determine status badge
                let statusBadge = 'status-pending';
                let statusText = 'PENDING';
                if (o.status_bayar == 1) {
                    statusBadge = 'status-lunas';
                    statusText = 'LUNAS';
                } else if (o.status_bayar == 2) {
                    statusBadge = 'status-gagal';
                    statusText = 'GAGAL';
                }

                return `
                    <div class="col-md-6 mb-3">
                        <div class="card order-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="qr-thumbnail me-3">
                                        <img src="${o.qrBase64 || ''}" alt="QR" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 80 80%22><rect fill=%22%23f1f5f9%22 width=%2280%22 height=%2280%22/><text fill=%22%2394a3b8%22 font-size=%2220%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>QR</text></svg>'">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge text-white ${statusBadge}">${statusText}</span>
                                        </div>
                                        <h5 class="mb-1">${o.nama || 'Customer'}</h5>
                                        <p class="text-success font-weight-bold mb-1">Rp ${o.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</p>
                                        <small class="text-muted">${formatDate(o.timestamp)}</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-gradient-primary btn-rounded btn-sm" onclick="showQrModal('${o.order_id}')">
                                        <i class="mdi mdi-qrcode"></i> QR
                                    </button>
                                    <a href="/pesanan/detail/${o.order_id}" class="btn btn-gradient-info btn-rounded btn-sm" target="_blank">
                                        <i class="mdi mdi-open-in-new"></i> Detail
                                    </a>
                                    <button type="button" class="btn btn-gradient-danger btn-rounded btn-sm" onclick="deleteOrder(${index})">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('') + '</div>';
        }

        function showQrModal(id) {
            const o = orders.find(x => x.order_id === id);
            if (!o) return;

            document.getElementById('modalQrImage').src = o.qrBase64 || '';
            document.getElementById('modalOrderId').textContent = o.order_id;
            document.getElementById('modalTotal').textContent = 'Rp ' + o.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            document.getElementById('modalDate').textContent = formatDate(o.timestamp);
            document.getElementById('modalDetailLink').href = '/pesanan/detail/' + o.order_id;

            // Status badge
            let statusBadge = 'status-pending';
            let statusText = 'PENDING';
            if (o.status_bayar == 1) {
                statusBadge = 'status-lunas';
                statusText = 'LUNAS';
            } else if (o.status_bayar == 2) {
                statusBadge = 'status-gagal';
                statusText = 'GAGAL';
            }

            const statusEl = document.getElementById('modalStatus');
            statusEl.textContent = statusText;
            statusEl.className = 'badge text-white ' + statusBadge;

            document.getElementById('qrModal').classList.add('show');
        }

        function closeModal() { document.getElementById('qrModal').classList.remove('show'); }

        function deleteOrder(index) {
            if (confirm('Hapus pesanan ini?')) {
                orders.splice(index, 1);
                localStorage.setItem('kantin_orders', JSON.stringify(orders));
                renderOrders();
            }
        }

        function formatDate(iso) {
            const d = new Date(iso);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        document.getElementById('qrModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
    </script>
@endsection
