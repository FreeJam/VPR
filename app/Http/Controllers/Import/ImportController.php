<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\RunImportRequest;
use App\Http\Requests\Import\UploadImportRequest;
use App\Models\ImportBatch;
use App\Services\Import\AssessmentImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(Request $request): View
    {
        $query = ImportBatch::query()
            ->with(['errors', 'assessmentLink.assessment'])
            ->latest();

        if (! $request->user()->hasRole('admin')) {
            $query->where('user_id', $request->user()->id);
        }

        return view('imports.index', [
            'imports' => $query->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('imports.create');
    }

    public function store(UploadImportRequest $request, AssessmentImportService $service): RedirectResponse
    {
        $batch = $service->upload($request->file('file'), $request->user());

        return redirect()->route('imports.show', $batch)
            ->with('status', $batch->status === 'validated'
                ? 'Import file uploaded and validated.'
                : 'Import file uploaded, but validation found issues.');
    }

    public function show(ImportBatch $importBatch, AssessmentImportService $service): View
    {
        $this->authorize('view', $importBatch);

        return view('imports.show', [
            'importBatch' => $importBatch->load(['errors', 'assessmentLink.assessment']),
            'preview' => $service->preview($importBatch),
        ]);
    }

    public function preview(ImportBatch $importBatch, AssessmentImportService $service): View
    {
        $this->authorize('view', $importBatch);

        return view('imports.preview', [
            'importBatch' => $importBatch,
            'preview' => $service->preview($importBatch),
        ]);
    }

    public function run(RunImportRequest $request, ImportBatch $importBatch, AssessmentImportService $service): RedirectResponse
    {
        $this->authorize('update', $importBatch);

        $assessment = $service->run($importBatch);

        return redirect()->route('assessments.show', $assessment)
            ->with('status', 'Assessment imported successfully.');
    }
}
