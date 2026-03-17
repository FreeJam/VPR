# LOCAL DEVELOPMENT

## Проверенное локальное окружение
- Laravel `11.49.0`
- Laravel Breeze `2.4.1`
- PHP `8.3.x`
- Node `20.x`
- npm `10.x`
- SQLite для локальной разработки

## Первый запуск
```powershell
composer install
npm install
Copy-Item .env.example .env
New-Item -ItemType File database/database.sqlite
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

## Полезные команды
```powershell
php artisan test
php artisan migrate:fresh --seed
php artisan route:list --except-vendor
composer audit
npm audit --omit=dev
npm run build
```

## Демо-аккаунты
- `admin@vpr.local` / `password`
- `teacher@vpr.local` / `password`
- `student@vpr.local` / `password`
- `parent@vpr.local` / `password`

## Проверка основного сценария
1. Войти как `teacher@vpr.local`.
2. Загрузить `docs/IMPORT_EXAMPLE_RU_6_V1_K1.json` через `Импорт`.
3. Выполнить import и открыть созданный тест.
4. Нажать `Назначить` и выбрать ученика `student@vpr.local`.
5. Войти как `student@vpr.local`, открыть `Назначения`, пройти попытку и отправить работу.
6. Снова войти как учитель и открыть `Проверка`.
7. Выставить rubric-баллы и сохранить review.

## Замечания
- PHPUnit использует in-memory SQLite, чтобы тесты не портили локальный `database/database.sqlite`.
- Для production в документации по-прежнему целевой стек `MySQL 8`, но на дату `2026-03-18` реально прогонялся и проверялся именно SQLite dev setup.
