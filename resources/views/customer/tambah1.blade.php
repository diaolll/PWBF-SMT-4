@extends('layouts.Template')

@section('content')
    <style>
        .photo-upload-area { border: 2px dashed #eaeaec; border-radius: 10px; padding: 30px; text-align: center; transition: all 0.3s; cursor: pointer; }
        .photo-upload-area:hover { border-color: #716aca; background: #f5f3fd; }
        .photo-upload-area.has-photo { border-style: solid; padding: 15px; }
        .photo-placeholder-icon { width: 60px; height: 60px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; }
        .photo-placeholder-icon i { font-size: 1.75rem; color: #6c7293; }
        .photo-preview-img { max-width: 200px; max-height: 200px; border-radius: 10px; object-fit: cover; }
        .modal-content { border: none; border-radius: 15px; }
        .modal-header { border-bottom: 1px solid #eaeaec; border-radius: 15px 15px 0 0; }
        .modal-footer { border-top: 1px solid #eaeaec; border-radius: 0 0 15px 15px; }
        .camera-box video, .camera-box canvas { width: 200px; height: 150px; object-fit: cover; border-radius: 10px; border: 1px solid #eaeaec; background: #f8f9fa; }
    </style>

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-account-plus"></i>
            </span> Tambah Customer (BLOB)
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customer</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah (BLOB)</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Data Customer</h4>
                    <p class="card-description">Isi data customer lengkap dengan foto</p>

                    <form class="forms-sample" method="POST" action="{{ route('customer.store1') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" name="alamat" class="form-control" placeholder="Alamat lengkap">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select id="provinsi" class="form-control" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinsi as $p)
                                            <option value="{{ $p->id }}" data-nama="{{ $p->name }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_nama" id="provinsi_nama">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kota</label>
                                    <select id="kota" class="form-control" disabled>
                                        <option value="">Pilih Kota</option>
                                    </select>
                                    <input type="hidden" name="kota_nama" id="kota_nama">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select id="kecamatan" class="form-control" disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <input type="hidden" name="kecamatan_nama" id="kecamatan_nama">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelurahan / Kodepos</label>
                                    <select id="kelurahan" class="form-control" disabled>
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                    <input type="hidden" name="kelurahan_nama" id="kelurahan_nama">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Foto Customer</label>
                            <div id="photoAreaEmpty" class="photo-upload-area" data-bs-toggle="modal" data-bs-target="#modalKamera">
                                <div class="photo-placeholder-icon">
                                    <i class="mdi mdi-camera-plus"></i>
                                </div>
                                <p class="mb-0">Klik untuk ambil foto</p>
                                <small class="text-muted">Gunakan kamera perangkat Anda</small>
                            </div>

                            <div id="photoAreaHasPhoto" class="photo-upload-area has-photo" style="display: none;">
                                <img id="preview" src="" alt="Foto Customer" class="photo-preview-img">
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKamera">
                                        <i class="mdi mdi-camera-refresh"></i> Ganti Foto
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="foto" id="fotoInput">
                        </div>

                        <button type="submit" class="btn btn-gradient-primary btn-rounded btn-fw">
                            <i class="mdi mdi-content-save"></i> Simpan Customer
                        </button>
                        <a href="{{ route('customer.index') }}" class="btn btn-gradient-light btn-rounded btn-fw">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Kamera --}}
    <div class="modal fade" id="modalKamera" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-camera me-2"></i>Ambil Foto
                        <small id="cameraStatus" class="badge badge-gradient-success ms-2">BELAKANG</small>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3 justify-content-center">
                        <div class="camera-box text-center">
                            <p class="text-muted small mb-2">Live Camera</p>
                            <video id="video" autoplay playsinline muted></video>
                        </div>
                        <div class="camera-box text-center">
                            <p class="text-muted small mb-2">Preview</p>
                            <canvas id="canvas" width="200" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-gradient-light btn-rounded" onclick="gantiKamera()">
                        <i class="mdi mdi-camera-switch"></i> Ganti Kamera
                    </button>
                    <div>
                        <button type="button" class="btn btn-gradient-primary btn-rounded" onclick="ambilFoto()">
                            <i class="mdi mdi-camera"></i> Ambil
                        </button>
                        <button type="button" class="btn btn-gradient-success btn-rounded" data-bs-dismiss="modal" onclick="simpanFoto()">
                            <i class="mdi mdi-check"></i> Gunakan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null, facingMode = 'environment';

        document.getElementById('modalKamera').addEventListener('shown.bs.modal', function() {
            setTimeout(startCamera, 100);
        });

        document.getElementById('modalKamera').addEventListener('hidden.bs.modal', function() {
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            const v = document.getElementById('video'); if (v) v.srcObject = null;
        });

        function startCamera() {
            if (stream) stream.getTracks().forEach(t => t.stop());
            navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } })
                .then(s => {
                    stream = s;
                    document.getElementById('video').srcObject = stream;
                    document.getElementById('video').play();
                    const b = document.getElementById('cameraStatus');
                    b.textContent = facingMode === 'user' ? 'DEPAN' : 'BELAKANG';
                    b.className = facingMode === 'user' ? 'badge badge-gradient-primary ms-2' : 'badge badge-gradient-success ms-2';
                })
                .catch(e => alert('Error: ' + e.message));
        }

        function gantiKamera() { facingMode = facingMode === 'user' ? 'environment' : 'user'; startCamera(); }

        function ambilFoto() {
            const c = document.getElementById('canvas'), ctx = c.getContext('2d'), v = document.getElementById('video');
            c.width = v.videoWidth || 640; c.height = v.videoHeight || 480;
            ctx.drawImage(v, 0, 0, c.width, c.height);
        }

        function simpanFoto() {
            const dataUrl = document.getElementById('canvas').toDataURL('image/png');
            document.getElementById('fotoInput').value = dataUrl;
            document.getElementById('preview').src = dataUrl;
            document.getElementById('photoAreaEmpty').style.display = 'none';
            document.getElementById('photoAreaHasPhoto').style.display = 'block';
        }

        // Wilayah berjenjang
        document.getElementById('provinsi').addEventListener('change', function() {
            const id = this.value, nama = this.options[this.selectedIndex].text;
            document.getElementById('provinsi_nama').value = nama;
            resetSelect('kota'); resetSelect('kecamatan'); resetSelect('kelurahan');
            if (!id) return;
            fetch(`/api/kota/${id}`).then(r => r.json()).then(res => fillSelect('kota', res.data, 'id', 'nama'));
        });

        document.getElementById('kota').addEventListener('change', function() {
            const id = this.value, nama = this.options[this.selectedIndex].text;
            document.getElementById('kota_nama').value = nama;
            resetSelect('kecamatan'); resetSelect('kelurahan');
            if (!id) return;
            fetch(`/api/kecamatan/${id}`).then(r => r.json()).then(res => fillSelect('kecamatan', res.data, 'id', 'nama'));
        });

        document.getElementById('kecamatan').addEventListener('change', function() {
            const id = this.value, nama = this.options[this.selectedIndex].text;
            document.getElementById('kecamatan_nama').value = nama;
            resetSelect('kelurahan');
            if (!id) return;
            fetch(`/api/kelurahan/${id}`).then(r => r.json()).then(res => fillSelect('kelurahan', res.data, 'id', 'nama'));
        });

        document.getElementById('kelurahan').addEventListener('change', function() {
            document.getElementById('kelurahan_nama').value = this.options[this.selectedIndex].text;
        });

        function resetSelect(id) {
            const el = document.getElementById(id);
            el.innerHTML = '<option value="">Pilih</option>';
            el.disabled = true;
        }

        function fillSelect(id, data, valKey, textKey) {
            const el = document.getElementById(id);
            el.disabled = false;
            data.forEach(item => el.innerHTML += `<option value="${item[valKey]}">${item[textKey]}</option>`);
        }
    </script>
@endsection
