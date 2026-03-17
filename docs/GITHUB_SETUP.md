# GITHUB SETUP

## Текущее состояние
- локальный git настроен
- удаленный `origin`: `git@github.com:FreeJam/VPR.git`
- активная ветка: `main`
- SSH-подключение уже подтверждено
- основной рабочий сценарий: `commit -> push -> origin/main`

## Практический workflow
```powershell
git status
git add .
git commit -m "feat: describe change"
git push origin main
```

## Что уже важно для этого репозитория
- `.env` не коммитится
- локальные tool downloads в `.tools/` не коммитятся
- служебный `__laravel_bootstrap/` не коммитится
- SQLite dev database игнорируется через `database/.gitignore`

## Если нужно подключать новый компьютер
1. Клонировать репозиторий.
2. Настроить SSH key для GitHub.
3. Выполнить шаги из `LOCAL_DEVELOPMENT.md`.

## Практический итог
GitHub уже не является задачей настройки.
Это рабочий репозиторий проекта, готовый к дальнейшим коммитам и push.
