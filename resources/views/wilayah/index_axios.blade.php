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
                        <a href="{{ route('wilayah.index') }}" class="btn btn-outline-primary btn-sm me-1">
                            <i class="mdi mdi-code-tags me-1"></i> jQuery AJAX
                        </a>
                        <a href="{{ route('wilayah.axios') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-lightning-bolt me-1"></i> Axios
                        </a>
                    </div>
                </div>

                <p class="card-description mb-4">
                    Pilih wilayah secara bertahap. Data dimuat dinamis menggunakan <code>Axios</code> berbasis Promise.
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function renderOptions(selectId, data, placeholder) {
    const select = document.getElementById(selectId);
    let html = `<option value="0">-- ${placeholder} --</option>`;
    data.forEach(item => {
        html += `<option value="${item.id}">${item.nama}</option>`;
    });
    select.innerHTML = html;
}

function resetSelect(selectId, placeholder) {
    const select = document.getElementById(selectId);
    select.innerHTML = `<option value="0">-- ${placeholder} --</option>`;
    select.disabled = true;
}

// Load provinsi saat halaman dibuka
axios.get("{{ route('api.provinsi') }}")
    .then(function (response) {
        if (response.data.status === 'success') {
            renderOptions('provinsi', response.data.data, 'Pilih Provinsi');
        }
    })
    .catch(function () {
        Swal.fire('Error!', 'Gagal memuat data provinsi.', 'error');
    });

// Provinsi berubah → load Kota
document.getElementById('provinsi').addEventListener('change', function () {
    const id_provinsi = this.value;
    resetSelect('kota', 'Pilih Kota');
    resetSelect('kecamatan', 'Pilih Kecamatan');
    resetSelect('kelurahan', 'Pilih Kelurahan');
    document.getElementById('result-box').style.display = 'none';
    if (id_provinsi == 0) return;
    document.getElementById('loading-kota').style.display = 'inline';
    axios.get(`/api/kota/${id_provinsi}`)
        .then(function (response) {
            document.getElementById('loading-kota').style.display = 'none';
            if (response.data.status === 'success') {
                renderOptions('kota', response.data.data, 'Pilih Kota');
                document.getElementById('kota').disabled = false;
            }
        })
        .catch(function () {
            document.getElementById('loading-kota').style.display = 'none';
            Swal.fire('Error!', 'Gagal memuat data kota.', 'error');
        });
});

// Kota berubah → load Kecamatan
document.getElementById('kota').addEventListener('change', function () {
    const id_kota = this.value;
    resetSelect('kecamatan', 'Pilih Kecamatan');
    resetSelect('kelurahan', 'Pilih Kelurahan');
    document.getElementById('result-box').style.display = 'none';
    if (id_kota == 0) return;
    document.getElementById('loading-kecamatan').style.display = 'inline';
    axios.get(`/api/kecamatan/${id_kota}`)
        .then(function (response) {
            document.getElementById('loading-kecamatan').style.display = 'none';
            if (response.data.status === 'success') {
                renderOptions('kecamatan', response.data.data, 'Pilih Kecamatan');
                document.getElementById('kecamatan').disabled = false;
            }
        })
        .catch(function () {
            document.getElementById('loading-kecamatan').style.display = 'none';
            Swal.fire('Error!', 'Gagal memuat data kecamatan.', 'error');
        });
});

// Kecamatan berubah → load Kelurahan
document.getElementById('kecamatan').addEventListener('change', function () {
    const id_kecamatan = this.value;
    resetSelect('kelurahan', 'Pilih Kelurahan');
    document.getElementById('result-box').style.display = 'none';
    if (id_kecamatan == 0) return;
    document.getElementById('loading-kelurahan').style.display = 'inline';
    axios.get(`/api/kelurahan/${id_kecamatan}`)
        .then(function (response) {
            document.getElementById('loading-kelurahan').style.display = 'none';
            if (response.data.status === 'success') {
                renderOptions('kelurahan', response.data.data, 'Pilih Kelurahan');
                document.getElementById('kelurahan').disabled = false;
            }
        })
        .catch(function () {
            document.getElementById('loading-kelurahan').style.display = 'none';
            Swal.fire('Error!', 'Gagal memuat data kelurahan.', 'error');
        });
});

// Kelurahan berubah → tampilkan hasil
document.getElementById('kelurahan').addEventListener('change', function () {
    if (this.value == 0) { document.getElementById('result-box').style.display = 'none'; return; }
    const provinsi  = document.getElementById('provinsi').options[document.getElementById('provinsi').selectedIndex].text;
    const kota      = document.getElementById('kota').options[document.getElementById('kota').selectedIndex].text;
    const kecamatan = document.getElementById('kecamatan').options[document.getElementById('kecamatan').selectedIndex].text;
    const kelurahan = this.options[this.selectedIndex].text;
    document.getElementById('result-text').textContent = `${kelurahan}, Kec. ${kecamatan}, ${kota}, Prov. ${provinsi}`;
    document.getElementById('result-box').style.display = 'block';
});
</script>
@endpush