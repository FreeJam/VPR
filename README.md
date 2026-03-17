# VPR

Платформа подготовки к ВПР на Laravel 11 с ролями `admin`, `teacher`, `student`, `parent`, импортом тестов из JSON, назначениями, попытками и ручной проверкой.

## Что уже работает
- аутентификация и регистрация на Laravel Breeze
- роли и ролевой редирект по кабинетам
- импорт JSON `v1.0`: upload -> validation -> preview -> import
- каталог тестов и карточка теста
- назначения учитель -> ученик
- прохождение попытки учеником
- автопроверка объективных вопросов
- ручная проверка rubric/open заданий
- итоговый пересчет баллов и отметки

## Локально проверено
- Laravel `11.49.0`
- Laravel Breeze `2.4.1`
- SQLite для локальной разработки
- `artisan test`: `33 passed`
- `artisan migrate:fresh --seed`: проходит
- `composer audit`: уязвимости не найдены
- `npm audit --omit=dev`: уязвимости не найдены
- `npm run build`: проходит

## Быстрый старт
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

## Демо-аккаунты
- `admin@vpr.local` / `password`
- `teacher@vpr.local` / `password`
- `student@vpr.local` / `password`
- `parent@vpr.local` / `password`

## Ручной MVP-сценарий
1. Войти как `teacher@vpr.local`.
2. Открыть `Импорт` и загрузить `docs/IMPORT_EXAMPLE_RU_6_V1_K1.json`.
3. Выполнить import и открыть карточку теста.
4. Нажать `Назначить` и выдать работу ученику.
5. Войти как `student@vpr.local`, открыть `Назначения`, пройти попытку и отправить работу.
6. Вернуться под учителем в `Проверка`, выставить баллы и сохранить review.

## Документация
- рабочая база знаний проекта: `docs/README.md`
- локальный запуск и команды: `docs/LOCAL_DEVELOPMENT.md`
- текущее состояние workspace: `docs/WORKSPACE_STATUS.md`
