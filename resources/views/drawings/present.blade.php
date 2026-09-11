<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $drawing->title }} · Present · {{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-slate-900">
    <div class="flex h-full flex-col">
        <div class="flex items-center gap-3 bg-slate-900 px-4 py-2 text-slate-300">
            <a href="{{ url()->previous(route('drawings.index')) }}" class="text-sm hover:text-white">✕ Exit</a>
            <span class="truncate text-sm font-medium text-white">{{ $drawing->title }}</span>

            <div class="ml-auto flex items-center gap-2 text-sm">
                @if ($previous)
                    <a href="{{ route('drawings.present', $previous) }}" class="hover:text-white">← Previous</a>
                @endif
                @if ($next)
                    <a href="{{ route('drawings.present', $next) }}" class="hover:text-white">Next →</a>
                @endif
            </div>
        </div>

        <div class="flex-1 overflow-hidden">
            <x-drawing.editor :drawing="$drawing" :can-edit="false" :can-export="false" :chrome="false" :fullscreen="true" />
        </div>
    </div>
</body>
</html>
