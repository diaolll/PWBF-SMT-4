<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = DB::table('customer')->orderBy('id', 'desc')->get();
        return view('customer.index', compact('customers'));
    }

    public function tambah1()
    {
        $provinsi = DB::table('reg_provinces')->orderBy('name')->get();
        return view('customer.tambah1', compact('provinsi'));
    }

    public function store1(Request $request)
    {
        $request->validate([
            'nama'  => 'required',
            'foto'  => 'required',
        ]);

        $fotoData = base64_decode(
            preg_replace('/^data:image\/\w+;base64,/', '', $request->foto)
        );

        DB::table('customer')->insert([
            'nama'             => $request->nama,
            'alamat'           => $request->alamat,
            'provinsi'         => $request->provinsi_nama,
            'kota'             => $request->kota_nama,
            'kecamatan'        => $request->kecamatan_nama,
            'kodepos_kelurahan'=> $request->kelurahan_nama,
            'foto_blob'        => $fotoData,
        ]);

        return redirect()->route('customer.index')
                         ->with('success', 'Customer berhasil ditambahkan (BLOB)!');
    }

    public function tambah2()
    {
        $provinsi = DB::table('reg_provinces')->orderBy('name')->get();
        return view('customer.tambah2', compact('provinsi'));
    }

    public function store2(Request $request)
    {
        $request->validate([
            'nama'  => 'required',
            'foto'  => 'required',
        ]);

        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);
        $imageData = str_replace(' ', '+', $imageData);
        $fileName  = 'customer_' . time() . '.png';

        Storage::disk('public')->put('customers/' . $fileName, base64_decode($imageData));

        DB::table('customer')->insert([
            'nama'             => $request->nama,
            'alamat'           => $request->alamat,
            'provinsi'         => $request->provinsi_nama,
            'kota'             => $request->kota_nama,
            'kecamatan'        => $request->kecamatan_nama,
            'kodepos_kelurahan'=> $request->kelurahan_nama,
            'foto_path'        => 'customers/' . $fileName,
        ]);

        return redirect()->route('customer.index')
                         ->with('success', 'Customer berhasil ditambahkan (File)!');
    }
}