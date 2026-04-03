@extends('layouts.Template')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-store"></i>
        </span> Manajemen Vendor
    </h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">
                <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
            </li>
        </ul>
    </nav>
</div>

<div class="row">
    {{-- Form Tambah --}}
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Registrasi Vendor</h4>
                <p class="card-description"> Tambahkan unit kantin baru </p>
                <form class="forms-sample" action="{{ route('admin.vendor.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_vendor">Nama Vendor/Kantin</label>
                        <input type="text" name="nama_vendor" class="form-control shadow-none border-primary" id="nama_vendor" placeholder="Masukkan nama..." required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 w-100">
                        <i class="mdi mdi-content-save me-2"></i>Simpan Vendor
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Vendor Terdaftar</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 100px;"> ID </th>
                                <th> Nama Vendor </th>
                                <th class="text-center"> Status </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $v)
                            <tr>
                                <td> <span class="badge badge-secondary">#{{ $v->idvendor }}</span> </td>
                                <td class="py-1 font-weight-bold text-dark"> 
                                    <i class="mdi mdi-store-24-hour me-2 text-info"></i>{{ $v->nama_vendor }} 
                                </td>
                                <td class="text-center">
                                    <label class="badge badge-success text-white">Aktif</label>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection