{{-- resources/views/pos/index_axios.blade.php --}}
@extends('layouts.template')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">Point of Sales (POS)</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Modul 4</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kasir</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">

    {{-- Kolom Kiri: Form Input Barang --}}
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Input Barang</h4>
                    <div>
                        <a href="{{ route('pos.index') }}" class="btn btn-outline-primary btn-sm me-1">
                            <i class="mdi mdi-code-tags me-1"></i> jQuery
                        </a>
                        <a href="{{ route('pos.axios') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-lightning-bolt me-1"></i> Axios
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kode-barang">Kode Barang</label>
                    <div class="input-group">
                        <input type="text"
                               id="kode-barang"
                               class="form-control text-uppercase"
                               placeholder="Ketik kode & tekan Enter"
                               autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btn-cari">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </div>
                    <small id="status-barang" class="form-text"></small>
                </div>

                <div class="form-group">
                    <label for="nama-barang">Nama Barang</label>
                    <input type="text" id="nama-barang" class="form-control"
                           style="background-color:#fff8e1;" placeholder="Otomatis terisi" readonly>
                </div>

                <div class="form-group">
                    <label for="harga-barang">Harga Satuan</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" id="harga-barang" class="form-control"
                               style="background-color:#fff8e1;" placeholder="Otomatis terisi" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" id="jumlah" class="form-control"
                           value="1" min="1" placeholder="Masukan jumlah">
                </div>

                <button id="btn-tambah" class="btn btn-success btn-block" disabled>
                    <i class="mdi mdi-plus-circle me-1"></i> Tambahkan
                </button>

            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Tabel Transaksi --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Keranjang Belanja</h4>
                    <span id="badge-item" class="badge badge-warning">0 item</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tbl-transaksi">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th>Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-transaksi">
                            <tr id="empty-row">
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="mdi mdi-cart-outline mdi-36px d-block mb-2"></i>
                                    Belum ada barang di keranjang
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold">TOTAL BAYAR :</td>
                                <td colspan="2" class="font-weight-bold text-primary" id="grand-total">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button id="btn-bayar" class="btn btn-success btn-lg" disabled>
                        <i class="mdi mdi-cash me-1"></i> Bayar
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- CSRF Token di meta tag untuk Axios --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Set CSRF Token global untuk semua request Axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';

let barangAktif = null;

function formatRupiah(angka) {
    return parseInt(angka).toLocaleString('id-ID');
}

function hitungTotal() {
    let grandTotal = 0;
    let jumlahItem = 0;
    document.querySelectorAll('#tbody-transaksi tr[data-kode]').forEach(function (baris) {
        const harga    = parseInt(baris.dataset.harga);
        const jumlah   = parseInt(baris.querySelector('.qty-input').value) || 0;
        const subtotal = harga * jumlah;
        baris.querySelector('.subtotal-cell').textContent = 'Rp ' + formatRupiah(subtotal);
        grandTotal += subtotal;
        jumlahItem++;
    });
    document.getElementById('grand-total').textContent = 'Rp ' + formatRupiah(grandTotal);
    document.getElementById('badge-item').textContent  = jumlahItem + ' item';
    document.getElementById('btn-bayar').disabled      = jumlahItem === 0;
    document.getElementById('empty-row').style.display = jumlahItem === 0 ? '' : 'none';
}

function resetFormBarang() {
    document.getElementById('kode-barang').value  = '';
    document.getElementById('nama-barang').value  = '';
    document.getElementById('harga-barang').value = '';
    document.getElementById('jumlah').value       = '1';
    const status = document.getElementById('status-barang');
    status.textContent = '';
    status.className   = 'form-text';
    document.getElementById('btn-tambah').disabled = true;
    document.getElementById('kode-barang').focus();
    barangAktif = null;
}

function cariBarang() {
    const kode = document.getElementById('kode-barang').value.trim().toUpperCase();
    document.getElementById('kode-barang').value = kode;
    if (!kode) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Kode barang tidak boleh kosong!', timer: 1500, showConfirmButton: false });
        return;
    }
    const status = document.getElementById('status-barang');
    status.textContent = 'Mencari...';
    status.className   = 'form-text text-muted';
    document.getElementById('btn-tambah').disabled = true;
    barangAktif = null;

    axios.get(`/api/barang/${kode}`)
        .then(function (response) {
            barangAktif = response.data.data;
            document.getElementById('nama-barang').value  = barangAktif.nama;
            document.getElementById('harga-barang').value = formatRupiah(barangAktif.harga);
            document.getElementById('jumlah').value       = '1';
            status.textContent = '✔ Barang ditemukan';
            status.className   = 'form-text text-success';
            document.getElementById('btn-tambah').disabled = false;
            document.getElementById('jumlah').focus();
        })
        .catch(function () {
            barangAktif = null;
            document.getElementById('nama-barang').value  = '';
            document.getElementById('harga-barang').value = '';
            status.textContent = '✘ Barang tidak ditemukan';
            status.className   = 'form-text text-danger';
            document.getElementById('btn-tambah').disabled = true;
        });
}

