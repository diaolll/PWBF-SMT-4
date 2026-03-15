<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table      = 'barang';
    protected $primaryKey = 'id_barang';
    public $incrementing  = false;   // PK bukan auto-increment (varchar)
    protected $keyType    = 'string';
    public $timestamps    = false;   // Tidak pakai created_at / updated_at bawaan Laravel

    protected $fillable = [
        'id_barang',
        'nama',
        'harga',
        'timestamp',
    ];

    // -------------------------------------------------------------------------
    // Accessor: Format harga ke Rupiah saat dipanggil $barang->harga_formatted
    // -------------------------------------------------------------------------
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // -------------------------------------------------------------------------
    // Relasi: Satu barang bisa muncul di banyak penjualan_detail
    // -------------------------------------------------------------------------
    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_barang', 'id_barang');
    }
}