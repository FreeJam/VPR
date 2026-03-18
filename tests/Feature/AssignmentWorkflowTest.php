<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentSection;
use App\Models\AssessmentVersion;
use App\Models\Assignment;
use App\Models\Attempt;
use App\Models\AttemptQuestionReview;
use App\Models\GradeLevel;
use App\Models\GradingScale;
use App\Models\TeacherGroup;
use App\Models\TeacherStudentLink;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionType;
use App\Models\Rubric;
use App\Models\RubricCriterion;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_assignment_for_linked_student(): void
    {
        $teacher = User::query()->where('email', 'teacher@vpr.local')->firstOrFail();
        $student = User::query()->where('email', 'student@vpr.local')->firstOrFail();
        $version = $this->createAssessmentVersion($teacher);

        $response = $this->actingAs($teacher)->post(route('assignments.store'), [
            'assessment_version_id' => $version->id,
            'target_type' => 'student',
            'student_profile_id' => $student->studentProfile->id,
            'title' => 'Домашняя работа по ВПР',
            'instructions' => 'Выполни оба задания и отправь работу.',
            'mode' => 'homework',
            'max_attempts' => 2,
        ]);

        $assignment = Assignment::query()->firstOrFail();

        $response->assertRedirect(route('assignments.show', $assignment));
        $this->assertSame('published', $assignment->status);
        $this->assertTrue($assignment->is_published);
        $this->assertSame($teacher->teacherProfile->id, $assignment->teacher_profile_id);
        $this->assertSame($student->studentProfile->id, $assignment->student_profile_id);

        $this->actingAs($teacher)
            ->get(route('assignments.show', $assignment))
            ->assertOk()
            ->assertSee('Домашняя работа по ВПР');

        $this->actingAs($student)
            ->get(route('assignments.show', $assignment))
            ->assertOk()
            ->assertSee('Начать попытку');
    }

    public function test_student_can_submit_attempt_and_teacher_can_review_manual_question(): void
    {
        $teacher = User::query()->where('email', 'teacher@vpr.local')->firstOrFail();
        $student = User::query()->where('email', 'student@vpr.local')->firstOrFail();
        $version = $this->createAssessmentVersion($teacher);

        $assignment = Assignment::query()->create([
            'teacher_profile_id' => $teacher->teacherProfile->id,
            'assessment_version_id' => $version->id,
            'student_profile_id' => $student->studentProfile->id,
            'title' => 'Тренировочная работа',
            'mode' => 'training',
            'status' => 'published',
            'is_published' => true,
            'max_attempts' => 1,
        ]);

        $startResponse = $this->actingAs($student)->post(route('assignments.start', $assignment));
        $attempt = Attempt::query()->firstOrFail();

        $startResponse->assertRedirect(route('attempts.show', $attempt));

        $this->actingAs($student)
            ->get(route('attempts.show', $attempt))
            ->assertOk()
            ->assertSee('Выберите правильный ответ.');

        $saveResponse = $this->actingAs($student)->patch(route('attempts.update', $attempt), [
            'answers' => [
                $this->singleChoiceQuestionId($version) => 'B',
                $this->manualQuestionId($version) => 'Развернутый ответ ученика',
            ],
        ]);

        $saveResponse->assertRedirect(route('attempts.show', $attempt));

        $submitResponse = $this->actingAs($student)->patch(route('attempts.submit', $attempt), [
            'answers' => [
                $this->singleChoiceQuestionId($version) => 'B',
                $this->manualQuestionId($version) => 'Развернутый ответ ученика',
            ],
        ]);

        $submitResponse->assertRedirect(route('attempts.show', $attempt));
        $attempt->refresh();

        $this->assertSame('pending_review', $attempt->status);
        $this->assertSame(1.0, (float) $attempt->auto_score);
        $this->assertSame(1.0, (float) $attempt->final_score);

        $review = AttemptQuestionReview::query()->firstOrFail();

        $queueResponse = $this->actingAs($teacher)->get(route('reviews.index'));
        $queueResponse->assertOk();
        $queueResponse->assertSee((string) $review->question->external_number);

        $this->actingAs($teacher)
            ->get(route('reviews.show', $review))
            ->assertOk()
            ->assertSee('Развернутый ответ ученика');

        $reviewResponse = $this->actingAs($teacher)->patch(route('reviews.update', $review), [
            'scores' => [
                $review->question->rubric->criteria->first()->id => 2,
            ],
            'comment' => 'Отлично раскрыта мысль.',
        ]);

        $reviewResponse->assertRedirect(route('reviews.show', $review));

        $attempt->refresh();

        $this->assertSame('checked', $attempt->status);
        $this->assertSame(2.0, (float) $attempt->manual_score);
        $this->assertSame(3.0, (float) $attempt->final_score);
        $this->assertSame('5', $attempt->grade_label);

        $this->actingAs($student)
            ->get(route('attempts.show', $attempt))
            ->assertOk()
            ->assertSee('Комментарий учителя: Отлично раскрыта мысль.');
    }

    public function test_teacher_can_save_comment_for_manual_open_question_without_rubric(): void
    {
        $teacher = User::query()->where('email', 'teacher@vpr.local')->firstOrFail();
        $student = User::query()->where('email', 'student@vpr.local')->firstOrFail();
        $version = $this->createManualOpenAssessmentVersion($teacher);

        $assignment = Assignment::query()->create([
            'teacher_profile_id' => $teacher->teacherProfile->id,
            'assessment_version_id' => $version->id,
            'student_profile_id' => $student->studentProfile->id,
            'title' => 'Работа с открытым ответом',
            'mode' => 'training',
            'status' => 'published',
            'is_published' => true,
            'max_attempts' => 1,
        ]);

        $this->actingAs($student)->post(route('assignments.start', $assignment));
        $attempt = Attempt::query()->firstOrFail();

        $this->actingAs($student)->patch(route('attempts.submit', $attempt), [
            'answers' => [
                $version->sections->first()->questions->first()->id => 'Ответ без rubric',
            ],
        ]);

        $review = AttemptQuestionReview::query()->firstOrFail();

        $response = $this->actingAs($teacher)->patch(route('reviews.update', $review), [
            'comment' => 'Комментарий без rubric сохранен.',
        ]);

        $response->assertRedirect(route('reviews.show', $review));
        $attempt->refresh();
        $review->refresh();

        $this->assertSame('checked', $attempt->status);
        $this->assertSame('Комментарий без rubric сохранен.', $review->comment);
    }

    public function test_teacher_can_create_group_and_see_linked_students_page(): void
    {
        $teacher = User::query()->where('email', 'teacher@vpr.local')->firstOrFail();
        $secondStudent = $this->createLinkedStudent($teacher, 'group-student@vpr.local', 'Групповой ученик');

        $studentsPage = $this->actingAs($teacher)->get(route('teacher.students.index'));

        $studentsPage->assertOk()
            ->assertSee('Student VPR')
            ->assertSee('Групповой ученик');

        $createResponse = $this->actingAs($teacher)->post(route('teacher.groups.store'), [
            'name' => '6Б Подготовка',
            'grade_level_id' => GradeLevel::query()->where('code', '6')->value('id'),
            'description' => 'Группа для массовых назначений',
            'member_ids' => [
                User::query()->where('email', 'student@vpr.local')->firstOrFail()->studentProfile->id,
                $secondStudent->id,
            ],
        ]);

        $group = TeacherGroup::query()->where('name', '6Б Подготовка')->firstOrFail();

        $createResponse->assertRedirect(route('teacher.groups.show', $group));
        $this->assertCount(2, $group->fresh('members')->members);

        $this->actingAs($teacher)
            ->get(route('teacher.groups.show', $group))
            ->assertOk()
            ->assertSee('6Б Подготовка')
            ->assertSee('Групповой ученик');
    }

    public function test_teacher_can_assign_assessment_to_group(): void
    {
        $teacher = User::query()->where('email', 'teacher@vpr.local')->firstOrFail();
        $defaultStudent = User::query()->where('email', 'student@vpr.local')->firstOrFail()->studentProfile;
        $secondStudent = $this->createLinkedStudent($teacher, 'group-assign@vpr.local', 'Ученик группы');
        $version = $this->createAssessmentVersion($teacher);

        $group = TeacherGroup::query()->create([
            'teacher_profile_id' => $teacher->teacherProfile->id,
            'grade_level_id' => GradeLevel::query()->where('code', '6')->value('id'),
            'name' => 'Массовое назначение',
            'description' => 'Проверка назначения по группе',
            'status' => 'active',
        ]);

        $group->members()->createMany([
            ['student_profile_id' => $defaultStudent->id],
            ['student_profile_id' => $secondStudent->id],
        ]);

        $response = $this->actingAs($teacher)->post(route('assignments.store'), [
            'assessment_version_id' => $version->id,
            'target_type' => 'group',
            'teacher_group_id' => $group->id,
            'title' => 'Назначение группе',
            'instructions' => 'Выполнить всем участникам группы.',
            'mode' => 'homework',
            'max_attempts' => 1,
        ]);

        $response->assertRedirect(route('teacher.groups.show', $group));
        $this->assertSame(2, Assignment::query()->where('teacher_group_id', $group->id)->count());

        $secondAssignment = Assignment::query()
            ->where('teacher_group_id', $group->id)
            ->where('student_profile_id', $secondStudent->id)
            ->firstOrFail();

        $this->assertSame('published', $secondAssignment->status);

        $studentUser = $secondStudent->user;

        $this->actingAs($studentUser)
            ->get(route('assignments.index'))
            ->assertOk()
            ->assertSee('Назначение группе');
    }

    private function createAssessmentVersion(User $teacher): AssessmentVersion
    {
        $assessment = Assessment::query()->create([
            'title' => 'ВПР Demo',
            'slug' => 'vpr-demo-'.uniqid(),
            'subject_id' => Subject::query()->where('code', 'ru')->value('id'),
            'grade_level_id' => GradeLevel::query()->where('code', '6')->value('id'),
            'assessment_kind' => 'trainer',
            'year_label' => '2026',
            'duration_minutes' => 45,
            'status' => 'draft',
            'created_by' => $teacher->id,
        ]);

        $gradingScale = GradingScale::query()->create([
            'assessment_id' => $assessment->id,
            'title' => 'Demo scale',
            'max_primary_score' => 3,
        ]);

        $gradingScale->ranges()->createMany([
            ['grade_label' => '2', 'min_score' => 0, 'max_score' => 1, 'position' => 1],
            ['grade_label' => '4', 'min_score' => 2, 'max_score' => 2, 'position' => 2],
            ['grade_label' => '5', 'min_score' => 3, 'max_score' => 3, 'position' => 3],
        ]);

        $version = AssessmentVersion::query()->create([
            'assessment_id' => $assessment->id,
            'grading_scale_id' => $gradingScale->id,
            'version_label' => 'v1',
            'status' => 'draft',
            'is_current' => true,
        ]);

        $section = AssessmentSection::query()->create([
            'assessment_version_id' => $version->id,
            'title' => 'Основная часть',
            'position' => 1,
        ]);

        $singleChoice = Question::query()->create([
            'assessment_section_id' => $section->id,
            'question_type_id' => QuestionType::query()->where('code', 'single_choice')->value('id'),
            'external_number' => '1',
            'checking_mode' => 'auto',
            'prompt_html' => '<p>Выберите правильный ответ.</p>',
            'max_score' => 1,
            'requires_manual_review' => false,
            'position' => 1,
        ]);

        QuestionOption::query()->create([
            'question_id' => $singleChoice->id,
            'option_key' => 'A',
            'text' => 'Вариант A',
            'is_correct' => false,
            'position' => 1,
        ]);

        QuestionOption::query()->create([
            'question_id' => $singleChoice->id,
            'option_key' => 'B',
            'text' => 'Вариант B',
            'is_correct' => true,
            'position' => 2,
        ]);

        $manual = Question::query()->create([
            'assessment_section_id' => $section->id,
            'question_type_id' => QuestionType::query()->where('code', 'open_response')->value('id'),
            'external_number' => '2',
            'checking_mode' => 'manual_rubric',
            'prompt_html' => '<p>Напишите короткий развернутый ответ.</p>',
            'max_score' => 2,
            'requires_manual_review' => true,
            'position' => 2,
        ]);

        $rubric = Rubric::query()->create([
            'question_id' => $manual->id,
            'title' => 'Критерий',
            'scoring_mode' => 'sum',
        ]);

        $criterion = RubricCriterion::query()->create([
            'rubric_id' => $rubric->id,
            'code' => 'K1',
            'title' => 'Содержательность ответа',
            'max_points' => 2,
            'position' => 1,
        ]);

        $criterion->levels()->createMany([
            ['rubric_criterion_id' => $criterion->id, 'points' => 2, 'description' => 'Полный ответ', 'position' => 1],
            ['rubric_criterion_id' => $criterion->id, 'points' => 1, 'description' => 'Частичный ответ', 'position' => 2],
            ['rubric_criterion_id' => $criterion->id, 'points' => 0, 'description' => 'Нет ответа', 'position' => 3],
        ]);

        return $version->fresh(['sections.questions']);
    }

    private function singleChoiceQuestionId(AssessmentVersion $version): int
    {
        return (int) $version->sections->first()->questions->firstWhere('external_number', '1')->id;
    }

    private function manualQuestionId(AssessmentVersion $version): int
    {
        return (int) $version->sections->first()->questions->firstWhere('external_number', '2')->id;
    }

    private function createManualOpenAssessmentVersion(User $teacher): AssessmentVersion
    {
        $assessment = Assessment::query()->create([
            'title' => 'ВПР Demo Manual Open',
            'slug' => 'vpr-demo-manual-open-'.uniqid(),
            'subject_id' => Subject::query()->where('code', 'ru')->value('id'),
            'grade_level_id' => GradeLevel::query()->where('code', '6')->value('id'),
            'assessment_kind' => 'trainer',
            'year_label' => '2026',
            'duration_minutes' => 45,
            'status' => 'draft',
            'created_by' => $teacher->id,
        ]);

        $gradingScale = GradingScale::query()->create([
            'assessment_id' => $assessment->id,
            'title' => 'Manual only scale',
            'max_primary_score' => 1,
        ]);

        $gradingScale->ranges()->create([
            'grade_label' => '2',
            'min_score' => 0,
            'max_score' => 1,
            'position' => 1,
        ]);

        $version = AssessmentVersion::query()->create([
            'assessment_id' => $assessment->id,
            'grading_scale_id' => $gradingScale->id,
            'version_label' => 'v1',
            'status' => 'draft',
            'is_current' => true,
        ]);

        $section = AssessmentSection::query()->create([
            'assessment_version_id' => $version->id,
            'title' => 'Открытый ответ',
            'position' => 1,
        ]);

        Question::query()->create([
            'assessment_section_id' => $section->id,
            'question_type_id' => QuestionType::query()->where('code', 'open_response')->value('id'),
            'external_number' => '1',
            'checking_mode' => 'manual_open',
            'prompt_html' => '<p>Напишите ответ.</p>',
            'max_score' => 1,
            'requires_manual_review' => true,
            'position' => 1,
        ]);

        return $version->fresh(['sections.questions']);
    }

    private function createLinkedStudent(User $teacher, string $email, string $name): StudentProfile
    {
        $user = User::factory()->create([
            'email' => $email,
            'name' => $name,
        ]);
        $user->assignRole('student', true);

        $studentProfile = $user->studentProfile()->create([
            'display_name' => $name,
            'grade_level_id' => GradeLevel::query()->where('code', '6')->value('id'),
        ]);

        TeacherStudentLink::query()->create([
            'teacher_profile_id' => $teacher->teacherProfile->id,
            'student_profile_id' => $studentProfile->id,
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return $studentProfile->fresh('user');
    }
}
