<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    // 1. Tampilkan Daftar Kategori
    public function index()
    {
        $kategori = Kategori::all();
        return view('Kategori.index', compact('kategori'));
    }

    // 2. Tampilkan Form Tambah
    public function create()
    {
        return view('Kategori.create');
    }

    // 3. Simpan Kategori Baru (INSERT)
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori'
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    // 4. Tampilkan Form Edit
    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id); // Mencari berdasarkan idkategori
        return view('Kategori.edit', compact('kategori'));
    }

    // 5. Update Data Kategori
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori,'.$id.',idkategori'
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    // 6. Hapus Kategori (DELETE)
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        
        // Cek apakah kategori ini masih dipakai di tabel buku (Opsional tapi disarankan)
        try {
            $kategori->delete();
            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')->with('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh data buku.');
        }
    }
}