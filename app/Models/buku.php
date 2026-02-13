<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'idbuku'; // TAMBAHKAN INI
    public $timestamps = false;

    protected $fillable = ['kode', 'judul', 'pengarang', 'idkategori'];

    public function kategori()
    {
        // Parameter: Model, foreign_key di tabel buku, owner_key di tabel kategori
        return $this->belongsTo(kategori::class, 'idkategori', 'idkategori');
    }
}
