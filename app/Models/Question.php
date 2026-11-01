<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'points',
    ];

    /**
     * Scope untuk mengambil soal secara acak.
     */
    public function scopeAmbilAcak($query)
    {
        return $query->inRandomOrder();
    }
}
