# PROJECT ARCHITECTURE

## Назначение
Платформа подготовки к ВПР для 5-11 классов на Laravel 11.

## Фактическое состояние
На дату `2026-03-18` проект уже существует как рабочее Laravel-приложение, а не только как набор документов.

## Технологический стек
- Laravel 11
- Blade
- Alpine.js
- Tailwind CSS
- SQLite для локальной разработки
- MySQL 8 остается целевым production storage

## Основные роли
- `admin`
- `teacher`
- `student`
- `parent`

## Реализованные модули

### 1. Identity
- `users`
- `roles`
- `user_roles`
- role redirect service

### 2. Academic
- `grade_levels`
- `subjects`
- `subject_grade_offerings`
- `academic_years`

### 3. Profiles and links
- `teacher_profiles`
- `student_profiles`
- `parent_profiles`
- `parent_student_links`
- `teacher_student_links`
- `teacher_codes`

### 4. Content
- `content_sources`
- `assessments`
- `assessment_versions`
- `assessment_sections`
- `questions`
- `question_types`
- `question_options`
- `question_answers`

### 5. Scoring
- `rubrics`
- `rubric_criteria`
- `rubric_levels`
- `grading_scales`
- `grading_scale_ranges`
- `AnswerCheckingService`
- `GradeCalculationService`
- `ManualReviewService`

### 6. Import
- `import_batches`
- `import_errors`
- `assessment_import_links`
- `AssessmentImportService`
- `AssessmentImportValidator`
- `AssessmentPreviewBuilder`
- `AssessmentImportMapper`

### 7. Assignments and attempts
- `assignments`
- `attempts`
- `attempt_question_answers`
- `attempt_question_reviews`
- `attempt_criterion_scores`
- `attempt_comments`
- `AttemptFlowService`
- teacher groups UI built on top of `teacher_groups` and `group_members`

## Основные пользовательские потоки

### Учитель
1. импортирует JSON-тест
2. управляет linked students и teacher groups
3. открывает карточку assessment
4. создает assignment для ученика или группы
5. отслеживает review queue
6. выставляет rubric/open scores

### Ученик
1. открывает `Назначения`
2. запускает attempt
3. сохраняет ответы
4. отправляет работу
5. получает итог и комментарий учителя

### Администратор
1. входит в admin dashboard
2. контролирует сводные показатели пользователей, тестов и импортов

### Родитель
1. имеет базовый dashboard и link к ребенку
2. полноценный results/progress UI пока остается следующим этапом

## Архитектурные правила
- бизнес-логика остается в сервисах, а не в controllers
- роли и доступ регулируются policy + middleware
- rubric criteria не превращаются в отдельные БД-колонки
- import остается отдельным bounded module
- версии assessment сохраняют воспроизводимость результатов

## Следующий технический backlog
- отдельные student results/history screens
- parent results/progress UI
- admin CRUD pages
- analytics, gamification, notifications
