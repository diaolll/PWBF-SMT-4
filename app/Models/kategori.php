<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'idkategori'; // TAMBAHKAN INI
    public $timestamps = false;

    protected $fillable = ['nama_kategori'];
}
