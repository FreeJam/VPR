# TESTING PLAN

## Фактически выполнено

### Unit
- `AssessmentImportValidatorTest`
- `AnswerCheckingServiceTest`

### Feature
- auth flow
- registration flow
- profile flow
- import happy path
- roles/access to imports
- assignment creation
- teacher students/groups flow
- group assignment flow
- student attempt flow
- teacher manual review flow

## Текущий подтвержденный результат
- `php artisan test` -> `35 passed`
- `125 assertions`

## Дополнительно проверено
- `php artisan migrate:fresh --seed`
- `php artisan route:list --except-vendor`
- `composer audit`
- `npm audit --omit=dev`
- `npm run build`

## Что еще желательно добавить позже
- feature tests для teacher group assignment
- feature tests для parent pages
- feature tests для admin CRUD
- unit tests для AttemptFlowService
- edge-case tests для deadlines и max attempts
- regression tests для duplicate imports и duplicate assignments
