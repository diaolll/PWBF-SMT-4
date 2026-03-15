@extends('layouts.template')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">Wilayah Administrasi Indonesia</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Modul 4</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Select Wilayah</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Pilih Wilayah</h4>
                    <div>
                        <a href="{{ route('wilayah.index') }}" class="btn btn-primary btn-sm me-1">
                            <i class="mdi mdi-code-tags me-1"></i> jQuery AJAX
                        </a>
                        <a href="{{ route('wilayah.axios') }}" class="btn btn-outline-primary btn-sm">
                            <i class="mdi mdi-lightning-bolt me-1"></i> Axios
                        </a>
                    </div>
                </div>

                <p class="card-description mb-4">
                    Pilih wilayah secara bertahap. Data dimuat dinamis menggunakan <code>jQuery AJAX</code>.
                </p>

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="provinsi">
                                <i class="mdi mdi-map-marker text-primary me-1"></i> Provinsi
                            </label>
                            <select id="provinsi" class="form-control">
                                <option value="0">-- Pilih Provinsi --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kota">
                                <i class="mdi mdi-city text-primary me-1"></i> Kota / Kabupaten
                                <span id="loading-kota" style="display:none">
                                    <i class="mdi mdi-loading mdi-spin text-muted ms-1"></i>
                                </span>
                            </label>
                            <select id="kota" class="form-control" disabled>
                                <option value="0">-- Pilih Kota --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kecamatan">
                                <i class="mdi mdi-map text-primary me-1"></i> Kecamatan
                                <span id="loading-kecamatan" style="display:none">
                                    <i class="mdi mdi-loading mdi-spin text-muted ms-1"></i>
                                </span>
                            </label>
                            <select id="kecamatan" class="form-control" disabled>
                                <option value="0">-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kelurahan">
                                <i class="mdi mdi-home text-primary me-1"></i> Kelurahan / Desa
                                <span id="loading-kelurahan" style="display:none">
                                    <i class="mdi mdi-loading mdi-spin text-muted ms-1"></i>
                                </span>
                            </label>
                            <select id="kelurahan" class="form-control" disabled>
                                <option value="0">-- Pilih Kelurahan --</option>
                            </select>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div id="result-box" style="display:none" class="mt-2">
                            <div class="alert alert-success">
                                <h6 class="alert-heading">
                                    <i class="mdi mdi-check-circle me-1"></i> Alamat Terpilih
                                </h6>
                                <hr>
                                <p class="mb-0" id="result-text"></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function renderOptions(selectId, data, placeholder) {
    let html = `<option value="0">-- ${placeholder} --</option>`;
    $.each(data, function(i, item) {
        html += `<option value="${item.id}">${item.nama}</option>`;
    });
    $(selectId).html(html);
}

function resetSelect(selectId, placeholder) {
    $(selectId).html(`<option value="0">-- ${placeholder} --</option>`).prop('disabled', true);
}

$(document).ready(function () {

    // Load provinsi saat halaman dibuka
    $.ajax({
        url: "{{ route('api.provinsi') }}",
        type: "GET",
        success: function (response) {
            if (response.status === 'success') {
                renderOptions('#provinsi', response.data, 'Pilih Provinsi');
            }
        },
        error: function () {
            Swal.fire('Error!', 'Gagal memuat data provinsi.', 'error');
        }
    });

    // Provinsi berubah → load Kota
    $('#provinsi').on('change', function () {
        const id_provinsi = $(this).val();
        resetSelect('#kota', 'Pilih Kota');
        resetSelect('#kecamatan', 'Pilih Kecamatan');
        resetSelect('#kelurahan', 'Pilih Kelurahan');
        $('#result-box').hide();
        if (id_provinsi == 0) return;
        $('#loading-kota').show();
        $.ajax({
            url: `/api/kota/${id_provinsi}`,
            type: "GET",
            success: function (response) {
                $('#loading-kota').hide();
                if (response.status === 'success') {
                    renderOptions('#kota', response.data, 'Pilih Kota');
                    $('#kota').prop('disabled', false);
                }
            },
            error: function () {
                $('#loading-kota').hide();
                Swal.fire('Error!', 'Gagal memuat data kota.', 'error');
            }
        });
    });

    // Kota berubah → load Kecamatan
    $('#kota').on('change', function () {
        const id_kota = $(this).val();
        resetSelect('#kecamatan', 'Pilih Kecamatan');
        resetSelect('#kelurahan', 'Pilih Kelurahan');
        $('#result-box').hide();
        if (id_kota == 0) return;
        $('#loading-kecamatan').show();
        $.ajax({
            url: `/api/kecamatan/${id_kota}`,
            type: "GET",
            success: function (response) {
                $('#loading-kecamatan').hide();
                if (response.status === 'success') {
                    renderOptions('#kecamatan', response.data, 'Pilih Kecamatan');
                    $('#kecamatan').prop('disabled', false);
                }
            },
            error: function () {
                $('#loading-kecamatan').hide();
                Swal.fire('Error!', 'Gagal memuat data kecamatan.', 'error');
            }
        });
    });

    // Kecamatan berubah → load Kelurahan
    $('#kecamatan').on('change', function () {
        const id_kecamatan = $(this).val();
        resetSelect('#kelurahan', 'Pilih Kelurahan');
        $('#result-box').hide();
        if (id_kecamatan == 0) return;
        $('#loading-kelurahan').show();
        $.ajax({
            url: `/api/kelurahan/${id_kecamatan}`,
            type: "GET",
            success: function (response) {
                $('#loading-kelurahan').hide();
                if (response.status === 'success') {
                    renderOptions('#kelurahan', response.data, 'Pilih Kelurahan');
                    $('#kelurahan').prop('disabled', false);
                }
            },
            error: function () {
                $('#loading-kelurahan').hide();
                Swal.fire('Error!', 'Gagal memuat data kelurahan.', 'error');
            }
        });
    });

    // Kelurahan berubah → tampilkan hasil
    $('#kelurahan').on('change', function () {
        if ($(this).val() == 0) { $('#result-box').hide(); return; }
        const provinsi  = $('#provinsi option:selected').text();
        const kota      = $('#kota option:selected').text();
        const kecamatan = $('#kecamatan option:selected').text();
        const kelurahan = $(this).find('option:selected').text();
        $('#result-text').text(`${kelurahan}, Kec. ${kecamatan}, ${kota}, Prov. ${provinsi}`);
        $('#result-box').show();
    });

});
</script>
@endpush