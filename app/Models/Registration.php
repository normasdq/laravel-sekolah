<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Registration extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::forceDeleting(function (Registration $registration) {
            foreach (['kartu_keluarga', 'ijazah', 'akte_kelahiran', 'pas_foto'] as $doc) {
                if ($registration->{$doc}) {
                    Storage::disk('local')->delete($registration->{$doc});
                }
            }
        });
    }

    protected $fillable = [
        'user_id',             // Relasi ke akun login
        'registration_number', // Nomor unik pendaftar
        'name',
        'nisn',
        'school_origin',
        'phone',
        'email',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'parent_name',
        'parent_phone',
        'status',
        'notes',
        'pas_foto',
        'kartu_keluarga',
        'ijazah',
        'akte_kelahiran',
        'documents_verified',
        'payment_amount',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'status' => 'string',
        'documents_verified' => 'boolean',
        'payment_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // =============================
    // RELATIONSHIPS
    // =============================

    /**
     * Menghubungkan data pendaftaran dengan akun User pendaftar.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function sawScores()
    {
        return $this->hasMany(SawScore::class);
    }

    public function examResult()
    {
        return $this->hasOne(ExamResult::class, 'user_id', 'user_id')->latestOfMany();
    }

    // =============================
    // LOCAL SCOPES
    // =============================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeLulus($query)
    {
        return $query->where('status', 'lulus');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // =============================
    // ACCESSORS (Data Helper)
    // =============================

    /**
     * Label status yang disinkronkan dengan Dashboard Pendaftar
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'Terverifikasi Berkas',
            'lulus' => 'Dinyatakan Lulus',
            'tidak_lulus' => 'Tidak Lulus',
            'rejected' => 'Berkas Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }

    /**
     * Warna badge Tailwind untuk status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'blue',
            'lulus' => 'emerald',
            'tidak_lulus' => 'red',
            'rejected' => 'red',
            default => 'amber',
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function getDocumentsCompleteAttribute(): bool
    {
        return ! empty($this->kartu_keluarga)
            && ! empty($this->ijazah)
            && ! empty($this->akte_kelahiran);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'pending' => 'Menunggu Pembayaran',
            'expired' => 'Kedaluwarsa',
            default => 'Belum Bayar',
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'emerald',
            'pending' => 'amber',
            'expired' => 'red',
            default => 'gray',
        };
    }

    public function getPaymentAmountFormattedAttribute(): string
    {
        return $this->payment_amount ? 'Rp '.number_format($this->payment_amount, 0, ',', '.') : '-';
    }
}
