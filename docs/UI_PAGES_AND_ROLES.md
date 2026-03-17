# UI PAGES AND ROLES

## Уже реализовано

### Public
- `/`
- `/login`
- `/register`
- `/forgot-password`

### Admin
- `/admin`

### Teacher
- `/teacher`
- `/imports`
- `/imports/create`
- `/imports/{importBatch}`
- `/imports/{importBatch}/preview`
- `/assessments`
- `/assessments/{assessment}`
- `/assignments`
- `/assignments/create?version={id}`
- `/assignments/{assignment}`
- `/reviews`
- `/reviews/{review}`

### Student
- `/student`
- `/assignments`
- `/assignments/{assignment}`
- `/attempts/{attempt}`

### Parent
- `/parent`

### Common
- `/profile`
- `/dashboard`

## Частично реализовано
- parent role и link model есть, но отдельные results/progress pages пока не собраны
- teacher groups и group members есть на уровне БД, но UI еще не сделан

## Следующие UI-экраны
1. teacher students / requests / groups / codes
2. student results history
3. parent children progress
4. admin CRUD pages

## UI-принципы
- единый app layout
- понятные статусы и таблицы
- быстрая навигация по teacher workflow
- адаптивность без отдельного SPA
