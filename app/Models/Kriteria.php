<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriterias';
    protected $primaryKey = 'id_kriteria';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['nama_kriteria', 'tipe'];

    public function getIdAttribute()
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    // Relasi ke tabel bobot fuzzy
    public function bobotFuzzy()
    {
        return $this->hasOne(KriteriaBobot::class, 'kriteria_id', 'id_kriteria');
    }

    // Relasi ke subkriteria
    public function subkriteria()
    {
        return $this->hasMany(Subkriteria::class, 'id_kriteria', 'id_kriteria');
    }

      // 🔹 (Tambahan baru) relasi untuk hanya subkriteria yang sudah disetujui
    public function subkriteriaApproved()
    {
        return $this->hasMany(Subkriteria::class, 'id_kriteria', 'id_kriteria')
                    ->where('status', 'approved');
    }

    // 🔹 (Opsional) relasi untuk subkriteria pending
    public function subkriteriaPending()
    {
        return $this->hasMany(Subkriteria::class, 'id_kriteria', 'id_kriteria')
                    ->where('status', 'pending');
    }
}
