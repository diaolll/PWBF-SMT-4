@extends('layouts.template')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">Point of Sales (POS)</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Modul 5</a></li>
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
                        <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm me-1">
                            <i class="mdi mdi-code-tags me-1"></i> jQuery
                        </a>
                        <a href="{{ route('pos.axios') }}" class="btn btn-outline-primary btn-sm">
                            <i class="mdi mdi-lightning-bolt me-1"></i> Axios
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kode-barang">Kode Barang</label>
                    <div class="input-group">
                        <input type="text" id="kode-barang" class="form-control text-uppercase"
                               placeholder="Ketik kode & tekan Enter" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btn-cari">
                                <span id="txt-cari"><i class="mdi mdi-magnify"></i></span>
                                <div id="loader-cari" class="spinner-border spinner-border-sm d-none"></div>
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
                    <input type="number" id="jumlah" class="form-control" value="1" min="1">
                </div>

                <button id="btn-tambah" class="btn btn-success btn-block" disabled>
                    <span id="txt-tambah">Tambahkan</span>
                    <div id="loader-tambah" class="spinner-border spinner-border-sm d-none"></div>
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
                        <span id="txt-bayar">Bayar</span>
                        <div id="loader-bayar" class="spinner-border spinner-border-sm d-none"></div>
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let barangAktif = null;

function formatRupiah(angka) {
    return parseInt(angka).toLocaleString('id-ID');
}

// Toggle loading state — pola sama persis dengan contoh barang
function setCariLoading(loading) {
    $('#btn-cari').prop('disabled', loading);
    $('#txt-cari').toggleClass('d-none', loading);
    $('#loader-cari').toggleClass('d-none', !loading);
}
function setTambahLoading(loading) {
    $('#btn-tambah').prop('disabled', loading);
    $('#txt-tambah').toggleClass('d-none', loading);
    $('#loader-tambah').toggleClass('d-none', !loading);
}
function setBayarLoading(loading) {
    $('#btn-bayar').prop('disabled', loading);
    $('#txt-bayar').toggleClass('d-none', loading);
    $('#loader-bayar').toggleClass('d-none', !loading);
}

function hitungTotal() {
    let grandTotal = 0;
    let jumlahItem = 0;
    $('#tbody-transaksi tr[data-kode]').each(function () {
        const harga    = parseInt($(this).data('harga'));
        const jumlah   = parseInt($(this).find('.qty-input').val()) || 0;
        const subtotal = harga * jumlah;
        $(this).find('.subtotal-cell').text('Rp ' + formatRupiah(subtotal));
        grandTotal += subtotal;
        jumlahItem++;
    });
    $('#grand-total').text('Rp ' + formatRupiah(grandTotal));
    $('#badge-item').text(jumlahItem + ' item');
    $('#btn-bayar').prop('disabled', jumlahItem === 0);
    if (jumlahItem === 0) { $('#empty-row').show(); }
    else                  { $('#empty-row').hide(); }
}

function resetFormBarang() {
    $('#kode-barang').val('').focus();
    $('#nama-barang').val('');
    $('#harga-barang').val('');
    $('#jumlah').val('1');
    $('#status-barang').text('').removeClass('text-success text-danger text-muted');
    $('#btn-tambah').prop('disabled', true);
    barangAktif = null;
}

function cariBarang() {
    const kode = $('#kode-barang').val().trim().toUpperCase();
    $('#kode-barang').val(kode);
    if (!kode) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Kode barang tidak boleh kosong!',
            timer: 1500, showConfirmButton: false });
        return;
    }

    setCariLoading(true);
    $('#status-barang').text('Mencari...').removeClass('text-success text-danger').addClass('text-muted');
    $('#btn-tambah').prop('disabled', true);
    barangAktif = null;

    $.ajax({
        url: `/api/barang/${kode}`,
        type: 'GET',
        success: function (response) {
            setCariLoading(false);
            barangAktif = response.data;
            $('#nama-barang').val(barangAktif.nama);
            $('#harga-barang').val(formatRupiah(barangAktif.harga));
            $('#jumlah').val('1');
            $('#status-barang').text('✔ Barang ditemukan')
                .removeClass('text-muted text-danger').addClass('text-success');
            $('#btn-tambah').prop('disabled', false);
            $('#jumlah').focus();
        },
        error: function () {
            setCariLoading(false);
            barangAktif = null;
            $('#nama-barang, #harga-barang').val('');
            $('#status-barang').text('✘ Barang tidak ditemukan')
                .removeClass('text-muted text-success').addClass('text-danger');
        }
    });
}

