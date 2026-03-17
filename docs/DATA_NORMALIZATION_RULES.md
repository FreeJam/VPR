# DATA NORMALIZATION RULES

## Цель
Зафиксировать правила хранения кодов, slug, JSON-метаданных и импортируемых значений, чтобы код и база не разъехались по стилю.

## Codes
- все системные коды хранить в lowercase snake_case
- `subjects.code`: `ru`, `math`, `bio` и т.д.
- `question_types.code`: только из зафиксированного справочника
- `roles.code`: `admin`, `teacher`, `student`, `parent`

## Slug and labels
- пользовательские названия могут быть на русском
- slug и codes должны быть ASCII
- slug строить из стабильного имени и при необходимости суффикса версии

## HTML and text
- тексты заданий хранить в `prompt_html` и `instruction_html`
- пользовательские ответы хранить как текст или JSON в зависимости от типа вопроса
- не смешивать raw text и rendered HTML в одном поле

## JSON metadata
- все редко используемые и расширяемые поля складывать в `meta_json`
- `meta_json` не заменяет явные колонки для часто фильтруемых данных
- при чтении meta-полей использовать DTO или accessors

## Import normalization
- trim всех строковых полей
- нормализация пустых строк в `null`, где это уместно
- `grade_code` и `subject_code` приводить к ожидаемому формату до поиска справочников
- для поиска дублей использовать `payload_hash`

## Scoring normalization
- `auto_score`, `manual_score`, `final_score` хранить отдельно
- итог не должен зависеть от порядка применения ручной проверки
- диапазоны шкал не должны пересекаться

## Auditability
- сохранять `created_by` и `updated_by`, где это важно
- импортируемый исходник должен быть связан с `content_sources`
- критичные переходы статусов логировать в `activity_logs`
