<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\ImportBatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_upload_and_run_import(): void
    {
        Storage::fake('local');

        $teacher = User::query()->where('email', 'teacher@vpr.local')->firstOrFail();

        $file = UploadedFile::fake()->createWithContent(
            'import.json',
            file_get_contents(base_path('docs/IMPORT_EXAMPLE_RU_6_V1_K1.json'))
        );

        $response = $this->actingAs($teacher)->post(route('imports.store'), [
            'file' => $file,
        ]);

        $batch = ImportBatch::query()->firstOrFail();

        $response->assertRedirect(route('imports.show', $batch));
        $this->assertSame('validated', $batch->status);

        $runResponse = $this->actingAs($teacher)->post(route('imports.run', $batch), [
            'confirm' => '1',
        ]);

        $assessment = Assessment::query()->firstOrFail();

        $runResponse->assertRedirect(route('assessments.show', $assessment));
        $this->assertSame('imported', $batch->fresh()->status);
        $this->assertCount(1, $assessment->versions);
    }

    public function test_student_cannot_access_imports(): void
    {
        $student = User::query()->where('email', 'student@vpr.local')->firstOrFail();

        $this->actingAs($student)
            ->get(route('imports.index'))
            ->assertForbidden();
    }
}
