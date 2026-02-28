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
        // Mengambil semua data barang, urutkan dari yang terbaru
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

        // Kita cukup insert nama dan harga. 
        // id_barang akan diisi otomatis oleh TRIGGER di database.
        Barang::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'timestamp' => now(),
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
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

        Barang::where('id_barang', $id)->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Menghapus data barang
     */
    public function destroy($id)
    {
        Barang::where('id_barang', $id)->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    /**
     * Fungsi Utama: Generate PDF Label TnJ 108
     */
    public function generatePDF(Request $request)
    {
        // 1. Ambil data dari form
        $ids = $request->input('ids', []); // Array ID yang dicentang
        $startX = (int) $request->input('x', 1); // Kolom awal (1-5)
        $startY = (int) $request->input('y', 1); // Baris awal (1-8)

        // 2. Validasi jika tidak ada barang yang dipilih
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Silahkan pilih minimal satu barang untuk dicetak!');
        }

        // 3. Ambil data barang berdasarkan ID yang dipilih
        $selectedBarang = Barang::whereIn('id_barang', $ids)->get();
        
        // 4. Hitung Skip Count (Kotak Kosong)
        // Rumus: ((Baris - 1) * Jumlah_Kolom) + (Kolom - 1)
        // TnJ 108 memiliki 5 kolom
        $skipCount = (($startY - 1) * 5) + ($startX - 1); //
        // 5. Generate PDF menggunakan view 'barang.pdf'
        $pdf = Pdf::loadView('barang.pdf', compact('selectedBarang', 'skipCount'))
                  ->setPaper('a4', 'portrait');

        // 6. Stream file ke browser
        return $pdf->stream('Tag-Harga-TnJ108.pdf');
    }
}