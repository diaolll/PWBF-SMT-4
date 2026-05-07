<?php
namespace App\Http\Controllers;

use App\Models\LokasiToko;
use Illuminate\Http\Request;

class KunjunganTokoController extends Controller
{
    // Halaman utama "Kunjungan Toko"
    public function index()
    {
        $tokos = LokasiToko::all();
        return view('kunjungan-toko.index', compact('tokos'));
    }

    // Simpan toko baru (Input Titik Awal)
    public function storeToko(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:50',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
        ]);

        // Generate barcode 8 karakter unik
        do {
            $barcode = strtoupper(substr(uniqid(), -8));
        } while (LokasiToko::where('barcode', $barcode)->exists());

        LokasiToko::create([
            'barcode'   => $barcode,
            'nama_toko' => $request->nama_toko,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
        ]);

        return redirect()->route('kunjungan-toko.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    // Hapus toko
    public function destroyToko($barcode)
    {
        LokasiToko::findOrFail($barcode)->delete();
        return redirect()->route('kunjungan-toko.index')->with('success', 'Toko berhasil dihapus.');
    }

    // AJAX: ambil data toko by barcode (untuk titik kunjungan)
    public function getByBarcode($barcode)
    {
        $toko = LokasiToko::find($barcode);
        if (!$toko) return response()->json(['error' => 'Toko tidak ditemukan'], 404);
        return response()->json($toko);
    }

    // AJAX: hitung jarak & return hasil (tidak simpan ke DB sesuai modul)
    public function cekKunjungan(Request $request)
    {
        $request->validate([
            'barcode'        => 'required|exists:lokasi_toko,barcode',
            'latitude_sales' => 'required|numeric',
            'longitude_sales'=> 'required|numeric',
            'accuracy_sales' => 'required|numeric',
        ]);

        $toko = LokasiToko::find($request->barcode);

        $jarak            = $this->haversine(
                                $toko->latitude, $toko->longitude,
                                $request->latitude_sales, $request->longitude_sales
                            );
        $threshold        = 300; // meter, bisa diubah
        $thresholdEfektif = $threshold + $toko->accuracy + $request->accuracy_sales;
        $status           = $jarak <= $thresholdEfektif ? 'diterima' : 'ditolak';

        return response()->json([
            'nama_toko'         => $toko->nama_toko,
            'latitude_sales'    => $request->latitude_sales,
            'longitude_sales'   => $request->longitude_sales,
            'accuracy_sales'    => $request->accuracy_sales,
            'jarak_aktual'      => round($jarak, 2),
            'threshold_efektif' => round($thresholdEfektif, 2),
            'status'            => $status,
        ]);
    }

    // Formula Haversine (Lampiran 2)
    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $R    = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}