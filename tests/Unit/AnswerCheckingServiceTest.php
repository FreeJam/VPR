<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\AssessmentSection;
use App\Models\AssessmentVersion;
use App\Models\GradeLevel;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use App\Models\QuestionType;
use App\Models\Subject;
use App\Services\AnswerCheckingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerCheckingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_scores_single_choice_question(): void
    {
        $question = $this->makeQuestion('single_choice');

        QuestionOption::query()->create([
            'question_id' => $question->id,
            'option_key' => 'A',
            'text' => 'A',
            'is_correct' => false,
            'position' => 1,
        ]);
        QuestionOption::query()->create([
            'question_id' => $question->id,
            'option_key' => 'B',
            'text' => 'B',
            'is_correct' => true,
            'position' => 2,
        ]);

        $score = app(AnswerCheckingService::class)->scoreQuestion($question->fresh(), 'B');

        $this->assertSame(3.0, $score);
    }

    public function test_service_scores_set_answer_question(): void
    {
        $question = $this->makeQuestion('multi_field_text', 2);

        QuestionAnswer::query()->create([
            'question_id' => $question->id,
            'answer_kind' => 'set',
            'answer_value' => ['first', 'second'],
        ]);

        $score = app(AnswerCheckingService::class)->scoreQuestion($question->fresh(), ['second', 'first']);

        $this->assertSame(2.0, $score);
    }

    private function makeQuestion(string $typeCode, int $maxScore = 3): Question
    {
        $subject = Subject::query()->where('code', 'ru')->firstOrFail();
        $grade = GradeLevel::query()->where('code', '6')->firstOrFail();
        $type = QuestionType::query()->where('code', $typeCode)->firstOrFail();

        $assessment = Assessment::query()->create([
            'subject_id' => $subject->id,
            'grade_level_id' => $grade->id,
            'title' => 'Demo',
            'slug' => 'demo-'.$typeCode.'-'.uniqid(),
            'assessment_kind' => 'trainer',
            'status' => 'draft',
        ]);

        $version = AssessmentVersion::query()->create([
            'assessment_id' => $assessment->id,
            'version_label' => 'v1',
            'status' => 'draft',
            'is_current' => true,
        ]);

        $section = AssessmentSection::query()->create([
            'assessment_version_id' => $version->id,
            'title' => 'Section',
            'position' => 1,
        ]);

        return Question::query()->create([
            'assessment_section_id' => $section->id,
            'question_type_id' => $type->id,
            'external_number' => '1',
            'checking_mode' => 'auto',
            'prompt_html' => '<p>Question</p>',
            'max_score' => $maxScore,
            'position' => 1,
        ]);
    }
}
