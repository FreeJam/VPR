# CODEX PROMPT

## Роль
Ты продолжаешь уже существующий Laravel 11 проект VPR, а не создаешь его с нуля.

## Фактическое состояние проекта
- git и GitHub уже настроены
- Laravel приложение уже создано
- есть auth, роли, dashboards, import module, teacher students/groups, assignments, attempts, manual review
- тесты проходят: `35 passed`
- локально проект проверен на SQLite

## Что уже считается источником правды
- код в `app/`, `resources/`, `routes/`, `database/`, `tests/`
- текущее состояние и runbook: `WORKSPACE_STATUS.md` и `LOCAL_DEVELOPMENT.md`
- архитектурные ограничения: `PROJECT_ARCHITECTURE.md`
- roadmap следующего этапа: `IMPLEMENTATION_ROADMAP.md`

## Текущий функциональный контур
- роли `admin`, `teacher`, `student`, `parent`
- import JSON `v1.0`
- assessments catalogue
- teacher students and groups
- teacher assignments to student
- teacher assignments to group
- student attempts with save/submit
- auto scoring
- manual review queue
- grade recalculation

## Приоритет дальнейшей разработки
1. отдельные student results/history screens
2. parent progress/results pages
3. admin CRUD для предметов, классов, assessments
4. analytics, notifications, gamification
5. MySQL verification and production deployment polish

## Технические правила
- не ломать текущий working flow import -> assignment -> attempt -> review
- перед изменениями сверять `artisan test`
- для новых фич сразу добавлять feature или unit tests
- поддерживать docs в папке `docs`
