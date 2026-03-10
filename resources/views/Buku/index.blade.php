@extends('layouts.Template')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">

                {{-- Header --}}
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Koleksi Buku</h5>

                    <a href="{{ route('buku.create') }}" class="btn btn-light btn-sm fw-bold">
                        <i class="mdi mdi-plus"></i> Tambah Buku
                    </a>
                </div>

                <div class="card-body">

                    {{-- Alert sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover mt-2">
                            <thead class="table-dark">
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="40%">Judul Buku</th>
                                    <th width="20%">Pengarang</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                            @forelse ($buku as $item)

                                <tr>
                                    <td class="fw-bold text-primary">
                                        {{ $item->kode }}
                                    </td>

                                    <td>
                                        {{ $item->judul }}
                                    </td>

                                    <td>
                                        {{ $item->pengarang }}
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $item->kategori->nama_kategori ?? 'Umum' }}
                                        </span>
                                    </td>

                                    <td class="text-center">

                                        <div class="d-flex justify-content-center">

                                            {{-- Edit --}}
                                            <a href="{{ route('buku.edit', $item->idbuku) }}" 
                                               class="btn btn-sm btn-warning me-2">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form class="delete-form"
                                                  action="{{ route('buku.destroy', $item->idbuku) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Data buku tidak ditemukan dalam database.
                                    </td>
                                </tr>

                            @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- SCRIPT UNTUK LOADER BUTTON --}}
@push('scripts')

<script>

document.addEventListener("DOMContentLoaded", function(){

    const forms = document.querySelectorAll(".delete-form");

    forms.forEach(function(form){

        form.addEventListener("submit", function(){

            let button = form.querySelector(".delete-btn");

            button.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Loading...
            `;

            button.disabled = true;

        });

    });

});

</script>

@endpush