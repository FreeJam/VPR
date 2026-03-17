# LARAVEL IMPORTER PLAN

## Назначение
Документ описывает, как реализовать импорт JSON формата `v1.0` в Laravel-проекте.

Цель:
- принять JSON-файл
- провалидировать его
- показать предпросмотр
- импортировать тест в БД
- сохранить журнал импорта
- при ошибках дать понятный отчет
- создать тест в статусе `draft`

## Текущий статус
Импортер пока не реализован.
Документ используется как техническое ТЗ для первой итерации import-модуля.

## Общая схема процесса
1. Пользователь с ролью `admin` или `teacher` открывает раздел импорта.
2. Загружает JSON-файл.
3. Система создает запись `import_batches` со статусом `uploaded`.
4. Система читает JSON и запускает валидацию.
5. Если JSON невалиден:
   - статус `failed`
   - ошибки пишутся в `import_errors`
6. Если JSON валиден:
   - статус `validated`
   - показывается предпросмотр
7. Пользователь нажимает `Импортировать`.
8. Система в транзакции создает связанные записи контента и оценивания.
9. Создается запись в `assessment_import_links`.
10. Статус `import_batches` меняется на `imported`.

## Рекомендуемая структура классов

### Controllers
- `App\Http\Controllers\Import\ImportController`

### Requests
- `App\Http\Requests\Import\UploadImportRequest`
- `App\Http\Requests\Import\RunImportRequest`

### Services
- `App\Services\Import\AssessmentImportService`
- `App\Services\Import\AssessmentImportValidator`
- `App\Services\Import\AssessmentPreviewBuilder`
- `App\Services\Import\AssessmentImportMapper`

### DTO / Data
- `App\Data\Import\ImportFileData`
- `App\Data\Import\AssessmentImportData`
- `App\Data\Import\SectionImportData`
- `App\Data\Import\QuestionImportData`
- `App\Data\Import\RubricImportData`

### Exceptions
- `App\Exceptions\Import\ImportValidationException`
- `App\Exceptions\Import\ImportExecutionException`

### Jobs
- `App\Jobs\RunAssessmentImportJob` опционально, не для первой итерации

## Страницы модуля
- `/imports`
- `/imports/upload`
- `/imports/{id}`
- `/imports/{id}/preview`
- `/imports/{id}/run`

### На экране списка импортов показывать
- имя файла
- дату загрузки
- статус
- кто загрузил
- число ошибок
- ссылку на предпросмотр
- ссылку на созданный тест

## Статусы импорта
- `uploaded`
- `validated`
- `imported`
- `failed`

## Таблицы импорта

### `import_batches`
- `id`
- `user_id`
- `source_type`
- `original_filename`
- `stored_path`
- `format_version`
- `status`
- `total_items`
- `imported_items`
- `error_count`
- `payload_hash`
- `created_at`
- `updated_at`

### `import_errors`
- `id`
- `import_batch_id`
- `entity_type`
- `entity_index`
- `field_name`
- `error_message`
- `raw_payload_json`
- `created_at`
- `updated_at`

### `assessment_import_links`
- `id`
- `import_batch_id`
- `assessment_id`
- `assessment_version_id`
- `created_at`
- `updated_at`

## Поток валидации

### 1. Проверка файла
Проверять:
- MIME type
- размер файла
- что это JSON
- что файл читается

### 2. Проверка JSON-структуры
Проверять:
- есть ли `format_version`
- `format_version == "1.0"`
- есть ли `assessment`
- есть ли `sections`
- хотя бы одна секция
- хотя бы один вопрос

### 3. Семантическая проверка
Проверять:
- `subject_code` существует в БД
- `grade_code` существует в БД
- `question_type_code` известен
- `checking_mode` допустим
- если `checking_mode = auto`, есть `answers` или `options`
- если есть `rubric`, критерии не пустые и коды уникальны
- если есть `grading_scale`, диапазоны не пересекаются

### 4. Проверка на дубли
Проверять:
- нет ли уже импортированного теста с тем же названием и тем же источником
- не дублируется ли версия
- не совпадает ли `payload_hash`

## Responsibilities по сервисам

### `AssessmentImportValidator`
Отвечает за:
- синтаксическую валидацию JSON
- базовую схему
- семантическую валидацию
- сбор всех ошибок без падения на первой

Публичные методы:
- `validateUploadedFile(UploadedFile $file): array`
- `validateDecodedPayload(array $payload): ValidationResultData`

### `AssessmentPreviewBuilder`
Отвечает за сбор preview для UI.

Что показывать:
- название теста
- предмет
- класс
- количество секций
- количество вопросов
- количество ручных вопросов
- количество вопросов с рубриками
- максимальный балл
- есть ли шкала оценивания

### `AssessmentImportMapper`
Отвечает за маппинг JSON в DTO и доменные объекты, чтобы не таскать сырой массив по всему коду.

### `AssessmentImportService`
Основной оркестратор импорта.

Что делает:
1. получает `ImportBatch`
2. читает JSON
3. вызывает `AssessmentImportValidator`
4. при ошибках пишет `import_errors` и помечает batch как `failed`
5. при успехе запускает транзакцию и создает записи
6. создает связь в `assessment_import_links`
7. обновляет статус batch
8. возвращает созданный `Assessment`

## Последовательность создания сущностей в транзакции
1. `content_sources`
2. `assessments`
3. `assessment_versions`
4. `grading_scales`
5. `grading_scale_ranges`
6. `assessment_sections`
7. `questions`
8. `question_options`
9. `question_answers`
10. `rubrics`
11. `rubric_criteria`
12. `rubric_levels`
13. `assessment_import_links`

## Формальные правила запросов

### `UploadImportRequest`
- `file` required
- `file` mimes: `json,txt`
- `file` max: `10240`

### `RunImportRequest`
- `confirm` accepted

## Пример маршрутов

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('/imports/upload', [ImportController::class, 'create'])->name('imports.create');
    Route::post('/imports/upload', [ImportController::class, 'store'])->name('imports.store');
    Route::get('/imports/{importBatch}', [ImportController::class, 'show'])->name('imports.show');
    Route::get('/imports/{importBatch}/preview', [ImportController::class, 'preview'])->name('imports.preview');
    Route::post('/imports/{importBatch}/run', [ImportController::class, 'run'])->name('imports.run');
});
```

## Правила обработки ошибок

### Blocking errors
- битый JSON
- нет обязательных полей
- неизвестный класс
- неизвестный предмет
- вопрос без типа

### Warnings
- отсутствует `grading_scale`
- нет `answer_source_file`
- у критериев нет `levels`
- `description` пустое

Импортер не должен падать белой страницей.

## Идемпотентность и аудит
- импорт всегда идет в `DB::transaction()`
- при ошибке все откатывается
- `payload_hash` нужен для поиска дублей
- в `activity_logs` нужно писать, кто загрузил и кто запустил импорт

## Что обязательно сделать в первой версии
- upload
- validate
- preview
- import
- error log
- draft assessment

## Что можно отложить
- background jobs
- zip с картинками
- повторный импорт как новая версия
- diff между версиями
- автообновление уже существующей версии
