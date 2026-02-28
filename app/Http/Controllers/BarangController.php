<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman utama (Form Input + Datatables)
     */
    public function index()
    {
        $barangs = Barang::orderBy('timestamp', 'desc')->get();
        return view('barang.index', compact('barangs'));
    }

    /**
     * Menyimpan data barang baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:50',
            'harga' => 'required|numeric',
        ]);

        try {
            Barang::create([
                'nama' => $request->nama,
                'harga' => $request->harga,
                'timestamp' => now(),
            ]);

            // Notifikasi Sukses Simpan
            return redirect()->route('barang.index')->with('success', 'Barang baru berhasil disimpan ke database!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan barang: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman edit
     */
    public function edit($id)
    {
        $barang = Barang::where('id_barang', $id)->firstOrFail();
        return view('barang.edit', compact('barang'));
    }

    /**
     * Memperbarui data barang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:50',
            'harga' => 'required|numeric',
        ]);

        try {
            Barang::where('id_barang', $id)->update([
                'nama' => $request->nama,
                'harga' => $request->harga,
            ]);

            // Notifikasi Sukses Update
            return redirect()->route('barang.index')->with('success', 'Data barang ' . $id . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data!');
        }
    }

    /**
     * Menghapus data barang
     */
    public function destroy($id)
    {
        try {
            Barang::where('id_barang', $id)->delete();
            
            // Notifikasi Sukses Hapus
            return redirect()->route('barang.index')->with('success', 'Barang ' . $id . ' telah dihapus secara permanen.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang!');
        }
    }

    /**
     * Generate PDF Label TnJ 108
     */
    public function generatePDF(Request $request)
    {
        $ids = $request->input('ids', []);
        $startX = (int) $request->input('x', 1);
        $startY = (int) $request->input('y', 1);

        // Notifikasi Error jika belum pilih barang (Muncul di Index)
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu barang yang ingin dicetak!');
        }

        $selectedBarang = Barang::whereIn('id_barang', $ids)->get();
        $skipCount = (($startY - 1) * 5) + ($startX - 1);
        
        $pdf = Pdf::loadView('barang.pdf', compact('selectedBarang', 'skipCount'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Tag-Harga-TnJ108.pdf');
    }
}