$(document).ready(function () {

    $('#kode-barang').on('keydown', function (e) { if (e.key === 'Enter') cariBarang(); });
    $('#btn-cari').on('click', function () { cariBarang(); });

    $('#jumlah').on('input', function () {
        const jumlah = parseInt($(this).val());
        $('#btn-tambah').prop('disabled', !barangAktif || isNaN(jumlah) || jumlah <= 0);
    });

    $('#btn-tambah').on('click', function () {
        if (!barangAktif) return;
        const jumlah = parseInt($('#jumlah').val());
        if (!jumlah || jumlah <= 0) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Jumlah harus lebih dari 0!',
                timer: 1500, showConfirmButton: false });
            return;
        }

        setTambahLoading(true);

        setTimeout(function () {
            const kode     = barangAktif.id_barang;
            const harga    = barangAktif.harga;
            const subtotal = harga * jumlah;

            const existing = $(`#tbody-transaksi tr[data-kode="${kode}"]`);
            if (existing.length > 0) {
                const qtyLama = parseInt(existing.find('.qty-input').val()) || 0;
                existing.find('.qty-input').val(qtyLama + jumlah);
            } else {
                const baris = `
                    <tr data-kode="${kode}" data-harga="${harga}">
                        <td><code>${kode}</code></td>
                        <td>${barangAktif.nama}</td>
                        <td>Rp ${formatRupiah(harga)}</td>
                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm qty-input text-center"
                                   value="${jumlah}" min="1" style="width:80px;margin:auto;">
                        </td>
                        <td class="subtotal-cell">Rp ${formatRupiah(subtotal)}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm btn-hapus">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </td>
                    </tr>`;
                $('#tbody-transaksi').append(baris);
            }
            hitungTotal();
            setTambahLoading(false);
            resetFormBarang();
        }, 400);
    });

    $('#tbody-transaksi').on('input', '.qty-input', function () {
        const val = parseInt($(this).val());
        if (isNaN(val) || val < 1) $(this).val(1);
        hitungTotal();
    });

    $('#tbody-transaksi').on('click', '.btn-hapus', function () {
        $(this).closest('tr').remove();
        hitungTotal();
    });

    $('#btn-bayar').on('click', function () {
        const items = [];
        $('#tbody-transaksi tr[data-kode]').each(function () {
            const kode   = $(this).data('kode');
            const harga  = parseInt($(this).data('harga'));
            const jumlah = parseInt($(this).find('.qty-input').val()) || 0;
            items.push({ id_barang: kode, jumlah: jumlah, subtotal: harga * jumlah });
        });
        if (items.length === 0) return;

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: `Proses transaksi dengan ${items.length} item?`,
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#3f51b5', cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Bayar!', cancelButtonText: 'Batal',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            setBayarLoading(true);

            $.ajax({
                url:         "{{ route('api.bayar') }}",
                type:        'POST',
                data:        JSON.stringify({ items: items }),
                contentType: 'application/json',
                headers:     { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status === 'success') {
                        setBayarLoading(false);
                        Swal.fire({
                            icon: 'success', title: 'Transaksi Berhasil!',
                            html: `ID Transaksi: <b>#${response.data.id_penjualan}</b><br>
                                   Total: <b>Rp ${formatRupiah(response.data.total)}</b>`,
                            confirmButtonColor: '#3f51b5',
                        }).then(function () {
                            $('#tbody-transaksi tr[data-kode]').remove();
                            hitungTotal();
                            resetFormBarang();
                        });
                    }
                },
                error: function (xhr) {
                    setBayarLoading(false);
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });
    });

});
</script>
@endpush