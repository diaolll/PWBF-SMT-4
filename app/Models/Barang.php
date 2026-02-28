<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    // Nama tabel di database 
    protected $table = 'barang';

    // Primary key yang kamu tentukan 
    protected $primaryKey = 'id_barang';

    // Karena id_barang adalah VARCHAR (bukan integer), set incrementing ke false
    public $incrementing = false;

    // Tipe data primary key adalah string 
    protected $keyType = 'string';

    // Laravel secara default mencari created_at dan updated_at. 
    // Karena kamu hanya pakai 'timestamp', kita matikan timestamps default Laravel. 
    public $timestamps = false;

    // Kolom yang boleh diisi (mass assignable) 
    protected $fillable = [
        'id_barang',
        'nama',
        'harga',
        'timestamp'
    ];

    /**
     * Opsional: Jika ingin format harga otomatis rapi saat dipanggil di view
     */
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}