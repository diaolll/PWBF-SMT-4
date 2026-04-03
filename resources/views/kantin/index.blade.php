@extends('layouts.Template')

@section('content')
    <style>
        .menu-card {
            border-radius: 20px;
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
            background: #fff;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }

        .menu-img-container {
            height: 160px;
            overflow: hidden;
        }

        .menu-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item {
            background: #fff;
            padding: 15px;
            border-radius: 15px;
            border: 1px solid #f2f2f2;
            margin-bottom: 12px;
        }

        .btn-pay {
            padding: 15px;
            font-weight: 700;
            border-radius: 15px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }

        .qty-display {
            font-weight: 700;
            min-width: 30px;
            text-align: center;
        }
    </style>

    <div class="row g-4">
        <div class="col-lg-8">
            <h3 class="fw-bold text-dark mb-4"><i class="mdi mdi-food-fork-drink me-2"></i> Pilih Menu</h3>
            <div class="d-flex gap-3 mb-4">
                <select id="v-select" class="form-select shadow-sm border-0 p-3 flex-grow-1" style="border-radius: 12px;">
                    <option value="">-- Pilih Kantin / Vendor --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                    @endforeach
                </select>
                <input type="text" id="search-menu" class="form-control shadow-sm border-0 p-3"
                    style="width: 250px; border-radius: 12px;" placeholder="Cari menu...">
            </div>
            <div id="menu-area" class="row"></div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 90px; border-radius: 20px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Keranjang Belanja</h5>
                    <div id="cart-list" style="min-height: 250px; max-height: 55vh; overflow-y: auto; padding-right: 5px;">
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-medium text-muted">Total Tagihan</span>
                        <h4 class="fw-bold text-dark mb-0">Rp <span id="total-val">0</span></h4>
                    </div>
                    <button id="btn-pay" class="btn btn-primary btn-pay w-100 text-white shadow-sm" disabled>Bayar
                        Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    {{-- FIX 1: Gunakan config() bukan env() agar tidak null saat di-cache --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@push('scripts')
    <script>
        $(function () {
            // FIX 2: Setup AJAX Header agar CSRF terbaca otomatis
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let cart = [];
            let total = 0;

            $('#search-menu').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('.menu-card').parent().filter(function () {
                    $(this).toggle($(this).find('h6').text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('#v-select').change(function () {
                let id = $(this).val();
                if (!id) { $('#menu-area').html('<p class="text-center py-5 text-muted">Silakan pilih vendor</p>'); return; }
                $('#menu-area').html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>');
                $.get('/kantin/menu/' + id, function (data) {
                    let html = '';
                    data.forEach(m => {
                        let img = m.path_gambar ? '/storage/' + m.path_gambar : 'https://via.placeholder.com/400x300';
                        html += `
                                <div class="col-md-6 col-xl-4 mb-4">
                                    <div class="card menu-card shadow-sm h-100">
                                        <div class="menu-img-container"><img src="${img}"></div>
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-1">${m.nama_menu}</h6>
                                            <div class="text-primary fw-bold">Rp ${Number(m.harga).toLocaleString('id-ID')}</div>
                                            <div class="d-flex align-items-center gap-2 mt-3">
                                                <button class="btn btn-light btn-sm rounded-pill qty-minus" data-id="${m.idmenu}">-</button>
                                                <span class="qty-display" id="qty-${m.idmenu}">0</span>
                                                <button class="btn btn-primary btn-sm rounded-pill qty-plus" data-id="${m.idmenu}">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                    });
                    $('#menu-area').html(html);
                    // Sinkronisasi qty jika ganti vendor tapi barang masih di cart
                    cart.forEach(item => updateQty(item.idmenu));
                });
            });

            $(document).on('click', '.qty-plus', function () {
                let id = Number($(this).data('id'));
                let cardBody = $(this).closest('.card-body');
                let nama = cardBody.find('h6').text();
                let harga = Number(cardBody.find('.text-primary').text().replace(/[^0-9]/g, ''));

                let item = cart.find(i => i.idmenu === id);
                if (item) item.qty++;
                else cart.push({ idmenu: id, nama: nama, harga: harga, qty: 1, catatan: '' });

                updateQty(id); renderCart();
            });

            $(document).on('click', '.qty-minus', function () {
                let id = Number($(this).data('id'));
                let item = cart.find(i => i.idmenu === id);
                if (item) {
                    if (item.qty > 1) item.qty--;
                    else cart = cart.filter(i => i.idmenu !== id);
                }
                updateQty(id); renderCart();
            });

            $(document).on('input', '.note-input', function () {
                let idx = $(this).data('idx');
                cart[idx].catatan = $(this).val();
            });

            function updateQty(id) {
                let item = cart.find(i => i.idmenu === id);
                $(`#qty-${id}`).text(item ? item.qty : 0);
            }

            function renderCart() {
                let html = ''; total = 0;
                if (cart.length === 0) {
                    $('#cart-list').html('<div class="text-center py-5 text-muted small">Keranjang kosong</div>');
                    $('#total-val').text('0'); $('#btn-pay').prop('disabled', true);
                    return;
                }
                cart.forEach((item, idx) => {
                    let subtotal = item.harga * item.qty; total += subtotal;
                    html += `
                            <div class="cart-item shadow-sm">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-bold text-dark small">${item.nama}</div>
                                    <button class="btn btn-link text-danger p-0 remove-item" data-idx="${idx}"><i class="mdi mdi-close-circle"></i></button>
                                </div>
                                <div class="text-muted small mb-2">${item.qty} x Rp ${item.harga.toLocaleString('id-ID')}</div>
                                <input type="text" class="form-control form-control-sm border-0 note-input" style="background:#f8f9fa; border-radius:8px; font-size:11px;" placeholder="Tambahkan catatan..." data-idx="${idx}" value="${item.catatan || ''}">
                            </div>`;
                });
                $('#cart-list').html(html);
                $('#total-val').text(total.toLocaleString('id-ID'));
                $('#btn-pay').prop('disabled', false);
            }

            $(document).on('click', '.remove-item', function () {
                let idx = $(this).data('idx');
                let id = cart[idx].idmenu;
                cart.splice(idx, 1);
                updateQty(id); renderCart();
            });

            // FIX 3: Perbaikan alur Checkout AJAX dan Integrasi Snap
            $('#btn-pay').click(function (e) {
                e.preventDefault();
                const btn = $(this);

                if (cart.length === 0 || total === 0) return;

                console.log('Total:', total, 'Cart:', cart);

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

                $.ajax({
                    url: "/kantin/checkout",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cart: cart,
                        total_bayar: total
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            // Memicu jendela pembayaran Midtrans
                            window.snap.pay(res.token, {
                                onSuccess: function (result) {
                                    Swal.fire('Berhasil!', 'Pembayaran diterima.', 'success').then(() => { location.reload(); });
                                },
                                onPending: function (result) {
                                    Swal.fire('Pending', 'Selesaikan pembayaran di instruksi Midtrans.', 'info').then(() => { location.href = "/pesanan"; });
                                },
                                onError: function (result) {
                                    Swal.fire('Gagal', 'Pembayaran bermasalah.', 'error');
                                    btn.prop('disabled', false).text('Bayar Sekarang');
                                },
                                onClose: function () {
                                    Swal.fire('Batal', 'Kamu menutup jendela pembayaran.', 'warning');
                                    btn.prop('disabled', false).text('Bayar Sekarang');
                                }
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Gagal membuat transaksi.', 'error');
                            btn.prop('disabled', false).text('Bayar Sekarang');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan sistem: ' + xhr.statusText, 'error');
                        btn.prop('disabled', false).text('Bayar Sekarang');
                    }
                });
            });
        });
    </script>
@endpush