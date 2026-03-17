# Docs Index

## Назначение
Эта папка хранит всю рабочую документацию по проекту VPR.
Пока кодовой базы нет, поэтому `docs` фактически является основным источником правды по проекту.

## Текущее состояние
- в проекте пока есть только папка `docs`
- Laravel-приложение еще не создано
- git в папке проекта еще не инициализирован
- формат импорта и целевая архитектура уже описаны

## С чего читать документацию
1. `WORKSPACE_STATUS.md`
2. `PROJECT_ARCHITECTURE.md`
3. `DATABASE_STRUCTURE.md`
4. `IMPLEMENTATION_ROADMAP.md`
5. `MVP_SCOPE.md`
6. `IMPORT_FORMAT_V1.md`
7. `LARAVEL_IMPORTER_PLAN.md`

## Карта документов

### Базовый контекст
- `WORKSPACE_STATUS.md` - текущее состояние проекта и стартовая точка
- `CODEX_PROMPT.md` - сжатое ТЗ для дальнейших coding-сессий
- `DECISIONS_LOG.md` - принятые архитектурные решения

### Архитектура и база
- `PROJECT_ARCHITECTURE.md` - модули и основные потоки
- `DATABASE_STRUCTURE.md` - структура таблиц и связи
- `DATA_NORMALIZATION_RULES.md` - правила кодов, slug, meta_json и дедупликации

### Реализация
- `IMPLEMENTATION_ROADMAP.md` - порядок этапов
- `MVP_SCOPE.md` - что входит в первую рабочую версию
- `SEEDERS_AND_ENUMS.md` - стартовые справочники и enum-значения
- `PERMISSIONS_MATRIX.md` - права по ролям
- `UI_PAGES_AND_ROLES.md` - страницы и маршруты по кабинетам
- `TESTING_PLAN.md` - план unit/feature тестов

### Импорт
- `IMPORT_FORMAT_V1.md` - контракт импортного JSON
- `IMPORT_SCHEMA_V1.json` - JSON Schema
- `IMPORT_EXAMPLE_RU_6_V1_K1.json` - пример файла импорта
- `LARAVEL_IMPORTER_PLAN.md` - серверная реализация import pipeline

### Репозиторий
- `GITHUB_SETUP.md` - как инициализировать git и подключить GitHub

## Ближайшие практические шаги
1. Инициализировать git и привязать GitHub-репозиторий.
2. Создать Laravel 11 проект в корне.
3. Поднять базовую auth-систему.
4. Реализовать роли, справочники и seeders.
5. Начать с миграций по `DATABASE_STRUCTURE.md`.
