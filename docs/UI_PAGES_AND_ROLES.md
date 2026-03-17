# UI PAGES AND ROLES

## Общий принцип
В проекте должны быть отдельные кабинеты по ролям, но с единым базовым layout и общей системой статусов, навигации и таблиц.

## Public
- `/` - главная страница проекта
- `/login` - вход
- `/register` - регистрация
- `/forgot-password` - восстановление пароля

## Admin
- `/admin` - dashboard
- `/admin/users` - пользователи
- `/admin/roles` - роли
- `/admin/grades` - классы
- `/admin/subjects` - предметы
- `/admin/assessments` - тесты
- `/admin/imports` - импорты
- `/admin/achievements` - достижения

## Teacher
- `/teacher` - dashboard
- `/teacher/profile` - профиль
- `/teacher/students` - мои ученики
- `/teacher/requests` - заявки на подключение
- `/teacher/groups` - группы
- `/teacher/codes` - коды подключения
- `/teacher/assessments` - мои тесты
- `/teacher/imports` - импорт
- `/teacher/assignments` - назначения
- `/teacher/reviews` - очередь проверки
- `/teacher/results` - результаты
- `/teacher/analytics` - аналитика

## Student
- `/student` - dashboard
- `/student/teacher-connect` - подключение к учителю
- `/student/assignments` - назначенные работы
- `/student/assessments` - каталог тестов
- `/student/attempts` - история попыток
- `/student/achievements` - достижения
- `/student/progress` - прогресс

## Parent
- `/parent` - dashboard
- `/parent/children` - мои дети
- `/parent/progress` - прогресс ребенка
- `/parent/results` - результаты ребенка

## Common resource pages
- `/assessments/{assessment}` - карточка теста
- `/attempts/{attempt}` - просмотр попытки
- `/attempts/{attempt}/take` - прохождение
- `/attempts/{attempt}/result` - результат
- `/imports/{importBatch}` - карточка импорта
- `/imports/{importBatch}/preview` - предпросмотр импорта

## Первые страницы для реализации
1. auth pages
2. dashboard redirect by role
3. `admin` dashboard
4. `teacher` dashboard
5. `student` dashboard
6. imports index/create/show/preview
7. assignments list
8. take attempt page
9. review queue

## UI-принципы
- крупные кликабельные элементы
- понятные бейджи статусов
- таблицы с фильтрами и пагинацией
- отдельный режим фокуса для прохождения теста
- отдельный режим фокуса для ручной проверки
