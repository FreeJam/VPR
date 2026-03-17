<?php

namespace Tests\Unit;

use App\Services\Import\AssessmentImportValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentImportValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_validator_detects_overlapping_grading_ranges(): void
    {
        $payload = json_decode(file_get_contents(base_path('docs/IMPORT_EXAMPLE_RU_6_V1_K1.json')), true);
        $payload['grading_scale']['ranges'][1]['min_score'] = 10;

        $validator = app(AssessmentImportValidator::class);
        $result = $validator->validateDecodedPayload($payload);

        $this->assertFalse($result['is_valid']);
        $this->assertTrue(
            collect($result['errors'])->contains(
                fn (array $error) => str_contains($error['error_message'], 'must not overlap')
            )
        );
    }
}
