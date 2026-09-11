@props(['name'])

@php
    // Minimal Lucide-style line icons, hand-drawn as inline SVG paths — no icon package dependency.
    $paths = [
        'code' => 'm9 18-6-6 6-6M15 6l6 6-6 6',
        'globe' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 0 1 0 18 14 14 0 0 1 0-18Z',
        'smartphone' => 'M7 3h10a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1ZM11 18h2',
        'layout' => 'M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5ZM3 10h18M9 21V10',
        'zap' => 'm13 3-9 11h6l-1 7 9-11h-6l1-7Z',
        'sparkles' => 'm12 3 1.7 4.3L18 9l-4.3 1.7L12 15l-1.7-4.3L6 9l4.3-1.7L12 3ZM5 17l.8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17ZM19 15l.6 1.5L21 17l-1.4.6L19 19l-.6-1.4L17 17l1.4-.5L19 15Z',
        'search' => 'M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM21 21l-4.3-4.3',
        'compass' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM15 9l-2 6-2-2-2 2 2-6 2 2 2-2Z',
        'pen' => 'M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z',
        'rocket' => 'M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09ZM12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 19 2c0 2.52-.74 6.5-4 9a22.35 22.35 0 0 1-3 2ZM9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5',
        'check' => 'm5 12 5 5 9-9',
        'arrow-right' => 'M5 12h14m-6-6 6 6-6 6',
        'arrow-up-right' => 'M7 17 17 7M7 7h10v10',
        'mail' => 'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1ZM3.5 6l8.5 7 8.5-7',
        'phone' => 'M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11 11 0 0 0 3.5.55 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11 11 0 0 0 .55 3.5 1 1 0 0 1-.25 1L6.6 10.8Z',
        'workflow' => 'M4 4h6v6H4zM14 14h6v6h-6zM10 7h4a2 2 0 0 1 2 2v4M10 17H8a2 2 0 0 1-2-2v-4',
        'layers' => 'm12 3 9 5-9 5-9-5 9-5ZM3 13l9 5 9-5M3 8l9 5 9-5',
        'shield' => 'M12 3l8 3v6c0 4.5-3.4 8.2-8 9-4.6-.8-8-4.5-8-9V6l8-3Z',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'size-5', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.75', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round']) }} aria-hidden="true">
    <path d="{{ $paths[$name] ?? '' }}" />
</svg>
