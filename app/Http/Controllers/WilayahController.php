<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Menampilkan halaman dropdown wilayah versi jQuery AJAX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('wilayah.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Menampilkan halaman dropdown wilayah versi Axios
    |--------------------------------------------------------------------------
    */
    public function indexAxios()
    {
        return view('wilayah.index_axios');
    }

    /*
    |--------------------------------------------------------------------------
    | API: Mengambil semua Provinsi
    | GET /api/provinsi
    |--------------------------------------------------------------------------
    | Tabel : reg_provinces
    | Kolom : id (char 2), name
    |
    | select('id', 'name as nama') → alias 'name' jadi 'nama'
    | agar response JSON konsisten { "id": "11", "nama": "ACEH" }
    | dan kode JavaScript di view tidak perlu diubah sama sekali.
    */
    public function getProvinsi()
    {
        $data = DB::table('reg_provinces')
            ->select('id', 'name as nama')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data provinsi berhasil diambil',
            'data'    => $data,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API: Mengambil Kota/Kabupaten berdasarkan ID Provinsi
    | GET /api/kota/{id_provinsi}
    |--------------------------------------------------------------------------
    | Tabel : reg_regencies
    | Kolom : id (char 4), province_id (char 2), name
    */
    public function getKota($id_provinsi)
    {
        if (!$id_provinsi || $id_provinsi == 0) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'ID Provinsi tidak valid',
                'data'    => [],
            ], 400);
        }

        $data = DB::table('reg_regencies')
            ->select('id', 'name as nama')
            ->where('province_id', $id_provinsi)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kota berhasil diambil',
            'data'    => $data,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API: Mengambil Kecamatan berdasarkan ID Kota
    | GET /api/kecamatan/{id_kota}
    |--------------------------------------------------------------------------
    | Tabel : reg_districts
    | Kolom : id (char 6), regency_id (char 4), name
    */
    public function getKecamatan($id_kota)
    {
        if (!$id_kota || $id_kota == 0) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'ID Kota tidak valid',
                'data'    => [],
            ], 400);
        }

        $data = DB::table('reg_districts')
            ->select('id', 'name as nama')
            ->where('regency_id', $id_kota)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kecamatan berhasil diambil',
            'data'    => $data,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API: Mengambil Kelurahan berdasarkan ID Kecamatan
    | GET /api/kelurahan/{id_kecamatan}
    |--------------------------------------------------------------------------
    | Tabel : reg_villages
    | Kolom : id (char 10), district_id (char 6), name
    */
    public function getKelurahan($id_kecamatan)
    {
        if (!$id_kecamatan || $id_kecamatan == 0) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'ID Kecamatan tidak valid',
                'data'    => [],
            ], 400);
        }

        $data = DB::table('reg_villages')
            ->select('id', 'name as nama')
            ->where('district_id', $id_kecamatan)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kelurahan berhasil diambil',
            'data'    => $data,
        ]);
    }
}