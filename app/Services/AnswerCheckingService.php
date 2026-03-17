<?php

namespace App\Services;

use App\Models\Question;

class AnswerCheckingService
{
    public function scoreQuestion(Question $question, mixed $response): float
    {
        $question->loadMissing(['questionType', 'options', 'answers']);

        return match ($question->questionType->code) {
            'single_choice' => $this->scoreSingleChoice($question, $response),
            'multiple_choice' => $this->scoreMultipleChoice($question, $response),
            'short_text', 'numeric' => $this->scoreAgainstReferenceAnswers($question, $response),
            'multi_field_text' => $this->scoreSetAnswer($question, $response),
            default => 0.0,
        };
    }

    private function scoreSingleChoice(Question $question, mixed $response): float
    {
        $correct = $question->options->firstWhere('is_correct', true)?->option_key;

        return $response === $correct ? (float) $question->max_score : 0.0;
    }

    private function scoreMultipleChoice(Question $question, mixed $response): float
    {
        if (! is_array($response)) {
            return 0.0;
        }

        $submitted = collect($response)->map(fn ($value) => (string) $value)->sort()->values()->all();
        $expected = $question->options
            ->where('is_correct', true)
            ->pluck('option_key')
            ->map(fn ($value) => (string) $value)
            ->sort()
            ->values()
            ->all();

        return $submitted === $expected ? (float) $question->max_score : 0.0;
    }

    private function scoreAgainstReferenceAnswers(Question $question, mixed $response): float
    {
        $normalizedResponse = $this->normalize($response);

        foreach ($question->answers as $answer) {
            $value = $answer->answer_value;

            if ($answer->answer_kind === 'pattern' && is_string($value) && preg_match($value, (string) $response)) {
                return (float) $question->max_score;
            }

            if (in_array($answer->answer_kind, ['exact', 'normalized_text', 'reference_text'], true)
                && $normalizedResponse === $this->normalize($value)) {
                return (float) $question->max_score;
            }
        }

        return 0.0;
    }

    private function scoreSetAnswer(Question $question, mixed $response): float
    {
        if (! is_array($response)) {
            return 0.0;
        }

        $submitted = collect($response)->map(fn ($value) => $this->normalize($value))->sort()->values()->all();

        foreach ($question->answers as $answer) {
            if ($answer->answer_kind !== 'set' || ! is_array($answer->answer_value)) {
                continue;
            }

            $expected = collect($answer->answer_value)->map(fn ($value) => $this->normalize($value))->sort()->values()->all();

            if ($submitted === $expected) {
                return (float) $question->max_score;
            }
        }

        return 0.0;
    }

    private function normalize(mixed $value): string
    {
        if (is_array($value)) {
            return collect($value)->map(fn ($item) => $this->normalize($item))->implode('|');
        }

        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) $value)));
    }
}
