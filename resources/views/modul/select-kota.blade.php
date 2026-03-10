@extends('layouts.Template')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* 1. PERBAIKAN FORM INPUT & BUTTON (Agar Sejajar & Presisi) */
    .custom-input-group {
        display: flex;
        align-items: stretch; /* Memaksa tinggi input dan button sama */
        width: 100%;
    }
    
    .custom-input-group .form-control {
        height: 45px !important; /* Kunci tinggi input */
        border-radius: 4px 0 0 4px !important; /* Rounded hanya di kiri */
        border: 1px solid #ebedf2;
    }

    .custom-input-group .btn {
        height: 45px !important; /* Kunci tinggi button sama dengan input */
        margin: 0 !important;
        border-radius: 0 4px 4px 0 !important; /* Rounded hanya di kanan */
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
    }

    /* 2. PENYELARASAN CARD */
    .stretch-card > .card {
        min-height: 250px;
    }

    /* 3. PERBAIKAN SELECT2 (Agar tidak melenceng) */
    .select2-container--default .select2-selection--single {
        height: 45px !important;
        border: 1px solid #ebedf2 !important;
        display: flex;
        align-items: center;
    }
    .select2-container {
        width: 100% !important;
    }

    .display-hasil {
        font-weight: bold;
        color: #b66dff;
        word-break: break-all;
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">Input Master Kota</h4>
                <div class="custom-input-group">
                    <input type="text" id="inputKota" class="form-control" placeholder="Masukkan nama kota...">
                    <button class="btn btn-gradient-primary" type="button" id="btnTambah">
                        <span id="btnText">Tambahkan</span>
                        <div id="btnLoader" class="spinner-border spinner-border-sm d-none"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-white pt-3"><b>Select</b></div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="form-group">
                    <label>Pilih Kota:</label>
                    <select id="selectBiasa" class="form-control" style="height: 45px;">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>
                <p class="mb-0">Kota Terpilih: <span id="hasilBiasa" class="display-hasil">-</span></p>
            </div>
        </div>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header bg-white pt-3"><b>select 2</b></div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="form-group">
                    <label>Pilih Kota (Select2):</label>
                    <select id="selectDua" class="form-control select2-custom">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>
                <p class="mb-0">Kota Terpilih: <span id="hasilDua" class="display-hasil">-</span></p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    var $j = jQuery.noConflict();

    $j(document).ready(function() {
        $j('.select2-custom').select2({
            placeholder: "-- Cari Kota --"
        });

        $j('#btnTambah').on('click', function() {
            var nama = $j('#inputKota').val().trim();
            if (nama !== "") {
                $j('#btnText').addClass('d-none');
                $j('#btnLoader').removeClass('d-none');
                $j('#btnTambah').prop('disabled', true);

                setTimeout(function() {
                    $j('#selectBiasa').append(new Option(nama, nama));
                    $j('#selectDua').append(new Option(nama, nama)).trigger('change');
                    $j('#inputKota').val('').focus();
                    $j('#btnText').removeClass('d-none');
                    $j('#btnLoader').addClass('d-none');
                    $j('#btnTambah').prop('disabled', false);
                }, 500);
            }
        });

        $j('#selectBiasa').on('change', function() {
            $j('#hasilBiasa').text($j(this).val() || "-");
        });

        $j('#selectDua').on('change', function() {
            $j('#hasilDua').text($j(this).val() || "-");
        });
    });
</script>
@endsection