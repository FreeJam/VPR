# DATABASE STRUCTURE

## Назначение
Документ фиксирует целевую структуру БД для платформы подготовки к ВПР.
Она должна масштабироваться по классам, предметам, ролям, версиям тестов, ручной проверке и импорту.

## Общие принципы
- все сущности именуются во множественном числе
- связи many-to-many оформляются через отдельные pivot-таблицы
- справочники не захардкоживаются в коде
- критерии оценивания живут в rubric-модели, а не в колонках `k1_score`, `k2_score`
- тесты и их версии разделяются
- все потенциально изменяемые внешние данные лучше хранить с `status` и `meta_json`

## Основные таблицы

### Users and roles
- `users`
- `roles`
- `user_roles`

### Academic
- `grade_levels`
- `subjects`
- `subject_grade_offerings`
- `academic_years`

### Organizations and profiles
- `organizations`
- `teacher_profiles`
- `teacher_codes`
- `student_profiles`
- `parent_profiles`
- `parent_student_links`
- `teacher_student_links`

### Groups
- `teacher_groups`
- `group_members`

### Taxonomy
- `topics`
- `skills`

### Content
- `content_sources`
- `assessments`
- `assessment_versions`
- `assessment_sections`
- `question_types`
- `questions`
- `question_options`
- `question_answers`

### Scoring
- `rubrics`
- `rubric_criteria`
- `rubric_levels`
- `grading_scales`
- `grading_scale_ranges`

### Workflow
- `assignments`
- `attempts`
- `attempt_question_answers`
- `attempt_question_reviews`
- `attempt_criterion_scores`
- `attempt_comments`

### Analytics
- `student_question_stats`

### Gamification
- `achievement_definitions`
- `user_achievements`
- `title_definitions`
- `user_titles`
- `user_counters`

### System
- `notifications`
- `activity_logs`

### Import
- `import_batches`
- `import_errors`
- `assessment_import_links`

## Ключевые связи
- `user` ↔ `roles` = many-to-many
- `teacher` ↔ `student` = many-to-many
- `teacher` → `teacher_groups` = one-to-many
- `teacher_group` ↔ `students` = many-to-many
- `assessment` → `assessment_versions` = one-to-many
- `assessment_version` → `assessment_sections` = one-to-many
- `assessment_section` → `questions` = one-to-many
- `question` → `question_options` = one-to-many
- `question` → `question_answers` = one-to-many
- `question` → `rubric` = one-to-one или one-to-many по выбранной модели
- `rubric` → `rubric_criteria` = one-to-many
- `rubric_criterion` → `rubric_levels` = one-to-many
- `attempt` → `attempt_question_answers` = one-to-many
- `attempt_question_review` → `attempt_criterion_scores` = one-to-many
- `import_batch` → `import_errors` = one-to-many

## Важные поля, которые стоит предусмотреть

### Для справочников
- `code`
- `name`
- `is_active`
- `sort_order`

### Для основных сущностей
- `status`
- `published_at` или аналог
- `meta_json`
- `created_by`
- `updated_by`

### Для тестов и версий
- `slug`
- `assessment_kind`
- `year_label`
- `version_label`
- `source_id`

### Для попыток и проверок
- `started_at`
- `submitted_at`
- `checked_at`
- `auto_score`
- `manual_score`
- `final_score`

## Индексы, которые стоит добавить в ранней версии
- `attempts(student_profile_id, status)`
- `attempts(assignment_id, status)`
- `assignments(teacher_profile_id, due_at)`
- `teacher_student_links(teacher_profile_id, status)`
- `questions(topic_id, skill_id)`
- `question_types(code)`
- `subjects(code)`
- `grade_levels(code)`
- `notifications(user_id, is_read)`
- `activity_logs(entity_type, entity_id)`
- `import_batches(status, created_at)`

## Почему структура именно такая
- критерии ВПР по русскому языку требуют гибкой rubric-модели
- шкалы перевода баллов могут отличаться по предмету и классу
- один и тот же тест может обновляться по версиям
- один ученик может заниматься у нескольких учителей
- импорт требует хранения источника, журнала ошибок и связи с созданным тестом

## Приоритет миграций для первого цикла
1. `users`, `roles`, `user_roles`
2. `grade_levels`, `subjects`, `subject_grade_offerings`, `academic_years`
3. `teacher_profiles`, `student_profiles`, `parent_profiles`
4. `teacher_codes`, `teacher_student_links`, `parent_student_links`
5. `teacher_groups`, `group_members`
6. `content_sources`, `assessments`, `assessment_versions`, `assessment_sections`
7. `question_types`, `questions`, `question_options`, `question_answers`
8. `rubrics`, `rubric_criteria`, `rubric_levels`
9. `grading_scales`, `grading_scale_ranges`
10. `import_batches`, `import_errors`, `assessment_import_links`
11. `assignments`, `attempts`, review-таблицы
