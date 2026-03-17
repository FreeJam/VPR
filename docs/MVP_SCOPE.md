# MVP SCOPE

## Цель MVP
Получить первую рабочую версию платформы, в которой учитель может загрузить или создать тест, назначить его ученику, ученик может пройти работу, а система способна сохранить результат и поддержать ручную проверку.

## Входит в MVP

### Платформа
- Laravel 11 проект
- базовая auth-система
- роли `admin`, `teacher`, `student`
- базовые dashboard pages

### Справочники
- классы 5-11
- предметы
- типы вопросов
- учебный год

### Пользовательские связи
- teacher profile
- student profile
- teacher codes
- teacher-student links
- teacher groups

### Контент
- assessments
- assessment versions
- sections
- questions
- options
- answers

### Оценивание
- rubrics
- rubric criteria
- grading scales
- авто- и ручные режимы проверки

### Импорт
- upload JSON
- validate
- preview
- import в `draft`
- import log

### Прохождение тестов
- assignments
- attempts
- answer saving
- submission
- basic results page

### Проверка
- базовая автопроверка для простых вопросов
- ручная проверка open/rubric вопросов
- итоговый пересчет балла

## Не входит в MVP
- parent кабинет
- расширенная аналитика
- сложная геймификация
- email/push уведомления
- импорт PDF напрямую
- background import jobs
- export в Excel/CSV
- multi-organization billing

## Условие готовности MVP
- админ или учитель может импортировать тест
- учитель может назначить тест ученику или группе
- ученик может пройти попытку
- система сохраняет ответы и считает простые задания
- учитель может вручную проверить сложные задания
- участники видят итоговый результат
