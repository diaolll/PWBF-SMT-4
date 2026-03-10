<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModulController extends Controller
{

    // modul point 2 (datatable)
    public function tableDatatable()
    {
        return view('modul.table-datatable');
    }

    // modul point 4 (select kota)
    public function selectKota()
    {
        return view('modul.select-kota');
    }

}