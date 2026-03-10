@extends('layouts.Template')

@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Form Tambah Buku</h5>
            </div>

            <div class="card-body">

                <form id="formBuku" action="{{ route('buku.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="fw-bold">Kode Buku</label>

                        <input type="text"
                               name="kode"
                               required
                               class="form-control @error('kode') is-invalid @enderror"
                               value="{{ old('kode') }}"
                               placeholder="Contoh: BK001">

                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group mb-3">
                        <label class="fw-bold">Judul Buku</label>

                        <input type="text"
                               name="judul"
                               required
                               class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul') }}"
                               placeholder="Masukkan judul buku">

                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group mb-3">
                        <label class="fw-bold">Pengarang</label>

                        <input type="text"
                               name="pengarang"
                               required
                               class="form-control @error('pengarang') is-invalid @enderror"
                               value="{{ old('pengarang') }}"
                               placeholder="Nama penulis">

                        @error('pengarang')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group mb-4">
                        <label class="fw-bold">Kategori</label>

                        <select name="idkategori"
                                required
                                class="form-select form-control @error('idkategori') is-invalid @enderror">

                            <option value="">-- Pilih Kategori --</option>

                            @foreach($kategori as $k)

                            <option value="{{ $k->idkategori }}"
                                {{ old('idkategori') == $k->idkategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>

                            @endforeach

                        </select>

                        @error('idkategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="d-flex justify-content-between">

                        <a href="{{ route('buku.index') }}" class="btn btn-light">
                            Kembali
                        </a>

                        <button type="button"
                                id="btnSubmit"
                                class="btn btn-primary">

                            Simpan Data

                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

</div>

<script>

document.getElementById("btnSubmit").addEventListener("click", function(){

    let form = document.getElementById("formBuku");

    if(!form.checkValidity()){
        form.reportValidity();
        return;
    }

    let button = this;

    button.innerHTML =
    '<span class="spinner-border spinner-border-sm"></span> Loading...';

    button.disabled = true;

    // kasih delay supaya spinner terlihat
    setTimeout(function(){
        form.submit();
    }, 500);

});

</script>

@endsection
