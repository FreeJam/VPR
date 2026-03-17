# PROJECT ARCHITECTURE

## Назначение
Платформа подготовки к ВПР для 5-11 классов на Laravel 11.

## Текущее состояние
Сейчас проект существует как набор проектных документов в папке `docs`.
Код и Laravel-каркас еще не созданы.

## Технологический стек
- Laravel 11
- MySQL 8
- Blade
- Alpine.js
- Tailwind CSS

## Основные роли
- admin
- teacher
- student
- parent

## Архитектурные модули

### 1. Identity
- `users`
- `roles`
- `user_roles`

### 2. Academic
- `grade_levels`
- `subjects`
- `subject_grade_offerings`
- `academic_years`

### 3. Profiles
- `teacher_profiles`
- `student_profiles`
- `parent_profiles`
- `parent_student_links`
- `teacher_student_links`
- `teacher_codes`

### 4. Groups
- `teacher_groups`
- `group_members`

### 5. Content
- `content_sources`
- `assessments`
- `assessment_versions`
- `assessment_sections`
- `questions`
- `question_types`
- `question_options`
- `question_answers`

### 6. Scoring
- `rubrics`
- `rubric_criteria`
- `rubric_levels`
- `grading_scales`
- `grading_scale_ranges`

### 7. Assignments and attempts
- `assignments`
- `attempts`
- `attempt_question_answers`
- `attempt_question_reviews`
- `attempt_criterion_scores`
- `attempt_comments`

### 8. Analytics
- `topics`
- `skills`
- `student_question_stats`

### 9. Gamification
- `achievement_definitions`
- `user_achievements`
- `title_definitions`
- `user_titles`
- `user_counters`

### 10. System
- `notifications`
- `activity_logs`

### 11. Import
- `import_batches`
- `import_errors`
- `assessment_import_links`

## Главные пользовательские потоки

### Учитель
1. получает кабинет и код подключения
2. собирает учеников и группы
3. создает или импортирует тест
4. назначает тест
5. проверяет результаты
6. смотрит аналитику

### Ученик
1. подключается к учителю
2. получает назначенный тест
3. проходит попытку
4. получает автооценку и ручную проверку
5. видит прогресс и достижения

### Родитель
1. привязывается к ребенку
2. смотрит прогресс
3. видит результаты и комментарии

### Администратор
1. управляет справочниками и пользователями
2. контролирует контент и импорт
3. следит за ролями, правами и журналом событий

## Важные архитектурные правила
- не зашивать предметы и классы в код
- не хранить критерии в отдельных колонках вида `k1_score`
- использовать versioning для тестов
- сложные проверки выносить в сервисы
- импорт держать отдельным модулем
- проектировать под обычный PHP-хостинг и без обязательного SPA

## Рекомендуемые сервисы
- `AnswerCheckingService`
- `ManualReviewService`
- `AssignmentService`
- `AchievementService`
- `GradeCalculationService`
- `AssessmentImportService`
- `AssessmentImportValidator`

## UI-принципы
- современный интерфейс
- подходит для 5-11 классов
- не слишком детский
- крупные и понятные элементы
- прогресс-бары
- понятные статусы
- обязательная мобильная адаптация

## Что считать источником правды
- архитектура модулей: этот документ
- таблицы и связи: `DATABASE_STRUCTURE.md`
- порядок реализации: `IMPLEMENTATION_ROADMAP.md`
- MVP-границы: `MVP_SCOPE.md`
- импорт: `IMPORT_FORMAT_V1.md` и `LARAVEL_IMPORTER_PLAN.md`
