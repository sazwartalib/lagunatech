<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in' }} · {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full">
        {{-- Brand panel --}}
        <div class="relative hidden w-1/2 flex-col justify-between bg-brand-700 p-12 text-white lg:flex">
            <div class="flex items-center gap-2 text-lg font-semibold">
                <img src="{{ asset('images/logo-icon.png') }}" alt="{{ config('app.name') }}" class="size-9 shrink-0">
                {{ config('app.name') }}
            </div>
            <div class="max-w-md space-y-4">
                <h1 class="text-3xl font-semibold leading-tight">The operating system for our studio.</h1>
                <p class="text-brand-100">Customers, quotations, projects, tasks, invoices and support — one place, always current.</p>
            </div>
            <p class="text-sm text-brand-200">© {{ date('Y') }} {{ config('app.name') }}</p>
        </div>

        {{-- Form panel --}}
        <div class="flex w-full flex-col justify-center px-6 py-12 sm:px-12 lg:w-1/2">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-8 flex items-center gap-2 text-lg font-semibold text-slate-900 lg:hidden">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="{{ config('app.name') }}" class="size-9 shrink-0">
                    {{ config('app.name') }}
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
