<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SawScore extends Model
{
    use HasFactory;

    protected $table = 'saw_scores';

    protected $fillable = [
        'registration_id',
        'saw_criterion_id',
        'value',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(SawCriterion::class, 'saw_criterion_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

