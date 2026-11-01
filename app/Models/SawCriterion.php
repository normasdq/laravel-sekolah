<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SawCriterion extends Model
{
    use HasFactory;

    protected $table = 'saw_criteria';

    protected $fillable = [
        'code',
        'name',
        'type',
        'weight',
        'source',
        'min_value',
        'max_value',
        'scale_options',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'float',
        'min_value' => 'float',
        'max_value' => 'float',
        'scale_options' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scores(): HasMany
    {
        return $this->hasMany(SawScore::class, 'saw_criterion_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

