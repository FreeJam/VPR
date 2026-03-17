# GITHUB SETUP

## Текущее состояние
- локально `git` установлен
- папка проекта уже является git-репозиторием
- `gh` CLI не установлен
- ветка: `main`
- стартовый коммит уже создан
- `origin` уже добавлен: `git@github.com:FreeJam/VPR.git`
- локальный `git user.email`: `Artik.lbt@gmail.com`
- локальный SSH-ключ уже создан: `C:\Users\Artik\.ssh\id_ed25519_freejam_github`

## Можно ли подключить проект к GitHub
Да.
Локальный git уже подготовлен, поэтому теперь нужно:
1. убедиться, что репозиторий `FreeJam/VPR` существует на GitHub
2. добавить публичный SSH-ключ в GitHub
3. выполнить `push`

## Вариант 1. Через сайт GitHub

### Шаги
1. Создать пустой репозиторий на GitHub без `README`, `.gitignore` и license.
2. В корне проекта выполнить:

```powershell
git remote add origin https://github.com/<owner>/<repo>.git
git push -u origin main
```

## Вариант 2. Через SSH
Если SSH уже настроен, вместо HTTPS использовать:

```powershell
git remote add origin git@github.com:<owner>/<repo>.git
git push -u origin main
```

Для этого workspace уже подготовлено:
- `origin = git@github.com:FreeJam/VPR.git`
- локальный ключ: `C:\Users\Artik\.ssh\id_ed25519_freejam_github`
- публичный ключ нужно добавить в GitHub account settings

## Вариант 3. Через GitHub CLI
Сейчас `gh` не установлен.
Если позже установить его, можно будет создать репозиторий прямо из терминала.

Пример:

```powershell
gh repo create <repo-name> --private --source . --remote origin --push
```

## Рекомендуемый workflow для этого проекта
- держать `main` в рабочем состоянии
- каждый крупный этап делать отдельным коммитом
- сначала обновлять `docs`, потом код
- для крупных изменений использовать feature-ветки

## Что стоит сделать перед первым push
- добавить `.gitignore` для Laravel
- при появлении `.env` не коммитить секреты
- договориться, будет ли репозиторий публичным или приватным

## Безопасный стартовый порядок
1. `git init`
2. первый commit только с `docs`
3. создание репозитория на GitHub
4. подключение `origin`
5. push ветки `main`

## Что потребуется от пользователя для полного подключения
- URL GitHub-репозитория
- и один из способов аутентификации:
  - Personal Access Token для HTTPS
  - или добавленный SSH-ключ

Одного email недостаточно для реального `push` в GitHub.
Без URL репозитория и способа аутентификации я не смогу привязать настоящий удаленный `origin`.
