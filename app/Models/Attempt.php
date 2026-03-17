<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'checked_at' => 'datetime',
            'meta_json' => 'array',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function questionAnswers(): HasMany
    {
        return $this->hasMany(AttemptQuestionAnswer::class);
    }

    public function questionReviews(): HasMany
    {
        return $this->hasMany(AttemptQuestionReview::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AttemptComment::class);
    }
}
