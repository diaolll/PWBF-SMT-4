<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage; 

class AdminController extends Controller
{
    /**
     * TAMPILAN HALAMAN
     */

    public function vendor()
    {
        // Ambil vendor beserta menu-menunya
        $vendors = Vendor::with('menus')->get();
        return view('vendor.index', compact('vendors'));
    }

    public function menu()
    {
        // Pastikan relasi 'vendor' ada di Model Menu agar tidak error
        $menus = Menu::with('vendor')->get();
        $vendors = Vendor::all();

        return view('menu.index', compact('menus', 'vendors'));
    }

    public function pesanan()
    {
        // Hapus filter where status_bayar 1 agar pesanan yang masih '0' (Pending) tetap muncul
        $pesanan = Pesanan::with(['details.menu'])
                    ->latest('idpesanan')
                    ->get();

        return view('pesanan.index', compact('pesanan'));
    }

    /**
     * Halaman QR Code Scanner untuk Vendor
     */
    public function scan()
    {
        return view('vendor.scan');
    }

    /**
     * PROSES SIMPAN DATA
     */

    public function storeVendor(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
        ]);

        Vendor::create([
            'nama_vendor' => $request->nama_vendor
        ]);

        return redirect()->back()->with('success', 'Vendor berhasil ditambahkan!');
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'idvendor'  => 'required|exists:vendor,idvendor',
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $path_gambar = null;

        if ($request->hasFile('gambar')) {
            /** * 🔹 TIPS MACBOOK: 
             * store('menu-images', 'public') akan menyimpan ke storage/app/public/menu-images
             * pastikan kamu sudah jalankan php artisan storage:link
             */
            $path_gambar = $request->file('gambar')->store('menu-images', 'public');
        }

        Menu::create([
            'idvendor'    => $request->idvendor,
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $path_gambar 
        ]);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }
}