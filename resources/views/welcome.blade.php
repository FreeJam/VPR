<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'VPR') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.22),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(250,204,21,0.18),_transparent_30%)]"></div>
            <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-8">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.35em] text-sky-300">VPR Platform</p>
                        <h1 class="mt-3 text-4xl font-semibold text-white sm:text-5xl">Тренажер подготовки к ВПР для 5-11 классов</h1>
                    </div>

                    <div class="flex gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-200">
                                В кабинет
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:border-white/50 hover:bg-white/5">
                                Войти
                            </a>
                            <a href="{{ route('register') }}" class="rounded-full bg-sky-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-sky-300">
                                Регистрация
                            </a>
                        @endauth
                    </div>
                </header>

                <main class="mt-16 grid flex-1 gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
                        <p class="max-w-2xl text-lg leading-8 text-slate-200">
                            Платформа объединяет импорт вариантов ВПР из JSON, кабинеты учителя и ученика, гибкую rubric-проверку,
                            шкалы оценивания и основу для дальнейшей аналитики и назначений.
                        </p>

                        <div class="mt-10 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-900/80 p-5 ring-1 ring-white/10">
                                <p class="text-sm text-sky-300">Для учителя</p>
                                <h2 class="mt-2 text-xl font-semibold text-white">Импорт, проверка, контроль</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-300">Загружайте JSON-варианты, собирайте учеников в группы и управляйте контентом из одного кабинета.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900/80 p-5 ring-1 ring-white/10">
                                <p class="text-sm text-amber-300">Для ученика</p>
                                <h2 class="mt-2 text-xl font-semibold text-white">Тесты и прогресс</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-300">Решайте варианты, отслеживайте результаты и работайте с понятной структурой заданий.</p>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-4">
                        <div class="rounded-[2rem] bg-white p-8 text-slate-900 shadow-2xl shadow-sky-950/30">
                            <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Что уже готово</p>
                            <ul class="mt-6 space-y-4 text-sm leading-6 text-slate-600">
                                <li>Laravel 11 + Blade + Alpine + Tailwind</li>
                                <li>Роли `admin`, `teacher`, `student`, `parent`</li>
                                <li>Контентная модель ВПР и import pipeline `v1.0`</li>
                                <li>Базовые dashboard'ы и роли доступа</li>
                            </ul>
                        </div>
                        <div class="rounded-[2rem] border border-sky-300/20 bg-sky-400/10 p-8">
                            <p class="text-sm uppercase tracking-[0.3em] text-sky-200">Документация</p>
                            <p class="mt-4 text-sm leading-7 text-slate-200">
                                Архитектура, формат импорта и roadmap проекта живут в папке <code class="rounded bg-black/20 px-2 py-1 text-sky-100">docs/</code>.
                            </p>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
