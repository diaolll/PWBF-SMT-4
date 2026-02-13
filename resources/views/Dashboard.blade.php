@extends('layouts.Template')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard Admin Paling Rajin
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Status Terkini <i class="mdi mdi-coffee icon-sm text-primary align-middle"></i>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Buku Tersedia <i class="mdi mdi-book-open-page-variant mdi-24px float-right"></i></h4>
                    <h2 class="mb-5">1.234 Kg</h2>
                    <h6 class="card-text">Banyak yang dipinjam, sedikit yang balik.</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Pengunjung Hari Ini <i class="mdi mdi-account-group mdi-24px float-right"></i></h4>
                    <h2 class="mb-5">999.999 Orang</h2>
                    <h6 class="card-text">Termasuk semut dan kecoa di perpus.</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Level Kopi Admin <i class="mdi mdi-coffee-outline mdi-24px float-right"></i></h4>
                    <h2 class="mb-5">Low Battery</h2>
                    <h6 class="card-text">Butuh asupan kafein biar nggak error.</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Quote Hari Ini</h4>
                    <blockquote class="blockquote blockquote-primary text-center">
                        <p>"bismillah tiba tiba bisa ngodong"</p>
                        <footer class="blockquote-footer">Developer kw </cite></footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
@endsection