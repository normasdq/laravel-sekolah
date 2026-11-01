<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'start_time',
        'end_time',
        'answers',
        'question_ids',
    ];

    /**
     * Casting atribut.
     * Otomatis mengubah JSON string menjadi Array PHP saat diakses.
     */
    protected $casts = [
        'answers' => 'array',
        'question_ids' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relationship: Hasil ujian ini milik satu User/Pendaftar.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Data Pendaftar SPMB calon siswa.
     */
    public function registration()
    {
        return $this->hasOne(Registration::class, 'user_id', 'user_id');
    }

    /**
     * Accessor: Menghitung sisa waktu pengerjaan secara real-time dari sisi server.
     */
    public function getRemainingSecondsAttribute()
    {
        if (! $this->start_time || $this->end_time) {
            return 0;
        }

        $durationInMinutes = 60; // Durasi standar ujian CBT 60 menit
        $expiryTime = $this->start_time->addMinutes($durationInMinutes);
        $diff = Carbon::now()->diffInSeconds($expiryTime, false);

        return $diff > 0 ? $diff : 0;
    }

    /**
     * Accessor: Format nilai agar lebih rapi (misal: 85.00).
     */
    public function getFormattedScoreAttribute()
    {
        return number_format($this->score, 2);
    }
}
