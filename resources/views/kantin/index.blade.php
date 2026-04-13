@extends('layouts.Template')

@section('content')
    <style>
        :root {
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
            --accent: #3b82f6;
        }

        /* Header */
        .page-header {
            background: white;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-soft);
        }

        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .page-header p {
            color: var(--text-muted);
            margin: 0.25rem 0 0;
            font-size: 0.9rem;
        }

        /* Filter Bar */
        .filter-bar {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-soft);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-bar select,
        .filter-bar input {
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .filter-bar select:focus,
        .filter-bar input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-bar select {
            flex: 1;
            min-width: 200px;
        }

        .filter-bar input {
            flex: 1;
            min-width: 180px;
        }

        /* Menu Card */
        .menu-card {
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            overflow: hidden;
            background: white;
            transition: all 0.2s;
        }

        .menu-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .menu-img {
            height: 140px;
            background: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-body {
            padding: 1rem;
        }

        .menu-title {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .menu-price {
            color: var(--accent);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .menu-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.1rem;
            color: var(--text-dark);
        }

        .qty-btn:hover {
            background: var(--bg-soft);
        }

        .qty-btn.add {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .qty-btn.add:hover {
            background: #2563eb;
        }

        .qty-display {
            min-width: 32px;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Cart Card */
        .cart-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            overflow: hidden;
        }

        .cart-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .cart-badge {
            background: var(--bg-soft);
            color: var(--text-muted);
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .cart-body {
            padding: 1rem;
        }

        .cart-list {
            max-height: 50vh;
            overflow-y: auto;
            min-height: 200px;
        }

        .cart-item {
            background: var(--bg-soft);
            border-radius: 12px;
            padding: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .cart-item-name {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .cart-item-remove {
            color: #ef4444;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .cart-item-remove:hover {
            background: #fef2f2;
        }

        .cart-item-detail {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .cart-item-note {
            width: 100%;
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
            background: white;
        }

        .cart-item-note:focus {
            outline: none;
            border-color: var(--accent);
        }

        .cart-footer {
            border-top: 1px solid var(--border-soft);
            padding: 1rem;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .cart-total-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .cart-total-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .btn-checkout {
            width: 100%;
            padding: 0.85rem;
            border-radius: 12px;
            background: var(--accent);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-checkout:hover:not(:disabled) {
            background: #2563eb;
        }

        .btn-checkout:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            opacity: 0.4;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            margin: 0;
            font-size: 0.9rem;
        }

        .loading-state {
            text-align: center;
            padding: 2rem;
        }

        .loading-state .spinner-border {
            width: 2rem;
            height: 2rem;
            color: var(--accent);
        }

        /* Scrollbar */
        .cart-list::-webkit-scrollbar {
            width: 4px;
        }

        .cart-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .cart-list::-webkit-scrollbar-thumb {
            background: var(--border-soft);
            border-radius: 4px;
        }
    </style>

    {{-- Header --}}
    <div class="page-header">
        <h2><i class="mdi mdi-food me-2"></i>Kantin</h2>
        <p>Pilih menu dan lakukan pemesanan</p>
    </div>

    <div class="row g-4">
        {{-- Menu Section --}}
        <div class="col-lg-8">
            {{-- Filter --}}
            <div class="filter-bar">
                <select id="v-select">
                    <option value="">Pilih Vendor</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                    @endforeach
                </select>
                <input type="text" id="search-menu" placeholder="Cari menu...">
            </div>

            {{-- Menu Grid --}}
            <div id="menu-area" class="row">
                <div class="col-12">
                    <div class="empty-state">
                        <i class="mdi mdi-store-outline"></i>
                        <p>Silakan pilih vendor terlebih dahulu</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cart Section --}}
        <div class="col-lg-4">
            <div class="cart-card sticky-top" style="top: 90px;">
                <div class="cart-header">
                    <h5><i class="mdi mdi-cart-outline me-1"></i>Keranjang</h5>
                    <span class="cart-badge" id="cart-count">0</span>
                </div>
                <div class="cart-body">
                    <div class="cart-list" id="cart-list">
                        <div class="empty-state">
                            <i class="mdi mdi-cart-off"></i>
                            <p>Keranjang kosong</p>
                        </div>
                    </div>
                </div>
                <div class="cart-footer">
                    <div class="cart-total">
                        <span class="cart-total-label">Total</span>
                        <span class="cart-total-value">Rp <span id="total-val">0</span></span>
                    </div>
                    <button id="btn-pay" class="btn-checkout" disabled>Bayar Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@push('scripts')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            let cart = [];
            let total = 0;

            $('#search-menu').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('.menu-card').parent().toggle(
                    $(this).find('.menu-title').text().toLowerCase().indexOf(value) > -1
                );
            });

            $('#v-select').change(function () {
                let id = $(this).val();
                if (!id) {
                    $('#menu-area').html('<div class="col-12"><div class="empty-state"><i class="mdi mdi-store-outline"></i><p>Silakan pilih vendor terlebih dahulu</p></div></div>');
                    return;
                }

                $('#menu-area').html('<div class="col-12"><div class="loading-state"><div class="spinner-border"></div></div></div>');

                $.get('/kantin/menu/' + id, function (data) {
                    if (data.length === 0) {
                        $('#menu-area').html('<div class="col-12"><div class="empty-state"><i class="mdi mdi-food-off"></i><p>Belum ada menu</p></div></div>');
                        return;
                    }

                    let html = '';
                    data.forEach(m => {
                        let img = m.path_gambar ? '/storage/' + m.path_gambar : 'https://via.placeholder.com/300x200/f1f5f9/94a3b8?text=No+Image';
                        html += `
                            <div class="col-md-6 col-xl-4 mb-3">
                                <div class="menu-card">
                                    <div class="menu-img"><img src="${img}" alt="${m.nama_menu}" onerror="this.src='https://via.placeholder.com/300x200/f1f5f9/94a3b8?text=No+Image'"></div>
                                    <div class="menu-body">
                                        <div class="menu-title">${m.nama_menu}</div>
                                        <div class="menu-price">Rp ${Number(m.harga).toLocaleString('id-ID')}</div>
                                        <div class="menu-actions">
                                            <button class="qty-btn" data-id="${m.idmenu}">−</button>
                                            <span class="qty-display" id="qty-${m.idmenu}">0</span>
                                            <button class="qty-btn add" data-id="${m.idmenu}">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#menu-area').html(html);
                    cart.forEach(item => updateQty(item.idmenu));
                });
            });

            $(document).on('click', '.qty-btn', function () {
                let id = Number($(this).data('id'));
                let isAdd = $(this).hasClass('add');
                let card = $(this).closest('.menu-card');
                let nama = card.find('.menu-title').text();
                let harga = Number(card.find('.menu-price').text().replace(/[^0-9]/g, ''));

                let item = cart.find(i => i.idmenu === id);
                if (item) {
                    if (isAdd) item.qty++;
                    else if (item.qty > 1) item.qty--;
                    else cart = cart.filter(i => i.idmenu !== id);
                } else if (isAdd) {
                    cart.push({ idmenu: id, nama: nama, harga: harga, qty: 1, catatan: '' });
                }

                updateQty(id);
                renderCart();
            });

            $(document).on('input', '.cart-item-note', function () {
                let idx = $(this).data('idx');
                cart[idx].catatan = $(this).val();
            });

            $(document).on('click', '.cart-item-remove', function () {
                let idx = $(this).data('idx');
                let id = cart[idx].idmenu;
                cart.splice(idx, 1);
                updateQty(id);
                renderCart();
            });

            function updateQty(id) {
                let item = cart.find(i => i.idmenu === id);
                $(`#qty-${id}`).text(item ? item.qty : 0);
            }

            function renderCart() {
                let html = '';
                total = 0;

                if (cart.length === 0) {
                    $('#cart-list').html('<div class="empty-state"><i class="mdi mdi-cart-off"></i><p>Keranjang kosong</p></div>');
                    $('#total-val').text('0');
                    $('#cart-count').text('0');
                    $('#btn-pay').prop('disabled', true);
                    return;
                }

                cart.forEach((item, idx) => {
                    total += item.harga * item.qty;
                    html += `
                        <div class="cart-item">
                            <div class="cart-item-header">
                                <div class="cart-item-name">${item.nama}</div>
                                <span class="cart-item-remove" data-idx="${idx}"><i class="mdi mdi-close"></i></span>
                            </div>
                            <div class="cart-item-detail">${item.qty} × Rp ${item.harga.toLocaleString('id-ID')}</div>
                            <input type="text" class="cart-item-note" placeholder="Catatan..." data-idx="${idx}" value="${item.catatan || ''}">
                        </div>`;
                });

                $('#cart-list').html(html);
                $('#total-val').text(total.toLocaleString('id-ID'));
                $('#cart-count').text(cart.reduce((sum, i) => sum + i.qty, 0));
                $('#btn-pay').prop('disabled', false);
            }

            $('#btn-pay').click(function (e) {
                e.preventDefault();
                const btn = $(this);

                if (cart.length === 0 || total === 0) return;

                btn.prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: '/kantin/checkout',
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}", cart: cart, total_bayar: total },
                    success: function (res) {
                        if (res.status === 'success') {
                            const orderId = res.order_id;
                            localStorage.setItem('last_order_id', orderId);
                            sessionStorage.setItem('last_order_id', orderId);

                            window.snap.pay(res.token, {
                                onSuccess: () => window.location.href = '/kantin/sukses?order_id=' + orderId,
                                onPending: () => window.location.href = '/kantin/sukses?order_id=' + orderId,
                                onError: () => {
                                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Pembayaran gagal.', confirmButtonColor: '#3b82f6' });
                                    btn.prop('disabled', false).text('Bayar Sekarang');
                                },
                                onClose: () => window.location.href = '/kantin/sukses?order_id=' + orderId
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Gagal.', confirmButtonColor: '#3b82f6' });
                            btn.prop('disabled', false).text('Bayar Sekarang');
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.', confirmButtonColor: '#3b82f6' });
                        btn.prop('disabled', false).text('Bayar Sekarang');
                    }
                });
            });
        });
    </script>
@endpush