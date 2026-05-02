@extends('layouts.Template')

@section('content')
    <style>
        .menu-card { transition: transform 0.2s; cursor: pointer; }
        .menu-card:hover { transform: translateY(-4px); }
        .menu-img { height: 140px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; overflow: hidden; }
        .menu-img img { width: 100%; height: 100%; object-fit: cover; }
        .qty-btn { width: 36px; height: 36px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .qty-btn.add { background: #716aca; color: white; }
        .qty-btn.add:hover { background: #5a52b5; }
        .qty-btn.minus { background: #eaeaec; color: #6c7293; }
        .qty-btn.minus:hover { background: #d3d3d5; }
        .cart-item { background: #f8f9fa; border-radius: 8px; padding: 12px; margin-bottom: 10px; }
        .cart-item-remove { color: #fd6472; cursor: pointer; }
        .empty-state { text-align: center; padding: 40px 20px; color: #6c7293; }
        .empty-state i { font-size: 3rem; opacity: 0.3; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-food"></i>
            </span> Kantin
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Modul 6</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kantin</li>
            </ul>
        </nav>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('kantin.orders') }}" class="btn btn-gradient-info btn-rounded">
            <i class="mdi mdi-history"></i> Pesanan Saya
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select id="v-select" class="form-control">
                                <option value="">Pilih Vendor</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="search-menu" class="form-control" placeholder="Cari menu...">
                        </div>
                    </div>

                    <div id="menu-area" class="row mt-4">
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="mdi mdi-store-outline"></i>
                                <p>Silakan pilih vendor terlebih dahulu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-cart-outline"></i> Keranjang
                        </h4>
                        <span class="badge badge-gradient-primary" id="cart-count">0</span>
                    </div>

                    <div id="cart-list" style="max-height: 400px; overflow-y: auto;">
                        <div class="empty-state">
                            <i class="mdi mdi-cart-off"></i>
                            <p>Keranjang kosong</p>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total</span>
                            <span class="h4 mb-0 text-primary">Rp <span id="total-val">0</span></span>
                        </div>
                        <button id="btn-pay" class="btn btn-gradient-success btn-rounded btn-fw" disabled>
                            <i class="mdi mdi-credit-card-outline"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@push('scripts')
    <script>
        $(function () {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            let cart = [], total = 0;

            $('#search-menu').on('keyup', function () {
                let val = $(this).val().toLowerCase();
                $('.menu-card').parent().toggle($(this).find('.card-title').text().toLowerCase().indexOf(val) > -1);
            });

            $('#v-select').change(function () {
                let id = $(this).val();
                if (!id) {
                    $('#menu-area').html('<div class="col-12"><div class="empty-state"><i class="mdi mdi-store-outline"></i><p>Silakan pilih vendor</p></div></div>');
                    return;
                }
                $('#menu-area').html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');

                $.get('/kantin/menu/' + id, function (data) {
                    if (data.length === 0) {
                        $('#menu-area').html('<div class="col-12"><div class="empty-state"><i class="mdi mdi-food-off"></i><p>Belum ada menu</p></div></div>');
                        return;
                    }

                    let html = '';
                    data.forEach(m => {
                        let img = m.path_gambar ? '/storage/' + m.path_gambar : 'https://via.placeholder.com/300x200/f1f5f9/94a3b8?text=No+Image';
                        html += `
                            <div class="col-md-6 mb-3">
                                <div class="card menu-card">
                                    <div class="card-body">
                                        <div class="menu-img mb-2"><img src="${img}" onerror="this.src='https://via.placeholder.com/300x200/f1f5f9/94a3b8?text=No+Image'"></div>
                                        <h5 class="card-title">${m.nama_menu}</h5>
                                        <p class="text-primary font-weight-bold">Rp ${Number(m.harga).toLocaleString('id-ID')}</p>
                                        <div class="d-flex align-items-center justify-content-center mt-3">
                                            <button class="qty-btn minus" data-id="${m.idmenu}">−</button>
                                            <span class="mx-3" id="qty-${m.idmenu}" style="min-width: 30px; text-align: center; font-weight: 600;">0</span>
                                            <button class="qty-btn add" data-id="${m.idmenu}">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#menu-area').html(html);
                    cart.forEach(item => $(`#qty-${item.idmenu}`).text(item.qty));
                });
            });

            $(document).on('click', '.qty-btn', function () {
                let id = Number($(this).data('id')), isAdd = $(this).hasClass('add'), card = $(this).closest('.menu-card');
                let nama = card.find('.card-title').text(), harga = Number(card.find('.text-primary').text().replace(/[^0-9]/g, ''));
                let item = cart.find(i => i.idmenu === id);
                if (item) {
                    if (isAdd) item.qty++; else if (item.qty > 1) item.qty--; else cart = cart.filter(i => i.idmenu !== id);
                } else if (isAdd) cart.push({ idmenu: id, nama: nama, harga: harga, qty: 1, catatan: '' });
                $(`#qty-${id}`).text(cart.find(i => i.idmenu === id)?.qty || 0);
                renderCart();
            });

            $(document).on('input', '.cart-item-note', function () { cart[$(this).data('idx')].catatan = $(this).val(); });
            $(document).on('click', '.cart-item-remove', function () {
                let idx = $(this).data('idx'), id = cart[idx].idmenu;
                cart.splice(idx, 1);
                $(`#qty-${id}`).text(cart.find(i => i.idmenu === id)?.qty || 0);
                renderCart();
            });

            function renderCart() {
                let html = ''; total = 0;
                if (cart.length === 0) {
                    $('#cart-list').html('<div class="empty-state"><i class="mdi mdi-cart-off"></i><p>Keranjang kosong</p></div>');
                    $('#total-val').text('0'); $('#cart-count').text('0'); $('#btn-pay').prop('disabled', true);
                    return;
                }
                cart.forEach((item, idx) => {
                    total += item.harga * item.qty;
                    html += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${item.nama}</h6>
                                    <small class="text-muted">${item.qty} × Rp ${item.harga.toLocaleString('id-ID')}</small>
                                </div>
                                <i class="mdi mdi-close cart-item-remove" data-idx="${idx}"></i>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-2 cart-item-note" placeholder="Catatan..." data-idx="${idx}" value="${item.catatan || ''}">
                        </div>`;
                });
                $('#cart-list').html(html);
                $('#total-val').text(total.toLocaleString('id-ID'));
                $('#cart-count').text(cart.reduce((sum, i) => sum + i.qty, 0));
                $('#btn-pay').prop('disabled', false);
            }

            $('#btn-pay').click(function (e) {
                e.preventDefault(); const btn = $(this);
                if (cart.length === 0 || total === 0) return;
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memproses...');

                $.ajax({
                    url: '/kantin/checkout', type: 'POST', data: { _token: "{{ csrf_token() }}", cart: cart, total_bayar: total },
                    success: function (res) {
                        if (res.status === 'success') {
                            let oid = res.order_id;
                            localStorage.setItem('last_order_id', oid);
                            sessionStorage.setItem('last_order_id', oid);
                            window.snap.pay(res.token, {
                                onSuccess: () => window.location.href = '/kantin/sukses?order_id=' + oid,
                                onPending: () => window.location.href = '/kantin/sukses?order_id=' + oid,
                                onError: () => { Swal.fire({ icon: 'error', title: 'Gagal', text: 'Pembayaran gagal.', confirmButtonColor: '#716aca' }); btn.prop('disabled', false).html('<i class="mdi mdi-credit-card-outline"></i> Bayar Sekarang'); },
                                onClose: () => window.location.href = '/kantin/sukses?order_id=' + oid
                            });
                        } else { Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Gagal.', confirmButtonColor: '#716aca' }); btn.prop('disabled', false).html('<i class="mdi mdi-credit-card-outline"></i> Bayar Sekarang'); }
                    },
                    error: function () { Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.', confirmButtonColor: '#716aca' }); btn.prop('disabled', false).html('<i class="mdi mdi-credit-card-outline"></i> Bayar Sekarang'); }
                });
            });
        });
    </script>
@endpush
