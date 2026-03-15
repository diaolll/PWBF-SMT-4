@extends('layouts.Template')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-body">

                <h4 class="card-title">Input Data Barang [Poin 2]</h4>

                <form id="formBarang">
                    <div class="row">

                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Nama barang :</label>
                                <input type="text" id="namaBarang" class="form-control" placeholder="Input Nama" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Harga barang:</label>
                                <input type="number" id="hargaBarang" class="form-control" placeholder="Input Harga" required>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" id="btnSubmitBarang" class="btn btn-gradient-primary w-100">
                                <span id="txtSubmit">Submit</span>
                                <div id="loaderSubmit" class="spinner-border spinner-border-sm d-none"></div>
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
var $j = jQuery.noConflict();

$j(document).ready(function(){

    $j('#btnSubmitBarang').click(function(){

        var form = document.getElementById('formBarang');

        if(!form.checkValidity()){
            form.reportValidity();
            return;
        }

        $j('#txtSubmit').addClass('d-none');
        $j('#loaderSubmit').removeClass('d-none');

        setTimeout(function(){

            let nama = $j('#namaBarang').val();
            let harga = $j('#hargaBarang').val();

            // simpan ke localStorage
            let data = JSON.parse(localStorage.getItem('barang')) || [];

            data.push({
                nama:nama,
                harga:harga
            });

            localStorage.setItem('barang', JSON.stringify(data));

            $j('#namaBarang').val('');
            $j('#hargaBarang').val('');

            $j('#txtSubmit').removeClass('d-none');
            $j('#loaderSubmit').addClass('d-none');

            alert("Data berhasil ditambahkan");

        },600);

    });

});
</script>

@endsection