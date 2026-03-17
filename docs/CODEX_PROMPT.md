# CODEX PROMPT

## Роль
Ты senior full-stack разработчик и архитектор.
Нужно спроектировать и поэтапно реализовать web-платформу подготовки к ВПР на Laravel + MySQL.

## Текущее состояние workspace
- в корне проекта пока есть только папка `docs`
- Laravel-приложение еще не создано
- git в этой папке еще не инициализирован
- документация уже описывает целевую архитектуру, формат импорта и этапы разработки

Перед началом кодинга нужно считать это стартовой точкой, а не искать уже готовый backend.

## Цель проекта
Создать образовательную платформу для учеников 5-11 классов с упором на:
- тесты по ВПР
- тренировочные задания
- кабинет ученика
- кабинет учителя
- кабинет родителя
- ручную и автоматическую проверку
- историю попыток
- аналитику
- достижения, звезды, титулы
- импорт тестов из JSON формата `v1.0`

## Технологический стек
- backend: Laravel 11
- database: MySQL 8
- frontend: Blade + Alpine.js + Tailwind CSS
- deployment target: обычный PHP-хостинг

## Основные роли
- admin
- teacher
- student
- parent

Роли должны храниться гибко через `roles` и `user_roles`.

## Ключевые модули
- Identity and roles
- Academic catalogs
- Teacher, student, parent profiles
- Teacher groups
- Content and assessments
- Scoring and rubrics
- Assignments and attempts
- Manual review
- Analytics
- Gamification
- Notifications and activity log
- Import module

## Базовые бизнес-требования

### Ученик
- регистрация и вход
- выбор класса
- прохождение тестов
- история попыток
- просмотр результатов
- подключение к учителю по коду
- достижения и титулы

### Учитель
- личный кабинет
- код подключения учеников
- подтверждение заявок
- группы учеников
- назначение тестов
- просмотр результатов
- ручная проверка
- комментарии
- аналитика

### Родитель
- просмотр прогресса ребенка
- просмотр оценок и комментариев

### Администратор
- управление пользователями
- управление предметами
- управление классами
- управление тестами
- управление импортом
- управление достижениями

## Контентная модель
Нужно использовать следующие сущности:
- `content_sources`
- `assessments`
- `assessment_versions`
- `assessment_sections`
- `questions`
- `question_types`
- `question_options`
- `question_answers`

Поддерживаемые типы вопросов:
- `single_choice`
- `multiple_choice`
- `short_text`
- `numeric`
- `open_response`
- `compound_open_response`
- `multi_field_text`
- `language_analysis`
- `matching`
- `cloze_text`
- `essay`

## Проверка и оценивание
Платформа должна поддерживать:
- `auto`
- `hybrid`
- `manual_open`
- `manual_rubric`

Используем:
- `rubrics`
- `rubric_criteria`
- `rubric_levels`
- `grading_scales`
- `grading_scale_ranges`
- `attempt_question_reviews`
- `attempt_criterion_scores`

Критерии нельзя хранить отдельными колонками вроде `K1`, `K2`, `K3`.

## Импорт
Нужно реализовать модуль импорта JSON формата `v1.0`.

Поддержать:
- загрузку файла
- валидацию
- предпросмотр
- импорт в `draft`
- журнал ошибок
- лог импортов

Нужные сущности:
- `import_batches`
- `import_errors`
- `assessment_import_links`

Нужные сервисы:
- `AssessmentImportService`
- `AssessmentImportValidator`
- `AssessmentPreviewBuilder`
- `AssessmentImportMapper`
- `GradeCalculationService`
- `AnswerCheckingService`
- `ManualReviewService`
- `AssignmentService`
- `AchievementService`

## Архитектурные правила
- использовать Laravel best practices
- не делать fat controllers
- бизнес-логику выносить в сервисы
- использовать Form Requests
- использовать policies and gates
- придерживаться clean architecture tendencies без переусложнения
- проектировать модели и миграции так, чтобы импорт, ручная проверка и версии тестов не потребовали переделки базы

## UI-требования
- современный интерфейс
- понятный ученикам 5-11 классов
- адаптивность
- крупные понятные элементы
- прогресс и статусы
- отдельные dashboard layouts для teacher и student

## Приоритет первой реализации
Начинать с:
1. инициализации git и Laravel-проекта
2. базовой auth-системы
3. ролей и seeders
4. академических справочников
5. профилей и связей teacher-student
6. контентной модели
7. import module skeleton

## Обязательные сидеры
- `roles`
- `grade_levels` для 5-11
- базовые `subjects`
- `question_types`

## Источники правды в docs
Перед реализацией использовать:
- `README.md`
- `WORKSPACE_STATUS.md`
- `PROJECT_ARCHITECTURE.md`
- `DATABASE_STRUCTURE.md`
- `IMPLEMENTATION_ROADMAP.md`
- `MVP_SCOPE.md`
- `IMPORT_FORMAT_V1.md`
- `LARAVEL_IMPORTER_PLAN.md`
- `SEEDERS_AND_ENUMS.md`
- `PERMISSIONS_MATRIX.md`
