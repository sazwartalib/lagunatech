@php
    $settings = app(\App\Support\Settings::class);
    $company = $settings->get('company.name');
@endphp

<x-layouts.marketing
    title="Laguna Tech — Custom Software & Digital Solutions"
    metaDescription="Laguna Tech builds custom software, web applications and digital solutions that help businesses simplify operations, automate processes and grow.">

    {{-- ================================================================ --}}
    {{-- HERO                                                              --}}
    {{-- ================================================================ --}}
    <section id="home" class="relative isolate overflow-hidden pt-40 pb-28 sm:pt-48 sm:pb-36">
        {{-- Background: grid + drifting glow + abstract circuitry --}}
        <div class="pointer-events-none absolute inset-0 -z-10 bg-grid-dark bg-radial-fade opacity-60"></div>
        <div class="pointer-events-none absolute -top-24 left-1/2 -z-10 h-[36rem] w-[36rem] -translate-x-[60%] rounded-full bg-cyan-500/20 blur-[120px] animate-drift-slow"></div>
        <div class="pointer-events-none absolute top-1/3 right-0 -z-10 h-[30rem] w-[30rem] translate-x-[30%] rounded-full bg-blue-600/20 blur-[120px] animate-drift-slow-reverse"></div>

        <svg class="pointer-events-none absolute right-0 top-24 -z-10 hidden h-[34rem] w-[34rem] opacity-70 lg:block" viewBox="0 0 400 400" fill="none" aria-hidden="true">
            <g stroke="url(#hero-line)" stroke-width="1">
                <path class="animate-dash" d="M40 320 L150 220 L150 120 L280 60" />
                <path class="animate-dash" style="animation-delay:-2s" d="M40 120 L130 160 L230 160 L320 260" />
                <path class="animate-dash" style="animation-delay:-4s" d="M60 60 L160 90 L230 260 L350 300" />
            </g>
            <g fill="#22D3EE">
                <circle cx="40" cy="320" r="3.5" class="animate-pulse"></circle>
                <circle cx="150" cy="220" r="3"></circle>
                <circle cx="280" cy="60" r="3.5" class="animate-pulse" style="animation-delay:.6s"></circle>
                <circle cx="320" cy="260" r="3" class="animate-pulse" style="animation-delay:1.1s"></circle>
                <circle cx="230" cy="160" r="3"></circle>
                <circle cx="350" cy="300" r="3.5" class="animate-pulse" style="animation-delay:.3s"></circle>
            </g>
            <defs>
                <linearGradient id="hero-line" x1="0" y1="0" x2="400" y2="400" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#22D3EE" stop-opacity="0"/>
                    <stop offset="0.5" stop-color="#22D3EE" stop-opacity="0.7"/>
                    <stop offset="1" stop-color="#22D3EE" stop-opacity="0"/>
                </linearGradient>
            </defs>
        </svg>

        <div class="mx-auto max-w-5xl px-5 text-center sm:px-8">
            <div data-reveal class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-4 py-1.5 text-xs font-medium text-white/60">
                <span class="size-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                Custom software studio, based in Malaysia
            </div>

            <h1 data-reveal data-reveal-delay="80"
                class="mt-8 text-[2.6rem] font-extrabold leading-[1.05] tracking-tight sm:text-6xl lg:text-[5.5rem]">
                We build software that
                <span class="relative inline-block px-1">
                    <span class="relative z-10 bg-gradient-to-b from-white to-white/70 bg-clip-text text-transparent">moves your business</span>
                </span>
                forward.
            </h1>

            <p data-reveal data-reveal-delay="160" class="mx-auto mt-7 max-w-2xl text-balance text-lg leading-relaxed text-white/55 sm:text-xl">
                From idea to deployment, we design and develop powerful digital solutions that simplify
                operations, automate processes and help businesses grow.
            </p>

            <div data-reveal data-reveal-delay="240" class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="#contact" class="group inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-[#05070A] transition hover:bg-cyan-300 sm:w-auto">
                    Start a Project
                    <svg class="size-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
                <a href="#solutions" class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/15 px-7 py-3.5 text-sm font-semibold text-white/80 transition hover:border-white/30 hover:text-white sm:w-auto">
                    Explore Our Solutions
                </a>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- TRUST STRIP                                                       --}}
    {{-- ================================================================ --}}
    <section class="border-y border-white/10 bg-white/[0.015] py-10" aria-label="Who we build for">
        <div data-reveal class="mx-auto max-w-6xl px-5 sm:px-8">
            <p class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-white/35">
                Built for businesses that want to move forward
            </p>
            <ul class="mt-6 flex flex-wrap items-center justify-center gap-x-10 gap-y-3 text-center text-base font-semibold text-white/45 sm:text-lg">
                @foreach (['SMEs', 'Startups', 'Retail', 'Service Businesses', 'Enterprise', 'Custom Operations'] as $segment)
                    <li class="transition hover:text-white/80">{{ $segment }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- SERVICES — expandable editorial list                             --}}
    {{-- ================================================================ --}}
    <section id="services" class="py-28 sm:py-36" aria-labelledby="services-heading">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-end">
                <h2 id="services-heading" data-reveal class="text-4xl font-bold tracking-tight sm:text-5xl">
                    Software built<br>around your business.
                </h2>
                <p data-reveal data-reveal-delay="80" class="text-lg leading-relaxed text-white/50">
                    Every business works differently. We build software around your workflow,
                    not the other way around.
                </p>
            </div>

            <div data-reveal data-reveal-delay="120"
                 x-data="{ open: 0 }" class="mt-16 divide-y divide-white/10 border-y border-white/10">
                @foreach ([
                    ['code', 'Custom Software', 'Tailored systems designed around your exact business requirements — from internal tools to full operating platforms.'],
                    ['globe', 'Web Applications', 'Fast, scalable and intuitive web applications accessible anywhere, built to feel effortless for your team and customers.'],
                    ['smartphone', 'Mobile Applications', 'Modern mobile experiences built for Android and iOS, designed to extend your product into your customers\' pockets.'],
                    ['layout', 'Business Management Systems', 'Centralize operations, customers, projects, sales and internal workflows in one connected system.'],
                    ['zap', 'Automation', 'Reduce repetitive work and automate business processes so your team can focus on what matters.'],
                    ['sparkles', 'Digital Transformation', 'Turn manual, paper-based or spreadsheet workflows into structured, reliable digital systems.'],
                ] as $i => [$icon, $title, $desc])
                    <div class="group" wire:key="service-{{ $i }}">
                        <button type="button" @click="open = open === {{ $i }} ? -1 : {{ $i }}"
                                class="flex w-full items-center gap-5 py-7 text-left transition sm:gap-8">
                            <span class="font-mono text-sm text-white/25" :class="open === {{ $i }} && 'text-cyan-400'">0{{ $i + 1 }}</span>
                            <span class="flex flex-1 items-center gap-4">
                                <span class="grid size-11 shrink-0 place-items-center rounded-xl border border-white/10 bg-white/[0.03] text-white/70 transition duration-300"
                                      :class="open === {{ $i }} && 'border-cyan-400/30 bg-cyan-400/10 text-cyan-300'">
                                    <x-marketing.icon :name="$icon" class="size-5" />
                                </span>
                                <span class="text-xl font-semibold text-white/80 transition sm:text-2xl" :class="open === {{ $i }} && 'text-white'">{{ $title }}</span>
                            </span>
                            <svg class="size-5 shrink-0 text-white/30 transition duration-300" :class="open === {{ $i }} && 'rotate-45 text-cyan-300'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <p class="max-w-2xl pb-8 pl-[3.75rem] text-base leading-relaxed text-white/50 sm:pl-24">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- APPROACH / CAPABILITY                                             --}}
    {{-- ================================================================ --}}
    <section class="border-t border-white/10 bg-white/[0.015] py-28 sm:py-36">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <h2 data-reveal class="max-w-2xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl">
                We don't just build software.<br>We solve problems.
            </h2>

            <div class="relative mt-16 grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="pointer-events-none absolute top-6 hidden h-px w-full bg-gradient-to-r from-transparent via-white/15 to-transparent lg:block"></div>
                @foreach ([
                    ['search', '01', 'Understand', 'We study how your business actually works before writing a single line of code.'],
                    ['pen', '02', 'Design', 'We design workflows and interfaces around your users, not the database.'],
                    ['code', '03', 'Build', 'We develop reliable, scalable software with clean, maintainable engineering.'],
                    ['rocket', '04', 'Improve', 'We continuously improve your system as your business grows and changes.'],
                ] as $i => [$icon, $num, $title, $desc])
                    <div data-reveal data-reveal-delay="{{ $i * 90 }}" class="relative">
                        <div class="relative grid size-12 place-items-center rounded-full border border-white/15 bg-[#05070A] text-cyan-300">
                            <x-marketing.icon :name="$icon" class="size-5" />
                        </div>
                        <p class="mt-5 font-mono text-xs text-white/30">{{ $num }}</p>
                        <h3 class="mt-1 text-xl font-semibold">{{ $title }}</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-white/50">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- SOLUTIONS                                                         --}}
    {{-- ================================================================ --}}
    <section id="solutions" class="py-28 sm:py-36">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-end">
                <h2 data-reveal class="text-4xl font-bold tracking-tight sm:text-5xl">
                    From simple systems<br>to complex platforms.
                </h2>
                <p data-reveal data-reveal-delay="80" class="text-lg leading-relaxed text-white/50">
                    Whatever shape your operations take, there's a system for it. Here's a sample
                    of what we design and build.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ([
                    'Management Systems', 'Booking Platforms', 'E-Commerce', 'CRM',
                    'ERP', 'POS Systems', 'Inventory Systems', 'HR Systems',
                    'Customer Portals', 'Payment Platforms', 'Internal Tools', 'Industry-specific Platforms',
                ] as $i => $solution)
                    <div data-reveal data-reveal-delay="{{ ($i % 4) * 60 }}"
                         class="group relative flex min-h-32 flex-col justify-between bg-[#05070A] p-5 transition duration-300 hover:bg-white/[0.035] sm:p-6">
                        <span class="font-mono text-xs text-white/25 transition group-hover:text-cyan-400">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="flex items-end justify-between gap-2">
                            <span class="text-sm font-semibold leading-snug text-white/80 transition group-hover:text-white">{{ $solution }}</span>
                            <svg class="size-4 shrink-0 -translate-x-1 text-cyan-400 opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M7 7h10v10"/></svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- PROJECT SHOWCASE                                                  --}}
    {{-- ================================================================ --}}
    <section id="projects" class="border-t border-white/10 bg-white/[0.015] py-28 sm:py-36">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <h2 data-reveal class="text-4xl font-bold tracking-tight sm:text-5xl">What we're building.</h2>

            <div class="mt-14 grid grid-cols-1 gap-5 lg:grid-cols-3 lg:grid-rows-2">
                {{-- Featured project --}}
                <article data-reveal class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] transition duration-300 hover:border-white/20 lg:col-span-2 lg:row-span-2">
                    <div class="relative flex h-56 items-center justify-center overflow-hidden bg-gradient-to-br from-rose-500/10 via-fuchsia-500/5 to-[#05070A] sm:h-72">
                        <x-marketing.mockup tone="rose" />
                    </div>
                    <div class="flex flex-1 flex-col p-7 sm:p-9">
                        <span class="text-xs font-semibold uppercase tracking-wider text-rose-300/70">Wedding &amp; Vendor Management</span>
                        <h3 class="mt-3 text-2xl font-bold sm:text-3xl">Neekah</h3>
                        <p class="mt-3 max-w-md text-white/50">
                            A wedding planning and vendor management platform that connects couples with
                            vendors and keeps every booking, timeline and payment organised in one place.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            @foreach (['Booking Platform', 'Vendor Management', 'Web App'] as $tag)
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-white/50">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </article>

                {{-- ERMS --}}
                <article data-reveal data-reveal-delay="100" class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] transition duration-300 hover:border-white/20">
                    <div class="relative flex h-40 items-center justify-center overflow-hidden bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-[#05070A]">
                        <x-marketing.mockup tone="amber" />
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <span class="text-xs font-semibold uppercase tracking-wider text-amber-300/70">Repair Operations</span>
                        <h3 class="mt-2 text-xl font-bold">ERMS</h3>
                        <p class="mt-2 text-sm text-white/50">
                            Electronic Repair Management System for tracking repair workflows, technicians and electronic parts inventory.
                        </p>
                    </div>
                </article>

                {{-- Business Management Platform --}}
                <article data-reveal data-reveal-delay="180" class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] transition duration-300 hover:border-white/20">
                    <div class="relative flex h-40 items-center justify-center overflow-hidden bg-gradient-to-br from-cyan-500/10 via-blue-500/5 to-[#05070A]">
                        <x-marketing.mockup tone="cyan" />
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <span class="text-xs font-semibold uppercase tracking-wider text-cyan-300/70">Internal Operations</span>
                        <h3 class="mt-2 text-xl font-bold">Business Management Platform</h3>
                        <p class="mt-2 text-sm text-white/50">
                            A custom platform for managing projects, customers, invoices and internal operations — the very system running this site.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- PROCESS                                                           --}}
    {{-- ================================================================ --}}
    <section id="process" class="py-28 sm:py-36">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <h2 data-reveal class="text-4xl font-bold tracking-tight sm:text-5xl">From idea to launch.</h2>

            <ol class="relative mt-16 grid grid-cols-1 gap-x-6 gap-y-12 sm:grid-cols-5">
                <div class="pointer-events-none absolute top-6 hidden h-px w-full bg-gradient-to-r from-transparent via-white/15 to-transparent sm:block"></div>
                @foreach ([
                    ['search', 'Discover', 'Understand the business and requirements.'],
                    ['compass', 'Plan', 'Define the architecture, workflow and roadmap.'],
                    ['pen', 'Design', 'Create intuitive, considered UI/UX.'],
                    ['code', 'Develop', 'Build and test the platform end to end.'],
                    ['rocket', 'Launch', 'Deploy, monitor and continuously improve.'],
                ] as $i => [$icon, $title, $desc])
                    <li data-reveal data-reveal-delay="{{ $i * 80 }}" class="relative pl-0">
                        <div class="relative z-10 grid size-12 place-items-center rounded-full border border-white/15 bg-[#05070A] text-white/70">
                            <x-marketing.icon :name="$icon" class="size-5" />
                        </div>
                        <p class="mt-5 text-sm font-semibold text-cyan-300/80">Step {{ $i + 1 }}</p>
                        <h3 class="mt-1 text-lg font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-white/45">{{ $desc }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- WHY LAGUNA TECH — editorial                                       --}}
    {{-- ================================================================ --}}
    <section id="about" class="border-t border-white/10 bg-white/[0.015] py-28 sm:py-36">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <div class="grid grid-cols-1 gap-16 lg:grid-cols-2">
                <div data-reveal>
                    <h2 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl">
                        Technology should make business simpler.
                    </h2>
                    <p class="mt-6 max-w-md text-lg leading-relaxed text-white/50">
                        We've seen too many businesses fight their own software. {{ $company }} exists to
                        flip that — systems that fit how you actually work, built by a team that
                        stays on for the long run.
                    </p>
                </div>

                <ul class="space-y-0 divide-y divide-white/10 border-t border-white/10">
                    @foreach ([
                        ['Built around your workflow', 'Not a rigid template — software shaped by how your team already operates.'],
                        ['Modern technology', 'A reliable, current stack chosen for longevity, not trends.'],
                        ['User-friendly interfaces', 'Software your team enjoys using, with no manual required.'],
                        ['Scalable architecture', 'Built to grow with your business, not be rebuilt in two years.'],
                        ['Transparent development', 'Clear scope, clear pricing, and visibility into progress throughout.'],
                        ['Long-term support', 'We stay on after launch with maintenance and continuous improvement.'],
                    ] as $i => [$title, $desc])
                        <li data-reveal data-reveal-delay="{{ $i * 60 }}" class="flex gap-4 py-5">
                            <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-cyan-400/10 text-cyan-300">
                                <x-marketing.icon name="check" class="size-3.5" />
                            </span>
                            <div>
                                <p class="font-semibold text-white/90">{{ $title }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-white/45">{{ $desc }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- CTA                                                               --}}
    {{-- ================================================================ --}}
    <section class="relative isolate overflow-hidden py-28 sm:py-32">
        <div class="pointer-events-none absolute left-1/2 top-1/2 -z-10 h-[40rem] w-[40rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan-500/15 blur-[130px]"></div>
        <div data-reveal class="mx-auto max-w-3xl px-5 text-center sm:px-8">
            <h2 class="text-4xl font-bold leading-tight tracking-tight sm:text-6xl">
                Have an idea?<br>Let's build it.
            </h2>
            <p class="mx-auto mt-6 max-w-lg text-lg leading-relaxed text-white/55">
                Tell us what you're trying to solve. We'll help turn your idea into a working digital solution.
            </p>
            <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="#contact" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-[#05070A] transition hover:bg-cyan-300 sm:w-auto">
                    Start a Project
                </a>
                <a href="mailto:{{ $settings->get('company.email') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/15 px-7 py-3.5 text-sm font-semibold text-white/80 transition hover:border-white/30 hover:text-white sm:w-auto">
                    Talk to Us
                </a>
            </div>
        </div>
    </section>

    {{-- ================================================================ --}}
    {{-- CONTACT                                                           --}}
    {{-- ================================================================ --}}
    <section id="contact" class="border-t border-white/10 py-28 sm:py-36">
        <div class="mx-auto max-w-6xl px-5 sm:px-8">
            <div class="grid grid-cols-1 gap-14 lg:grid-cols-[0.85fr_1.15fr]">
                <div data-reveal>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-400/80">Get in touch</span>
                    <h2 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Tell us about your project.</h2>
                    <p class="mt-5 max-w-sm text-white/50">
                        Share a few details and we'll get back to you within one business day —
                        no obligation, just an honest conversation about what's possible.
                    </p>

                    <ul class="mt-10 space-y-5 text-sm">
                        @if ($settings->get('company.email'))
                            <li class="flex items-center gap-3 text-white/70">
                                <span class="grid size-9 place-items-center rounded-full border border-white/10 text-cyan-300"><x-marketing.icon name="mail" class="size-4" /></span>
                                <a href="mailto:{{ $settings->get('company.email') }}" class="transition hover:text-white">{{ $settings->get('company.email') }}</a>
                            </li>
                        @endif
                        @if ($settings->get('company.phone'))
                            <li class="flex items-center gap-3 text-white/70">
                                <span class="grid size-9 place-items-center rounded-full border border-white/10 text-cyan-300"><x-marketing.icon name="phone" class="size-4" /></span>
                                {{ $settings->get('company.phone') }}
                            </li>
                        @endif
                    </ul>
                </div>

                <div data-reveal data-reveal-delay="120">
                    <livewire:marketing.lead-form />
                </div>
            </div>
        </div>
    </section>
</x-layouts.marketing>
