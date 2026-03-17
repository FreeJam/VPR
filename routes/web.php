<?php

use App\Http\Controllers\Assignment\AssignmentController;
use App\Http\Controllers\Assignment\AttemptController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Import\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Review\ReviewController;
use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\ImportBatch;
use App\Models\AttemptQuestionReview;
use App\Models\TeacherGroup;
use App\Models\TeacherStudentLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/assessments', function (Request $request) {
        $query = Assessment::query()
            ->with(['subject', 'gradeLevel', 'versions'])
            ->latest();

        if (! $request->user()->hasRole('admin')) {
            $query->where('status', 'published');
        }

        return view('assessments.index', [
            'assessments' => $query->paginate(15),
        ]);
    })->name('assessments.index')->can('viewAny', Assessment::class);

    Route::get('/assessments/{assessment}', function (Assessment $assessment) {
        return view('assessments.show', [
            'assessment' => $assessment->load([
                'subject',
                'gradeLevel',
                'versions.sections.questions.questionType',
            ]),
        ]);
    })->name('assessments.show')->can('view', 'assessment');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('dashboards.admin', [
            'stats' => [
                'users' => User::query()->count(),
                'assessments' => Assessment::query()->count(),
                'imports' => ImportBatch::query()->count(),
            ],
        ]);
    })->name('dashboard');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/', function (Request $request) {
        $teacherProfile = $request->user()->teacherProfile;

        return view('dashboards.teacher', [
            'stats' => [
                'students' => TeacherStudentLink::query()
                    ->where('teacher_profile_id', $teacherProfile?->id)
                    ->where('status', 'approved')
                    ->count(),
                'groups' => TeacherGroup::query()
                    ->where('teacher_profile_id', $teacherProfile?->id)
                    ->count(),
                'assignments' => Assignment::query()
                    ->where('teacher_profile_id', $teacherProfile?->id)
                    ->count(),
                'imports' => ImportBatch::query()
                    ->where('user_id', $request->user()->id)
                    ->count(),
                'pending_reviews' => AttemptQuestionReview::query()
                    ->whereNull('reviewed_at')
                    ->whereHas('attempt.assignment', fn ($query) => $query->where('teacher_profile_id', $teacherProfile?->id))
                    ->count(),
            ],
        ]);
    })->name('dashboard');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/', function (Request $request) {
        $studentProfile = $request->user()->studentProfile;

        return view('dashboards.student', [
            'stats' => [
                'teachers' => TeacherStudentLink::query()
                    ->where('student_profile_id', $studentProfile?->id)
                    ->where('status', 'approved')
                    ->count(),
                'assignments' => Assignment::query()
                    ->where('student_profile_id', $studentProfile?->id)
                    ->count(),
                'published_assessments' => Assessment::query()
                    ->where('status', 'published')
                    ->count(),
            ],
        ]);
    })->name('dashboard');
});

Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/', function (Request $request) {
        return view('dashboards.parent', [
            'stats' => [
                'linked_children' => $request->user()->parentProfile?->studentLinks()->count() ?? 0,
            ],
        ]);
    })->name('dashboard');
});

Route::middleware(['auth', 'role:admin,teacher'])->prefix('imports')->name('imports.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::get('/create', [ImportController::class, 'create'])->name('create');
    Route::post('/', [ImportController::class, 'store'])->name('store');
    Route::get('/{importBatch}', [ImportController::class, 'show'])->name('show');
    Route::get('/{importBatch}/preview', [ImportController::class, 'preview'])->name('preview');
    Route::post('/{importBatch}/run', [ImportController::class, 'run'])->name('run');
});

Route::middleware('auth')->group(function () {
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');

    Route::middleware('role:teacher')->group(function () {
        Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    });

    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

    Route::middleware('role:student')->group(function () {
        Route::post('/assignments/{assignment}/start', [AttemptController::class, 'start'])->name('assignments.start');
        Route::get('/attempts/{attempt}', [AttemptController::class, 'show'])->name('attempts.show');
        Route::patch('/attempts/{attempt}', [AttemptController::class, 'update'])->name('attempts.update');
        Route::patch('/attempts/{attempt}/submit', [AttemptController::class, 'submit'])->name('attempts.submit');
    });
});

require __DIR__.'/auth.php';
