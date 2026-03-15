<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table      = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public $incrementing  = true;      // PK auto-increment (int)
    protected $keyType    = 'int';
    public $timestamps    = false;     // Kolom timestamp dikelola manual

    protected $fillable = [
        'timestamp',
        'total',
    ];

    // -------------------------------------------------------------------------
    // Relasi: Satu penjualan punya banyak detail item
    // -------------------------------------------------------------------------
    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }
}