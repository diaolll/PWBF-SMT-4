<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianAdminController extends Controller
{
    public function index()
    {
        // Data will be loaded via SSE, no need to query DB here
        return view('admin.antrian');
    }

    public function tambah(Request $request)
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

        return redirect()->back()->with('success', "Antrian #$nomor untuk {$request->nama} berhasil ditambahkan.");
    }

    public function panggil()
    {
        // Tandai yang sedang dipanggil jadi selesai (jika ada)
        $sedangDipanggil = Antrian::where('status', 'dipanggil')->first();
        if ($sedangDipanggil) {
            $sedangDipanggil->update(['status' => 'selesai']);
        }

        // Ambil antrian menunggu berikutnya
        $menunggu = Antrian::where('status', 'menunggu')->orderBy('nomor')->first();

        if (!$menunggu) {
            $this->updateCache();
            return redirect()->back()->with('error', 'Tidak ada antrian menunggu.');
        }

        $menunggu->update(['status' => 'dipanggil']);
        $this->updateCache();

        return redirect()->back()->with('success', "Memanggil antrian #{$menunggu->nomor} - {$menunggu->nama}");
    }

    public function tandaiTerlambat($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'terlambat']);
        $this->updateCache();

        return redirect()->back()->with('success', "Antrian #{$antrian->nomor} ditandai terlambat.");
    }

    public function panggilTerlambat($id)
    {
        $antrian = Antrian::findOrFail($id);

        $sedangDipanggil = Antrian::where('status', 'dipanggil')->first();
        if ($sedangDipanggil) {
            $sedangDipanggil->update(['status' => 'selesai']);
        }

        $antrian->update(['status' => 'dipanggil']);
        $this->updateCache();

        return redirect()->back()->with('success', "Memanggil antrian terlambat #{$antrian->nomor} - {$antrian->nama}");
    }

    public function reset()
    {
        Antrian::truncate();
        Cache::forget('antrian_data');

        return redirect()->back()->with('success', 'Semua data antrian berhasil direset.');
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
