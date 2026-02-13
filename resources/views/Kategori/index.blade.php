@extends('layouts.Template')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Kategori Buku</h5>
                    <a href="{{ route('kategori.create') }}" class="btn btn-light btn-sm fw-bold">
                        <i class="mdi mdi-plus"></i> Tambah Kategori
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover mt-2">
                            <thead class="table-dark">
                                <tr>
                                    <th width="15%">ID</th>
                                    <th width="65%">Nama Kategori</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kategori as $item)
                                    <tr>
                                        <td class="fw-bold">{{ $item->idkategori }}</td>
                                        <td>{{ $item->nama_kategori }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('kategori.edit', $item->idkategori) }}" class="btn btn-sm btn-warning me-2">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('kategori.destroy', $item->idkategori) }}" method="POST" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada kategori.</td>
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