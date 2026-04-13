@extends('layouts.Template')

@section('content')
    <style>
        :root {
            --border-soft: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
            --accent: #3b82f6;
            --bg-soft: #f8fafc;
        }

        .page-header {
            background: white;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            padding: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select:disabled {
            background: var(--bg-soft);
        }

        /* Photo Upload Area - UX Improvement */
        .photo-upload-area {
            border: 2px dashed var(--border-soft);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .photo-upload-area:hover {
            border-color: var(--accent);
            background: #f0f9ff;
        }

        .photo-upload-area.has-photo {
            border-style: solid;
            padding: 1rem;
        }

        .photo-upload-area.empty {
            min-height: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .photo-placeholder-icon {
            width: 64px;
            height: 64px;
            background: var(--bg-soft);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .photo-placeholder-icon i {
            font-size: 1.75rem;
            color: var(--text-muted);
        }

        .photo-placeholder-text {
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .photo-placeholder-subtext {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .photo-preview-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-camera {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            background: var(--accent);
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .btn-camera:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-camera i {
            font-size: 1.1rem;
        }

        .btn-change-photo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: var(--bg-soft);
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            margin-top: 0.75rem;
        }

        .btn-change-photo:hover {
            background: #e2e8f0;
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #2563eb; }

        .btn-secondary { background: white; border: 1px solid var(--border-soft); color: var(--text-dark); }
        .btn-secondary:hover { background: var(--bg-soft); }

        .btn-outline { background: white; border: 1px solid var(--border-soft); color: var(--text-dark); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        /* Modal */
        .modal-content {
            border: 1px solid var(--border-soft);
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid var(--border-soft);
            padding: 1.25rem 1.5rem;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-soft);
            padding: 1.25rem 1.5rem;
        }

        .camera-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .camera-box {
            text-align: center;
        }

        .camera-box p {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .camera-box video,
        .camera-box canvas {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            background: var(--bg-soft);
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-actions-right {
            display: flex;
            gap: 0.5rem;
        }

        .btn-modal {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-modal-outline {
            background: white;
            border: 1px solid var(--border-soft);
            color: var(--text-dark);
        }

        .btn-modal-outline:hover {
            background: var(--bg-soft);
        }

        .btn-modal-primary {
            background: var(--accent);
            color: white;
        }

        .btn-modal-primary:hover {
            background: #2563eb;
        }

        .btn-modal-success {
            background: #10b981;
            color: white;
        }

        .btn-modal-success:hover {
            background: #059669;
        }
    </style>

    <div class="page-header">
        <div>
            <a href="{{ route('customer.index') }}" class="btn-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.85rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 0.5rem;">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
            <h2 style="margin-top: 0.5rem;">Tambah Customer (BLOB)</h2>
        </div>
    </div>

    <form class="form-card" method="POST" action="{{ route('customer.store1') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
            </div>

            <div class="col-12">
                <label class="form-label">Alamat</label>
                <input type="text" name="alamat" class="form-control" placeholder="Alamat lengkap">
            </div>

            <div class="col-md-6">
                <label class="form-label">Provinsi</label>
                <select id="provinsi" class="form-select" required>
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsi as $p)
                        <option value="{{ $p->id }}" data-nama="{{ $p->name }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="provinsi_nama" id="provinsi_nama">
            </div>

            <div class="col-md-6">
                <label class="form-label">Kota</label>
                <select id="kota" class="form-select" disabled>
                    <option value="">Pilih Kota</option>
                </select>
                <input type="hidden" name="kota_nama" id="kota_nama">
            </div>

            <div class="col-md-6">
                <label class="form-label">Kecamatan</label>
                <select id="kecamatan" class="form-select" disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
                <input type="hidden" name="kecamatan_nama" id="kecamatan_nama">
            </div>

            <div class="col-md-6">
                <label class="form-label">Kelurahan / Kodepos</label>
                <select id="kelurahan" class="form-select" disabled>
                    <option value="">Pilih Kelurahan</option>
                </select>
                <input type="hidden" name="kelurahan_nama" id="kelurahan_nama">
            </div>

            <div class="col-12">
                <label class="form-label">Foto Customer</label>

                {{-- Empty State --}}
                <div id="photoAreaEmpty" class="photo-upload-area empty" data-bs-toggle="modal" data-bs-target="#modalKamera">
                    <div class="photo-placeholder-icon">
                        <i class="mdi mdi-camera-plus"></i>
                    </div>
                    <div class="photo-placeholder-text">Klik untuk ambil foto</div>
                    <div class="photo-placeholder-subtext">Gunakan kamera perangkat Anda</div>
                </div>

                {{-- Has Photo State --}}
                <div id="photoAreaHasPhoto" class="photo-upload-area has-photo" style="display: none;">
                    <img id="preview" src="" alt="Foto Customer" class="photo-preview-img">
                    <br>
                    <button type="button" class="btn-change-photo" data-bs-toggle="modal" data-bs-target="#modalKamera">
                        <i class="mdi mdi-camera-refresh"></i> Ganti Foto
                    </button>
                </div>

                <input type="hidden" name="foto" id="fotoInput">
            </div>

            <div class="col-12">
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Customer
                    </button>
                    <a href="{{ route('customer.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal Kamera --}}
    <div class="modal fade" id="modalKamera" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-camera me-2"></i>Ambil Foto
                        <small id="cameraStatus" class="badge bg-secondary ms-2">Kamera Depan</small>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="camera-container">
                        <div class="camera-box">
                            <p>Live Camera</p>
                            <video id="video" autoplay playsinline muted></video>
                        </div>
                        <div class="camera-box">
                            <p>Preview</p>
                            <canvas id="canvas" width="200" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-actions w-100">
                        <button type="button" class="btn-modal btn-modal-outline" onclick="gantiKamera()">
                            <i class="mdi mdi-camera-switch"></i> Ganti Kamera
                        </button>
                        <div class="modal-actions-right">
                            <button type="button" class="btn-modal btn-modal-primary" onclick="ambilFoto()">
                                <i class="mdi mdi-camera"></i> Ambil
                            </button>
                            <button type="button" class="btn-modal btn-modal-success" data-bs-dismiss="modal" onclick="simpanFoto()">
                                <i class="mdi mdi-check"></i> Gunakan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let stream = null;
    let facingMode = 'environment'; // Default: kamera belakang

    document.getElementById('modalKamera').addEventListener('shown.bs.modal', function() {
        // Gunakan shown (bukan show) - setelah animasi selesai
        setTimeout(startCamera, 100);
    });

    document.getElementById('modalKamera').addEventListener('hidden.bs.modal', function() {
        stopCamera();
    });

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        const video = document.getElementById('video');
        if (video) video.srcObject = null;
    }

    function startCamera() {
        stopCamera();

        const video = document.getElementById('video');
        const constraints = {
            video: {
                facingMode: facingMode
            }
        };

        console.log('Requesting camera:', facingMode);

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(newStream) {
                stream = newStream;
                video.srcObject = stream;
                video.play()
                    .then(() => console.log('Camera started:', facingMode))
                    .catch(e => console.log('Play failed:', e));

                // Update badge
                const badge = document.getElementById('cameraStatus');
                if (badge) {
                    badge.textContent = (facingMode === 'user') ? 'DEPAN' : 'BELAKANG';
                    badge.className = (facingMode === 'user')
                        ? 'badge bg-primary ms-2'
                        : 'badge bg-success ms-2';
                }
            })
            .catch(function(err) {
                console.error('Camera error:', err);
                alert('Error: ' + err.name + '\n' + err.message);
            });
    }

    function gantiKamera() {
        facingMode = (facingMode === 'user') ? 'environment' : 'user';
        console.log('Switch to:', facingMode);
        startCamera();
    }

    function ambilFoto() {
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const video = document.getElementById('video');

        // Set canvas size sama dengan video
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    }

    function simpanFoto() {
        const canvas = document.getElementById('canvas');
        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById('fotoInput').value = dataUrl;

        document.getElementById('preview').src = dataUrl;
        document.getElementById('photoAreaEmpty').style.display = 'none';
        document.getElementById('photoAreaHasPhoto').style.display = 'block';
    }

    function ambilFoto() {
        const canvas = document.getElementById('canvas');
        canvas.getContext('2d').drawImage(document.getElementById('video'), 0, 0, 200, 150);
    }

    function simpanFoto() {
        const canvas = document.getElementById('canvas');
        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById('fotoInput').value = dataUrl;

        // Show photo, hide empty state
        document.getElementById('preview').src = dataUrl;
        document.getElementById('photoAreaEmpty').style.display = 'none';
        document.getElementById('photoAreaHasPhoto').style.display = 'block';
    }

    // Wilayah berjenjang
    document.getElementById('provinsi').addEventListener('change', function () {
        const id = this.value;
        const nama = this.options[this.selectedIndex].text;
        document.getElementById('provinsi_nama').value = nama;
        resetSelect('kota');
        resetSelect('kecamatan');
        resetSelect('kelurahan');
        if (!id) return;
        fetch(`/api/kota/${id}`).then(r => r.json()).then(res => fillSelect('kota', res.data, 'id', 'nama'));
    });

    document.getElementById('kota').addEventListener('change', function () {
        const id = this.value;
        const nama = this.options[this.selectedIndex].text;
        document.getElementById('kota_nama').value = nama;
        resetSelect('kecamatan');
        resetSelect('kelurahan');
        if (!id) return;
        fetch(`/api/kecamatan/${id}`).then(r => r.json()).then(res => fillSelect('kecamatan', res.data, 'id', 'nama'));
    });

    document.getElementById('kecamatan').addEventListener('change', function () {
        const id = this.value;
        const nama = this.options[this.selectedIndex].text;
        document.getElementById('kecamatan_nama').value = nama;
        resetSelect('kelurahan');
        if (!id) return;
        fetch(`/api/kelurahan/${id}`).then(r => r.json()).then(res => fillSelect('kelurahan', res.data, 'id', 'nama'));
    });

    document.getElementById('kelurahan').addEventListener('change', function () {
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
