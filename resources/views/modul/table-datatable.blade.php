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
                            <div class="form-group w-100">
                                <button type="button" id="btnSubmitBarang" class="btn btn-gradient-primary w-100" style="height: 45px;">
                                    <span id="txtSubmit">Submit</span>
                                    <div id="loaderSubmit" class="spinner-border spinner-border-sm d-none"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang [Poin 3]</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tabelBarang">
                        <thead>
                            <tr class="text-center">
                                <th>ID barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit / Hapus Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <div class="form-group">
                        <label>ID barang :</label>
                        <input type="text" id="editId" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama barang :</label>
                        <input type="text" id="editNama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga barang:</label>
                        <input type="number" id="editHarga" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnHapus">Hapus</button>
                <button type="button" class="btn btn-primary" id="btnUbah">Ubah</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mouse jadi pointer saat hover di row */
    #tabelBarang tbody tr { cursor: pointer; }
    #tabelBarang tbody tr:hover { background-color: rgba(182, 109, 255, 0.1); }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var $j = jQuery.noConflict();
    var idCounter = 1;
    var currentRow; 

    $j(document).ready(function() {
        // PROSES SUBMIT (Poin 53-56)
        $j('#btnSubmitBarang').on('click', function() {
            var form = document.getElementById('formBarang');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Animasi Loader
            $j('#txtSubmit').addClass('d-none');
            $j('#loaderSubmit').removeClass('d-none');
            $j(this).prop('disabled', true);

            setTimeout(function() {
                let nama = $j('#namaBarang').val();
                let harga = $j('#hargaBarang').val();

                // Tambah ke row baru
                let row = `
                    <tr data-harga="${harga}">
                        <td class="text-center">${idCounter}</td>
                        <td>${nama}</td>
                        <td class="text-right">Rp ${parseInt(harga).toLocaleString('id-ID')}</td>
                    </tr>
                `;
                $j('#tabelBarang tbody').append(row);

                // Kosongkan input
                $j('#namaBarang, #hargaBarang').val('');
                idCounter++;

                $j('#txtSubmit').removeClass('d-none');
                $j('#loaderSubmit').addClass('d-none');
                $j('#btnSubmitBarang').prop('disabled', false);
            }, 600);
        });

        // KLIK ROW UNTUK MODAL (Poin 75)
        $j(document).on('click', '#tabelBarang tbody tr', function() {
            currentRow = $j(this);
            let id = currentRow.find('td:eq(0)').text();
            let nama = currentRow.find('td:eq(1)').text();
            let harga = currentRow.attr('data-harga');

            $j('#editId').val(id);
            $j('#editNama').val(nama);
            $j('#editHarga').val(harga);
            
            // Trigger modal manual pakai JQuery agar lebih aman
            $j('#modalEdit').modal('show'); 
        });

        // PROSES UBAH (Poin 94)
        $j('#btnUbah').on('click', function() {
            let namaBaru = $j('#editNama').val();
            let hargaBaru = $j('#editHarga').val();

            if(namaBaru && hargaBaru) {
                currentRow.find('td:eq(1)').text(namaBaru);
                currentRow.find('td:eq(2)').text("Rp " + parseInt(hargaBaru).toLocaleString('id-ID'));
                currentRow.attr('data-harga', hargaBaru);
                $j('#modalEdit').modal('hide');
            }
        });

        // PROSES HAPUS (Poin 93)
        $j('#btnHapus').on('click', function() {
            currentRow.remove();
            $j('#modalEdit').modal('hide');
        });
    });
</script>
@endsection