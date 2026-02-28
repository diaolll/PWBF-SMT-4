@extends('layouts.Template')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-warning text-white me-2">
            <i class="mdi mdi-pencil"></i>
        </span> Edit Barang
    </h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Manajemen Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ul>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Perbarui Informasi Barang</h4>
                <p class="card-description"> ID Barang: <code>{{ $barang->id_barang }}</code> </p>
                <form class="forms-sample" action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="editNama">Nama Barang</label>
                        <input type="text" name="nama" class="form-control" id="editNama" value="{{ $barang->nama }}" required>
                    </div>

                    <div class="form-group">
                        <label for="editHarga">Harga</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-gradient-primary text-white">Rp</span>
                            </div>
                            <input type="number" name="harga" class="form-control" id="editHarga" value="{{ $barang->harga }}" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-gradient-primary me-2">Update Data</button>
                        <a href="{{ route('barang.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection