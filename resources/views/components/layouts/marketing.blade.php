@php($settings = app(\App\Support\Settings::class))
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Laguna Tech — Custom Software & Digital Solutions' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Laguna Tech builds custom software, web applications and digital solutions that help businesses simplify operations, automate processes and grow.' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'Laguna Tech — Custom Software & Digital Solutions' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Custom software, web and mobile development for businesses that want to move forward.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="theme-color" content="#05070A">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-marketing overflow-x-hidden bg-[#05070A] text-white antialiased selection:bg-cyan-400/30 selection:text-white" x-data="{ mobileNav: false }">

    {{-- ============ Sticky nav ============ --}}
    <header data-marketing-nav
            class="fixed inset-x-0 top-0 z-50 transition-colors duration-300 [&.is-scrolled]:border-b [&.is-scrolled]:border-white/10 [&.is-scrolled]:bg-[#05070A]/80 [&.is-scrolled]:backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/logo-icon.png') }}" alt="{{ $settings->get('company.name') }}" class="size-8" loading="eager">
                <span class="hidden text-[15px] font-bold tracking-tight sm:inline">Laguna <span class="font-medium text-white/50">Tech</span></span>
            </a>

            <nav class="hidden items-center gap-1 rounded-full border border-white/10 bg-white/[0.03] px-1.5 py-1.5 text-sm text-white/70 backdrop-blur lg:flex">
                @foreach ([
                    '#home' => 'Home', '#services' => 'Services', '#solutions' => 'Solutions',
                    '#process' => 'Process', '#projects' => 'Projects', '#about' => 'About',
                ] as $href => $label)
                    <a href="{{ $href }}" class="rounded-full px-4 py-1.5 font-medium transition hover:bg-white/[0.06] hover:text-white">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('portal.login') }}" class="hidden text-sm font-medium text-white/60 transition hover:text-white sm:block">
                    Client Portal
                </a>
                <a href="#contact" class="group inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#05070A] transition hover:bg-cyan-300">
                    Start a Project
                    <svg class="size-3.5 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>

                <button type="button" @click="mobileNav = true" class="ml-1 grid size-9 place-items-center rounded-full border border-white/10 text-white lg:hidden" aria-label="Open menu">
                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Mobile nav overlay --}}
    <div x-show="mobileNav" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-[#05070A]/98 backdrop-blur-xl lg:hidden">
        <div class="flex h-16 items-center justify-between px-5">
            <img src="{{ asset('images/logo-icon.png') }}" alt="{{ $settings->get('company.name') }}" class="size-8">
            <button type="button" @click="mobileNav = false" class="grid size-9 place-items-center rounded-full border border-white/10 text-white" aria-label="Close menu">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <nav class="mt-8 flex flex-col gap-1 px-5 text-2xl font-semibold">
            @foreach ([
                '#home' => 'Home', '#services' => 'Services', '#solutions' => 'Solutions',
                '#process' => 'Process', '#projects' => 'Projects', '#about' => 'About',
            ] as $href => $label)
                <a href="{{ $href }}" @click="mobileNav = false" class="border-b border-white/5 py-4 text-white/80 transition hover:text-white">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="mt-8 flex flex-col gap-3 px-5">
            <a href="#contact" @click="mobileNav = false" class="rounded-full bg-white px-5 py-3 text-center text-sm font-semibold text-[#05070A]">Start a Project</a>
            <a href="{{ route('portal.login') }}" class="rounded-full border border-white/10 px-5 py-3 text-center text-sm font-medium text-white/70">Client Portal</a>
        </div>
    </div>

    <main>
        {{ $slot }}
    </main>

    {{-- ============ Footer ============ --}}
    <footer class="border-t border-white/10 bg-[#05070A]">
        <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
                <div>
                    <img src="{{ asset('images/logo-full-dark.png') }}" alt="{{ $settings->get('company.name') }}" class="h-8 w-auto" loading="lazy">
                    <p class="mt-4 max-w-xs text-sm leading-relaxed text-white/45">
                        Building digital solutions for businesses that want to move forward.
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        @foreach ([
                            'TikTok' => '#', 'Facebook' => '#', 'Instagram' => '#', 'LinkedIn' => '#',
                        ] as $network => $href)
                            <a href="{{ $href }}" aria-label="{{ $network }} (coming soon)"
                               class="grid size-9 place-items-center rounded-full border border-white/10 text-white/50 transition hover:border-white/25 hover:text-white">
                                <x-marketing.social-icon :name="$network" class="size-4" />
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/40">Company</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="#about" class="transition hover:text-white">About</a></li>
                        <li><a href="#process" class="transition hover:text-white">Process</a></li>
                        <li><a href="#projects" class="transition hover:text-white">Projects</a></li>
                        <li><a href="#contact" class="transition hover:text-white">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/40">Services</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="#services" class="transition hover:text-white">Custom Software</a></li>
                        <li><a href="#services" class="transition hover:text-white">Web Applications</a></li>
                        <li><a href="#services" class="transition hover:text-white">Mobile Applications</a></li>
                        <li><a href="#solutions" class="transition hover:text-white">Solutions</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/40">Get in touch</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        @if ($settings->get('company.email'))<li><a href="mailto:{{ $settings->get('company.email') }}" class="transition hover:text-white">{{ $settings->get('company.email') }}</a></li>@endif
                        @if ($settings->get('company.phone'))<li>{{ $settings->get('company.phone') }}</li>@endif
                        <li class="pt-1"><a href="{{ route('login') }}" class="text-white/35 transition hover:text-white/70">Staff Login</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-16 flex flex-col gap-4 border-t border-white/10 pt-8 text-xs text-white/35 sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} {{ $settings->get('company.name') }}. All rights reserved.</p>
                @if ($settings->get('company.registration_no'))<p>Reg. No. {{ $settings->get('company.registration_no') }}</p>@endif
            </div>
        </div>
    </footer>

    @include('partials.toasts')
    @livewireScripts
</body>
</html>
