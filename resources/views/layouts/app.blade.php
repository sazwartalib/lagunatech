<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' · ' : '' }}{{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full">
<div
    x-data="{
        collapsed: JSON.parse(localStorage.getItem('lt.sidebar.collapsed') ?? 'false'),
        mobileOpen: false,
        toggle() { this.collapsed = !this.collapsed; localStorage.setItem('lt.sidebar.collapsed', JSON.stringify(this.collapsed)); },
    }"
    class="flex h-full"
    @keydown.window.escape="mobileOpen = false"
>
    {{-- ============ Sidebar ============ --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 flex flex-col border-r border-slate-200 bg-white transition-all duration-200 lg:static"
        :class="[
            collapsed ? 'lg:w-[68px]' : 'lg:w-64',
            mobileOpen ? 'w-64 translate-x-0' : '-translate-x-full lg:translate-x-0',
        ]"
    >
        <div class="flex h-14 items-center gap-2 border-b border-slate-200 px-4">
            <img src="{{ asset('images/logo-icon.png') }}" alt="{{ config('app.name') }}" class="size-8 shrink-0" x-show="collapsed" x-cloak>
            <img src="{{ asset('images/logo-full.png') }}" alt="{{ config('app.name') }}" class="h-7 w-auto" x-show="!collapsed" x-cloak>
        </div>

        <nav class="scrollbar-slim flex-1 space-y-6 overflow-y-auto px-3 py-4" x-data="{ collapsed }">
            @foreach (config('navigation.groups') as $group)
                <div>
                    <p class="px-2 pb-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400"
                       x-show="!collapsed" x-cloak>{{ $group['label'] }}</p>
                    <ul class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            @php
                                $isReady = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route']);
                                $active = $isReady && request()->routeIs($item['active'] ?? $item['route']);
                            @endphp
                            <li>
                                <a
                                    @if ($isReady) href="{{ route($item['route']) }}" wire:navigate @else href="#" @endif
                                    @class([
                                        'group flex items-center gap-3 rounded-lg px-2 py-2 text-sm font-medium transition',
                                        'bg-brand-50 text-brand-700' => $active,
                                        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $active && $isReady,
                                        'cursor-default text-slate-300' => ! $isReady,
                                    ])
                                    @unless ($isReady) aria-disabled="true" tabindex="-1" @endunless
                                    :title="collapsed ? '{{ $item['label'] }}' : null"
                                >
                                    <span class="grid size-5 shrink-0 place-items-center text-base">{{ $item['icon'] }}</span>
                                    <span class="truncate" x-show="!collapsed" x-cloak>{{ $item['label'] }}</span>
                                    @unless ($isReady)
                                        <span class="ml-auto rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400"
                                              x-show="!collapsed" x-cloak>Soon</span>
                                    @endunless
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <button
            type="button"
            @click="toggle()"
            class="hidden items-center gap-2 border-t border-slate-200 px-4 py-3 text-xs font-medium text-slate-400 hover:text-slate-700 lg:flex"
        >
            <span x-text="collapsed ? '»' : '«'"></span>
            <span x-show="!collapsed" x-cloak>Collapse</span>
        </button>
    </aside>

    {{-- Mobile backdrop --}}
    <div x-show="mobileOpen" x-cloak @click="mobileOpen = false"
         class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>

    {{-- ============ Main ============ --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-14 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6">
            <button type="button" class="text-slate-500 lg:hidden" @click="mobileOpen = true" aria-label="Open menu">
                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-sm font-semibold text-slate-900">{{ $header ?? ($title ?? 'Dashboard') }}</h1>
            </div>

            <livewire:global-search />

            <div class="flex items-center gap-1">
                @include('partials.quick-add')
                <livewire:notification-center />
                @include('partials.user-menu')
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    @include('partials.toasts')
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
