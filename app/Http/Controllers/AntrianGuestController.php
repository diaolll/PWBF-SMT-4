<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianGuestController extends Controller
{
    public function index()
    {
        return view('guest.index');
    }

    public function daftar(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $lastNomor = Antrian::max('nomor') ?? 0;
        $nomor = $lastNomor + 1;

        $antrian = Antrian::create([
            'nomor' => $nomor,
            'nama' => $request->nama,
            'status' => 'menunggu',
        ]);

        $this->updateCache();

        return redirect()->route('guest.redirect', [
            'nomor' => $antrian->nomor,
            'nama' => $antrian->nama,
        ]);
    }

    public function redirectView($nomor, $nama)
    {
        return view('guest.redirect', compact('nomor', 'nama'));
    }

    public function tiket($nomor, $nama)
    {
        $antrian = Antrian::where('nomor', $nomor)->where('nama', $nama)->firstOrFail();

        return view('guest.tiket', compact('antrian'));
    }

    private function updateCache()
    {
        $menunggu = Antrian::where('status', 'menunggu')->orderBy('nomor')->get()->toArray();
        $dipanggil = Antrian::where('status', 'dipanggil')->first();
        $terlambat = Antrian::where('status', 'terlambat')->orderBy('nomor')->get()->toArray();
        $selesai = Antrian::where('status', 'selesai')->orderBy('updated_at', 'desc')->take(10)->get()->toArray();

        Cache::put('antrian_data', [
            'menunggu' => $menunggu,
            'dipanggil' => $dipanggil ? $dipanggil->toArray() : null,
            'terlambat' => $terlambat,
            'selesai' => $selesai,
        ], now()->addHours(24));
    }
}
