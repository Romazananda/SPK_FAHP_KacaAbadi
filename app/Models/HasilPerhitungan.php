<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPerhitungan extends Model
{
    use HasFactory;

    protected $table = 'hasil_perhitungan';

    // Tambahkan SEMUA kolom yang diinput ke sini
    protected $fillable = [
        'alternatif_id',
        'preferensi_id',
        'nilai_total',
        'ranking',
    ];

    // Relasi ke tabel Alternatif
    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class, 'alternatif_id');
    }

    // Relasi ke tabel Preferensi
    public function preferensi()
    {
        return $this->belongsTo(PreferensiKriteria::class, 'preferensi_id');
    }
}
