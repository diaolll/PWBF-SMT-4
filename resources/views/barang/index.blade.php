@extends('layouts.Template')

@section('content')
<style>
    /* Styling tambahan untuk scrollable table dan fixed footer */
    .table-responsive-scroll {
        max-height: 400px; /* Batas tinggi scroll */
        overflow-y: auto;
        border: 1px solid #ebedf2;
    }
    
    /* Agar header tabel tetap terlihat saat di-scroll */
    .table-responsive-scroll thead th {
        position: sticky;
        top: 0;
        background-color: #f2edf3;
        z-index: 1;
        border-bottom: 2px solid #ebedf2;
    }

    /* Padding bawah agar konten tidak tertutup fixed footer */
    .content-wrapper {
        padding-bottom: 70px; 
    }

    /* Gaya Fixed Footer */
    footer.footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        background: #fff;
        border-top: 1px solid #e3e3e3;
        z-index: 1000;
        left: 0;
        padding: 15px 2.5rem;
    }
    
    /* Penyesuaian lebar footer jika ada sidebar (Bootstrap Purple Admin) */
    @media (min-width: 992px) {
        footer.footer {
            width: calc(100% - 260px);
            left: 260px;
        }
    }
</style>

<div class="content-wrapper">
    {{-- Area Notifikasi --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
        <i class="mdi mdi-check-circle me-2"></i>
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <strong>Gagal!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-package-variant-closed"></i>
            </span> Manajemen Barang
        </h3>
    </div>

    <div class="row">
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Input Barang</h4>
                    <form action="{{ route('barang.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="nama" class="form-control border-primary" required placeholder="Nama Barang">
                        </div>
                        <div class="form-group">
                            <label>Harga</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-gradient-primary text-white">Rp</span>
                                </div>
                                <input type="number" name="harga" class="form-control border-primary" required placeholder="Harga">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary w-100">
                            <i class="mdi mdi-plus-circle btn-icon-prepend"></i> Simpan Barang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('barang.pdf') }}" method="POST" target="_blank">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Daftar Barang</h4>
                            <button type="submit" class="btn btn-gradient-danger btn-sm btn-icon-text">
                                <i class="mdi mdi-printer btn-icon-prepend"></i> Cetak PDF
                            </button>
                        </div>

                        <div class="row mb-3 p-3 bg-light rounded mx-0 border">
                            <div class="col-6">
                                <label class="small font-weight-bold">Kolom Mulai (X)</label>
                                <input type="number" name="x" class="form-control form-control-sm" value="1" min="1" max="5">
                            </div>
                            <div class="col-6">
                                <label class="small font-weight-bold">Baris Mulai (Y)</label>
                                <input type="number" name="y" class="form-control form-control-sm" value="1" min="1" max="8">
                            </div>
                        </div>

                        <div class="table-responsive-scroll rounded">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAll"></th>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangs as $item)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $item->id_barang }}" class="item-checkbox"></td>
                                        <td><span class="badge badge-outline-primary">{{ $item->id_barang }}</span></td>
                                        <td class="text-wrap" style="max-width: 150px;">{{ $item->nama }}</td>
                                        <td class="font-weight-bold">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('barang.edit', $item->id_barang) }}" class="btn btn-inverse-warning btn-sm p-2 border-0">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-inverse-danger btn-sm p-2 border-0" onclick="confirmDelete('{{ $item->id_barang }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Footer Fixed --}}
<footer class="footer">
    <div class="container-fluid d-flex justify-content-between">
        <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">Copyright © HIMTI UNAIR 2026</span>
        <span class="float-none float-sm-end mt-1 mt-sm-0 text-end"> GDGOC UNAIR UI/UX Speaker</span>
    </div>
</footer>

{{-- Hidden Form Delete --}}
<form id="delete-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Check All
    document.getElementById('checkAll').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Delete Confirmation
    function confirmDelete(id) {
        if (confirm('Yakin ingin menghapus barang ini?')) {
            let form = document.getElementById('delete-form');
            form.action = '/barang/' + id;
            form.submit();
        }
    }

    // Auto close alert
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 3000);
</script>
@endsection