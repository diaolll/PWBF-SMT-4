@extends('layouts.Template')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Buku: {{ $buku->judul }}</h5>
                </div>
                <div class="card-body">
                    {{-- Perhatikan method PUT untuk Update --}}
                    <form action="{{ route('buku.update', $buku->idbuku) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label class="fw-bold">Kode Buku</label>
                            <input type="text" name="kode" class="form-control" value="{{ $buku->kode }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-bold">Judul Buku</label>
                            <input type="text" name="judul" class="form-control" value="{{ $buku->judul }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-bold">Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" value="{{ $buku->pengarang }}">
                        </div>

                        <div class="form-group mb-4">
                            <label class="fw-bold">Kategori</label>
                            <select name="idkategori" class="form-select form-control">
                                @foreach($kategori as $k)
                                    <option value="{{ $k->idkategori }}" {{ $buku->idkategori == $k->idkategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('buku.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-warning">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection