# TESTING PLAN

## Цель
Покрыть критические сценарии платформы так, чтобы можно было безопасно развивать импорт, прохождение тестов и ручную проверку.

## Приоритеты
1. импорт
2. роли и доступ
3. прохождение попыток
4. ручная проверка
5. расчет итогового балла

## Unit tests

### Import
- validation of required fields
- invalid `format_version`
- invalid `question_type_code`
- invalid `grade_code`
- grading scale overlap
- rubric criteria uniqueness
- duplicate payload hash detection

### Scoring
- exact answer checking
- normalized text checking
- set answer checking
- numeric answer checking
- rubric total score calculation
- grading scale conversion

### Domain helpers
- role resolver
- assignment availability rules
- attempt status transitions

## Feature tests

### Auth and access
- login redirects by role
- teacher cannot open admin pages
- student cannot run import
- parent sees only linked child records

### Import flow
- upload valid file
- preview valid file
- import valid file
- fail on invalid file
- create linked DB records

### Assessment and assignment flow
- teacher creates assignment
- student sees assigned work
- student starts attempt
- answers are saved
- attempt can be submitted

### Review flow
- auto-checkable answers are scored
- manual review queue is visible to teacher
- teacher can set criterion scores
- final score is recalculated after review

## Минимум перед первым релизом
- feature tests для auth и ролей
- feature tests для import happy path
- feature tests для take attempt flow
- unit tests для scoring и validator
