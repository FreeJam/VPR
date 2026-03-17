<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptCriterionScore extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function review(): BelongsTo
    {
        return $this->belongsTo(AttemptQuestionReview::class, 'attempt_question_review_id');
    }

    public function rubricCriterion(): BelongsTo
    {
        return $this->belongsTo(RubricCriterion::class);
    }
}
