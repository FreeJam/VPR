# SEEDERS AND ENUMS

## Seeders первой очереди

### RolesSeeder
- `admin`
- `teacher`
- `student`
- `parent`

### GradeLevelsSeeder
- `5`
- `6`
- `7`
- `8`
- `9`
- `10`
- `11`

### SubjectsSeeder
- `ru` - Русский язык
- `math` - Математика
- `bio` - Биология
- `hist` - История
- `geo` - География
- `soc` - Обществознание
- `phys` - Физика
- `chem` - Химия

### QuestionTypesSeeder
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

## Рекомендуемые enum-значения

### Role codes
- `admin`
- `teacher`
- `student`
- `parent`

### Assessment kind
- `vpr_official_style`
- `trainer`
- `teacher_custom`

### Assessment status
- `draft`
- `published`
- `archived`

### Import status
- `uploaded`
- `validated`
- `imported`
- `failed`

### Assignment mode
- `training`
- `homework`
- `exam`

### Assignment status
- `draft`
- `scheduled`
- `active`
- `closed`
- `archived`

### Attempt status
- `not_started`
- `in_progress`
- `submitted`
- `waiting_review`
- `reviewed`
- `finalized`

### Checking mode
- `auto`
- `hybrid`
- `manual_open`
- `manual_rubric`

### Teacher-student link status
- `pending`
- `approved`
- `rejected`
- `archived`

### Notification type
- `assignment_created`
- `assignment_due_soon`
- `attempt_reviewed`
- `teacher_request_pending`
- `import_failed`

## Правила
- для кодов использовать lowercase snake_case
- код не должен зависеть от локализации интерфейса
- текстовые названия и описания хранятся отдельно от кода
