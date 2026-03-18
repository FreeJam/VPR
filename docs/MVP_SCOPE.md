# MVP SCOPE

## Статус MVP
На дату `2026-03-18` базовый MVP собран.

## Что входит в текущий MVP

### Платформа
- Laravel 11 проект
- Laravel Breeze auth
- роли `admin`, `teacher`, `student`, `parent`
- role-based dashboards

### Справочники и профили
- классы 5-11
- предметы
- question types
- teacher/student/parent profiles
- teacher/student и parent/student demo links
- teacher groups

### Контент и импорт
- assessments
- versions
- sections
- questions
- options
- answers
- import JSON `v1.0`
- preview и import log

### Назначения и попытки
- assignment учитель -> ученик
- assignment учитель -> группа
- start/save/submit attempt
- хранение ответов
- итоговые статусы попытки

### Проверка
- auto scoring для simple objective questions
- manual review queue
- rubric-based scoring
- final score + grade label recalculation

## Что пока вне MVP
- student history/results pages как отдельный модуль
- parent progress/results UI
- advanced analytics
- gamification
- notifications
- export/reporting

## Условие готовности MVP
Текущее условие выполнено, потому что:
- teacher может импортировать тест
- teacher может назначить тест ученику
- student может пройти и отправить работу
- система считает objective answers
- teacher может вручную проверить сложные задания
- итоговый score и grade сохраняются
