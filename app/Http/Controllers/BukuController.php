<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori; // Tambahkan ini untuk dropdown kategori

class BukuController extends Controller
{
    // Menampilkan daftar buku
    public function index()
    {
        $buku = Buku::with('kategori')->get();
        return view('Buku.index', compact('buku'));
    }

    // Menampilkan form tambah buku
    public function create()
    {
        $kategori = Kategori::all(); // Ambil semua kategori untuk pilihan di form
        return view('Buku.create', compact('kategori'));
    }

    // Menyimpan data buku baru (INSERT)
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:buku,kode',
            'judul' => 'required',
            'pengarang' => 'required',
            'idkategori' => 'required'
        ]);

        Buku::create($request->all());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Menampilkan form edit buku
    public function edit($id)
    {
        $buku = Buku::findOrFail($id); // Mencari berdasarkan idbuku
        $kategori = Kategori::all();
        return view('Buku.edit', compact('buku', 'kategori'));
    }

    // Memperbarui data buku (UPDATE)
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|unique:buku,kode,'.$id.',idbuku',
            'judul' => 'required',
            'pengarang' => 'required',
            'idkategori' => 'required'
        ]);

        $buku = Buku::findOrFail($id);
        $buku->update($request->all());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui!');
    }

    // Menghapus data buku (DELETE)
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus!');
    }
}