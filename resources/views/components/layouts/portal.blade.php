<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' · ' : '' }}{{ config('app.name') }} Portal</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-50">
@php($me = auth('customer')->user())

<div class="min-h-full">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-14 max-w-5xl items-center gap-4 px-4 sm:px-6">
            <a href="{{ route('portal.dashboard') }}" wire:navigate class="flex items-center gap-2 font-semibold text-slate-900">
                <img src="{{ asset('images/logo-icon.png') }}" alt="{{ config('app.name') }}" class="size-8 shrink-0 sm:hidden">
                <img src="{{ asset('images/logo-full.png') }}" alt="{{ config('app.name') }}" class="hidden h-7 w-auto sm:block">
            </a>

            <nav class="ml-2 flex items-center gap-1 text-sm">
                @foreach ([
                    'portal.dashboard' => 'Home',
                    'portal.projects' => 'Projects',
                    'portal.quotations' => 'Quotations',
                    'portal.invoices' => 'Invoices',
                    'portal.tickets' => 'Support',
                ] as $route => $label)
                    <a href="{{ route($route) }}" wire:navigate
                       @class([
                           'rounded-lg px-2.5 py-1.5 font-medium transition',
                           'bg-brand-50 text-brand-700' => request()->routeIs($route.'*'),
                           'text-slate-600 hover:bg-slate-100' => ! request()->routeIs($route.'*'),
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            <div x-data="{ open: false }" @click.outside="open = false" class="relative ml-auto">
                <button @click="open = !open" class="flex items-center gap-2 rounded-lg p-1 hover:bg-slate-100">
                    <x-ui.avatar :name="$me->name" size="sm" />
                    <span class="hidden text-sm font-medium text-slate-700 sm:inline">{{ $me->customer->company_name }}</span>
                </button>
                <div x-show="open" x-cloak x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                    <div class="border-b border-slate-100 px-3 py-2">
                        <p class="truncate text-sm font-medium text-slate-900">{{ $me->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $me->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('portal.logout') }}" class="pt-1">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100">Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        {{ $slot }}
    </main>
</div>

@include('partials.toasts')
@livewireScripts
</body>
</html>
