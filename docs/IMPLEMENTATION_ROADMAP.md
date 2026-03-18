# IMPLEMENTATION ROADMAP

## Фактическая стадия на 2026-03-18
Базовый MVP уже собран и проверен.

## Уже завершено
- git + GitHub setup
- Laravel 11 bootstrap
- Breeze auth
- роли и role-based dashboards
- академические справочники и seeders
- teacher/student/parent demo links
- import module `v1.0`
- assessment catalogue
- assignments teacher -> student
- teacher students/groups UI
- assignments teacher -> group
- attempts with draft/save/submit
- auto scoring
- manual review queue
- feature and unit tests для ключевых сценариев

## Текущий рабочий MVP
1. учитель импортирует JSON
2. учитель открывает assessment и создает assignment
3. ученик проходит attempt
4. система считает objective questions
5. teacher завершает manual review
6. итоговая оценка пересчитывается

## Следующие этапы

### Этап A. Teacher operations polish
- список linked students и заявки
- более детальная страница результатов

Статус:
- teacher groups UI и group assignment уже готовы
- linked students list уже готов
- детальные results pages остаются следующим подэтапом

### Этап B. Student results polish
- история попыток
- отдельная страница результата
- улучшенный режим прохождения длинных работ

### Этап C. Parent and admin surface
- parent progress/results pages
- admin CRUD для предметов, классов, тестов и импортов

### Этап D. Advanced platform modules
- analytics
- notifications
- gamification
- activity log

## Правило на ближайшие итерации
Каждый следующий этап не должен ломать уже подтвержденный сценарий:
`import -> assignment -> attempt -> review`.