// Enter → cari barang
document.getElementById('kode-barang').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') cariBarang();
});

// Klik tombol cari
document.getElementById('btn-cari').addEventListener('click', cariBarang);

// Jumlah berubah
document.getElementById('jumlah').addEventListener('input', function () {
    const jumlah = parseInt(this.value);
    document.getElementById('btn-tambah').disabled = !barangAktif || isNaN(jumlah) || jumlah <= 0;
});

// Tombol Tambahkan
document.getElementById('btn-tambah').addEventListener('click', function () {
    if (!barangAktif) return;
    const jumlah = parseInt(document.getElementById('jumlah').value);
    if (!jumlah || jumlah <= 0) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Jumlah harus lebih dari 0!', timer: 1500, showConfirmButton: false });
        return;
    }
    const kode     = barangAktif.id_barang;
    const nama     = barangAktif.nama;
    const harga    = barangAktif.harga;
    const subtotal = harga * jumlah;

    const existing = document.querySelector(`#tbody-transaksi tr[data-kode="${kode}"]`);
    if (existing) {
        const qtyInput = existing.querySelector('.qty-input');
        qtyInput.value = parseInt(qtyInput.value) + jumlah;
        hitungTotal();
    } else {
        const tbody = document.getElementById('tbody-transaksi');
        const tr    = document.createElement('tr');
        tr.setAttribute('data-kode', kode);
        tr.setAttribute('data-harga', harga);
        tr.innerHTML = `
            <td><code>${kode}</code></td>
            <td>${nama}</td>
            <td>Rp ${formatRupiah(harga)}</td>
            <td class="text-center">
                <input type="number" class="form-control form-control-sm qty-input text-center" value="${jumlah}" min="1" style="width:80px;margin:auto;">
            </td>
            <td class="subtotal-cell">Rp ${formatRupiah(subtotal)}</td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm btn-hapus">
                    <i class="mdi mdi-delete"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
        hitungTotal();
    }
    resetFormBarang();
});

// Perubahan qty di tabel
document.getElementById('tbody-transaksi').addEventListener('input', function (e) {
    if (e.target.classList.contains('qty-input')) {
        const val = parseInt(e.target.value);
        if (isNaN(val) || val < 1) e.target.value = 1;
        hitungTotal();
    }
});

// Hapus baris
document.getElementById('tbody-transaksi').addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-hapus');
    if (btn) { btn.closest('tr').remove(); hitungTotal(); }
});

// Tombol Bayar
document.getElementById('btn-bayar').addEventListener('click', function () {
    const items = [];
    document.querySelectorAll('#tbody-transaksi tr[data-kode]').forEach(function (baris) {
        items.push({
            id_barang : baris.dataset.kode,
            jumlah    : parseInt(baris.querySelector('.qty-input').value) || 0,
            subtotal  : parseInt(baris.dataset.harga) * (parseInt(baris.querySelector('.qty-input').value) || 0),
        });
    });
    if (items.length === 0) return;

    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: `Proses transaksi dengan ${items.length} item?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3f51b5',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal',
    }).then(function (result) {
        if (!result.isConfirmed) return;

        const btnBayar = document.getElementById('btn-bayar');
        btnBayar.disabled   = true;
        btnBayar.innerHTML  = '<i class="mdi mdi-loading mdi-spin me-1"></i> Memproses...';

        axios.post("{{ route('api.bayar') }}", { items: items })
            .then(function (response) {
                if (response.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        html: `ID Transaksi: <b>#${response.data.data.id_penjualan}</b><br>Total: <b>Rp ${formatRupiah(response.data.data.total)}</b>`,
                        confirmButtonColor: '#3f51b5',
                    }).then(function () {
                        document.querySelectorAll('#tbody-transaksi tr[data-kode]').forEach(tr => tr.remove());
                        hitungTotal();
                        resetFormBarang();
                    });
                }
            })
            .catch(function (error) {
                btnBayar.disabled  = false;
                btnBayar.innerHTML = '<i class="mdi mdi-cash me-1"></i> Bayar';
                const msg = error.response && error.response.data ? error.response.data.message : 'Terjadi kesalahan.';
                Swal.fire('Error!', msg, 'error');
            });
    });
});
</script>
@endpush