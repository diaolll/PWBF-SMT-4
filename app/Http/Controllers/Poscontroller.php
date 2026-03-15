<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;

class PosController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Menampilkan halaman POS versi jQuery AJAX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('pos.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Menampilkan halaman POS versi Axios
    |--------------------------------------------------------------------------
    */
    public function indexAxios()
    {
        return view('pos.index_axios');
    }

    /*
    |--------------------------------------------------------------------------
    | API: Mencari barang berdasarkan kode
    | GET /api/barang/{kode}
    |--------------------------------------------------------------------------
    | Menggunakan Eloquent Model Barang.
    |
    | find($kode) → mencari berdasarkan primary key (id_barang).
    | Karena Model sudah mendefinisikan:
    |   protected $primaryKey = 'id_barang';
    |   protected $keyType    = 'string';
    | maka find() langsung mencari di kolom id_barang.
    |
    | Response sukses (HTTP 200):
    | { "status":"success", "code":200, "data": { id_barang, nama, harga } }
    |
    | Response tidak ditemukan (HTTP 404):
    | { "status":"error", "code":404, "message":"Barang tidak ditemukan" }
    */
    public function getBarang($kode)
    {
        // find() menggunakan $primaryKey dari Model → id_barang
        $barang = Barang::select('id_barang', 'nama', 'harga')
            ->find($kode);

        if ($barang) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Barang ditemukan',
                'data'    => $barang,
            ]);
        }

        // HTTP 404 agar $.ajax masuk ke callback error / Axios masuk ke .catch()
        return response()->json([
            'status'  => 'error',
            'code'    => 404,
            'message' => 'Barang tidak ditemukan',
            'data'    => null,
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | API: Menyimpan transaksi pembayaran ke database
    | POST /api/bayar
    |--------------------------------------------------------------------------
    | Alur penyimpanan:
    | 1. Validasi input dari request
    | 2. Hitung total dari semua subtotal item
    | 3. Buat record Penjualan (header transaksi) → Eloquent create()
    | 4. Loop items → buat PenjualanDetail untuk setiap item
    | 5. Gunakan DB Transaction agar data tetap konsisten
    |
    | Eloquent create() memanfaatkan $fillable yang sudah didefinisikan
    | di masing-masing Model sehingga lebih aman dari mass-assignment.
    */
    public function bayar(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.id_barang' => 'required|string',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.subtotal'  => 'required|integer|min:0',
        ]);

        $items = $request->items;
        $total = collect($items)->sum('subtotal');

        DB::beginTransaction();
        try {
            // --- Langkah 1: Simpan header penjualan menggunakan Eloquent ---
            // Penjualan::create() menggunakan $fillable: ['timestamp', 'total']
            $penjualan = Penjualan::create([
                'timestamp' => now(),
                'total'     => $total,
            ]);

            // --- Langkah 2: Simpan setiap item sebagai PenjualanDetail ---
            foreach ($items as $item) {
                // PenjualanDetail::create() menggunakan $fillable:
                // ['id_penjualan', 'id_barang', 'jumlah', 'subtotal']
                PenjualanDetail::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_barang'    => $item['id_barang'],
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Transaksi berhasil disimpan',
                'data'    => [
                    'id_penjualan' => $penjualan->id_penjualan,
                    'total'        => $total,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}