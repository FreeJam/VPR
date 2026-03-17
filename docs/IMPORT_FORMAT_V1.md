# IMPORT FORMAT v1.0

## Назначение
Формат `v1.0` предназначен для импорта тестов ВПР и тренировочных заданий в платформу на Laravel + MySQL.

Рабочий поток:
1. PDF с вариантом и PDF с системой оценивания анализируются внешним AI-инструментом.
2. AI формирует JSON по этому формату.
3. Сайт импортирует JSON и создает тест, задания, критерии и шкалу оценивания.

## Связанные файлы
- схема: `IMPORT_SCHEMA_V1.json`
- пример: `IMPORT_EXAMPLE_RU_6_V1_K1.json`
- серверная реализация: `LARAVEL_IMPORTER_PLAN.md`

## Основные принципы
- основной формат импорта: JSON
- обязательна версионность через `format_version`
- неизвестные дополнительные поля не должны ломать импорт
- импорт всегда идет в `draft`
- публикация после импорта выполняется отдельно

## Корневая структура JSON

```json
{
  "format_version": "1.0",
  "source": {},
  "assessment": {},
  "grading_scale": {},
  "sections": []
}
```

## Верхний уровень

### `format_version`
Строка. Для текущей версии всегда:

```json
"format_version": "1.0"
```

### `source`
Информация об источнике.

Пример:

```json
{
  "type": "pdf",
  "title": "ВПР 2026 Русский язык 6 класс Вариант 1",
  "original_file": "vpr26-6kl-ru-v1_k1.pdf",
  "source_url": "https://example.com/file.pdf",
  "answer_source_file": "vpr26-6kl-ru-v1-o_k1.pdf"
}
```

Поля:
- `type` = `pdf | manual | import`
- `title`
- `original_file`
- `source_url`
- `answer_source_file`

### `assessment`
Метаданные теста.

Пример:

```json
{
  "title": "ВПР 2026 Русский язык 6 класс Вариант 1",
  "subject_code": "ru",
  "grade_code": "6",
  "assessment_kind": "vpr_official_style",
  "year_label": "2026",
  "duration_minutes": 45,
  "description": "Тренировочный вариант ВПР"
}
```

Поля:
- `title` обязательно
- `subject_code` обязательно, например `ru`, `math`
- `grade_code` обязательно, например `5`, `6`, `7`, `8`, `9`, `10`, `11`
- `assessment_kind` = `vpr_official_style | trainer | teacher_custom`
- `year_label`
- `duration_minutes`
- `description`

### `grading_scale`
Шкала перевода первичных баллов в отметку.

Пример:

```json
{
  "title": "Шкала ВПР 2026 Русский язык 6 класс",
  "max_primary_score": 25,
  "ranges": [
    { "grade_label": "2", "min_score": 0, "max_score": 12 },
    { "grade_label": "3", "min_score": 13, "max_score": 16 },
    { "grade_label": "4", "min_score": 17, "max_score": 20 },
    { "grade_label": "5", "min_score": 21, "max_score": 25 }
  ]
}
```

### `sections`
Массив секций теста.

Пример:

```json
[
  {
    "title": "Основная часть",
    "position": 1,
    "questions": []
  }
]
```

## Вопросы
Каждый вопрос находится внутри `sections[].questions[]`.

Базовый пример:

```json
{
  "external_number": "1",
  "question_type_code": "open_response",
  "checking_mode": "manual_rubric",
  "prompt_html": "<p>Текст задания</p>",
  "instruction_html": "<p>Инструкция</p>",
  "max_score": 9,
  "requires_manual_review": true
}
```

### Обязательные поля вопроса
- `external_number`
- `question_type_code`
- `checking_mode`
- `prompt_html`
- `max_score`

### Допустимые `question_type_code`
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

### Допустимые `checking_mode`
- `auto`
- `hybrid`
- `manual_open`
- `manual_rubric`

## Варианты ответов
Для тестовых заданий:

```json
"options": [
  { "key": "A", "text": "Первый вариант", "is_correct": false },
  { "key": "B", "text": "Второй вариант", "is_correct": true }
]
```

## Эталоны ответов
Для открытых, коротких или гибридных вопросов:

```json
"answers": [
  {
    "answer_kind": "normalized_text",
    "answer_value": "правильный ответ"
  }
]
```

### Допустимые `answer_kind`
- `exact`
- `normalized_text`
- `set`
- `pattern`
- `reference_text`

## Составные ответы
Для заданий, где ответ состоит из нескольких частей:

```json
"response_structure": {
  "parts": [
    {
      "code": "word",
      "type": "short_text",
      "label": "Многозначное слово"
    },
    {
      "code": "sentence",
      "type": "open_text",
      "label": "Предложение"
    }
  ]
}
```

## Рубрики и критерии
Для ручной проверки по критериям:

```json
"rubric": {
  "title": "Критерии задания 1",
  "scoring_mode": "sum",
  "criteria": [
    {
      "code": "1K1",
      "title": "Соблюдение орфографических норм",
      "max_points": 4,
      "levels": [
        { "points": 4, "description": "Ошибок нет" },
        { "points": 3, "description": "Не более двух ошибок" },
        { "points": 2, "description": "Три-четыре ошибки" },
        { "points": 1, "description": "Пять ошибок" },
        { "points": 0, "description": "Более пяти ошибок" }
      ]
    }
  ]
}
```

Поля:
- `title`
- `scoring_mode` = `sum | max | custom_formula`
- `criteria[]`
  - `code`
  - `title`
  - `max_points`
  - `description`
  - `levels[]`
    - `points`
    - `description`

## Требования к валидации
- `format_version` обязателен
- `assessment.title` обязателен
- `subject_code` обязателен
- `grade_code` обязателен
- должна быть хотя бы одна секция
- в секции должен быть хотя бы один вопрос
- у вопроса должен быть `question_type_code`
- если `checking_mode = auto`, должны быть корректные `answers` или `options`
- диапазоны `grading_scale.ranges` не должны пересекаться
- коды критериев внутри одной рубрики должны быть уникальны

## Поведение импортера
- неизвестные дополнительные поля не должны ломать импорт
- импорт идет в черновик
- после импорта тест не публикуется автоматически
- ошибки импорта логируются

## Ограничения версии `v1.0`
- изображения и вложения пока не стандартизированы
- повторный импорт как новая версия не описан
- пользовательские формулы `custom_formula` лучше отложить до `v1.1+`
- структура пригодна для русского языка и базовых сценариев других предметов, но может потребовать расширений
