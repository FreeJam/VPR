# Docs Index

## Назначение
Эта папка хранит рабочую документацию по проекту VPR.
Сейчас кодовая база уже существует, а `docs` фиксирует реальное состояние проекта, принятые решения и инструкции для следующих сессий.

## Текущее состояние
- Laravel 11 приложение собрано и подключено к GitHub
- базовый MVP уже реализован: auth, роли, импорт, students/groups, назначения, попытки, review
- локально подтверждены тесты, миграции, сборка и аудит зависимостей
- `docs` больше не описывает только планы, а сопровождает уже работающий код

## С чего читать документацию
1. `WORKSPACE_STATUS.md`
2. `LOCAL_DEVELOPMENT.md`
3. `PROJECT_ARCHITECTURE.md`
4. `DATABASE_STRUCTURE.md`
5. `IMPLEMENTATION_ROADMAP.md`
6. `MVP_SCOPE.md`
7. `IMPORT_FORMAT_V1.md`

## Карта документов

### Базовый контекст
- `WORKSPACE_STATUS.md` - текущее состояние проекта и стартовая точка
- `LOCAL_DEVELOPMENT.md` - команды, demo users и ручной runbook
- `CODEX_PROMPT.md` - сжатый prompt для следующих coding-сессий
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
- `GITHUB_SETUP.md` - текущее состояние git/GitHub и рабочий workflow

## Ближайшие практические шаги
1. При необходимости импортировать demo JSON из `docs/IMPORT_EXAMPLE_RU_6_V1_K1.json`.
2. Реализовать student history/results screens отдельным экраном.
3. Добрать parent UI, аналитику и admin CRUD.
4. Подготовить production deployment guide.
5. При необходимости перевести dev/prod окружение на MySQL-проверку.
