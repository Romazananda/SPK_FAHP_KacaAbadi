<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilRanking extends Model
{
    use HasFactory;

    protected $table = 'hasil_rankings';

    protected $fillable = [
        'id_alternatif',
        'skor_total',
        'ranking',
    ];

    // Relasi ke Alternatif
    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class, 'id_alternatif', 'id');
    }
}
