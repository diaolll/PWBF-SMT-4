<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;

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
     * Halaman Barcode Scanner
     */
    public function scan()
    {
        return view('barang.scan');
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

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu barang yang ingin dicetak!');
        }

        $selectedBarang = Barang::whereIn('id_barang', $ids)->get();
        $skipCount = (($startY - 1) * 5) + ($startX - 1);

        // Generate barcode untuk setiap barang
        $generator = new BarcodeGeneratorPNG();
        $barcodes = [];
        foreach ($selectedBarang as $item) {
            $barcodes[$item->id_barang] = base64_encode(
                $generator->getBarcode($item->id_barang, $generator::TYPE_CODE_128)
            );
        }

        $pdf = Pdf::loadView('barang.pdf', compact('selectedBarang', 'skipCount', 'barcodes'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Tag-Harga-TnJ108.pdf');
    }

    /**
     * Cetak tag harga untuk barang tertentu
     */
    public function cetakTagHarga($id)
    {
        $barang = \DB::table('barang')->where('id_barang', $id)->first();

        // Generate barcode dari id_barang
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode(
            $generator->getBarcode($barang->id_barang, $generator::TYPE_CODE_128)
        );

        $pdf = Pdf::loadView('barang.tag-harga', [
            'barang'  => $barang,
            'barcode' => $barcode,
        ]);

        return $pdf->stream('tag-harga-' . $barang->id_barang . '.pdf');
    }
}