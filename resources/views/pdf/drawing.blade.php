<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 24px; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        p { font-size: 11px; color: #64748b; margin: 0 0 16px; }
        img { max-width: 100%; }
    </style>
</head>
<body>
    <h1>{{ $drawing->title }}</h1>
    <p>{{ config('app.name') }} &middot; exported {{ now()->format('d M Y, H:i') }}</p>
    <img src="{{ $image }}" alt="{{ $drawing->title }}">
</body>
</html>
