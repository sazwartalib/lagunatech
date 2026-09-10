<?php

use App\Support\ReferenceGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('references increment sequentially within a year', function () {
    $generator = new ReferenceGenerator;

    expect($generator->next('LT', 2026))->toBe('LT-2026-001')
        ->and($generator->next('LT', 2026))->toBe('LT-2026-002')
        ->and($generator->next('LT', 2026))->toBe('LT-2026-003');
});

test('each prefix and year keeps its own counter', function () {
    $generator = new ReferenceGenerator;

    $generator->next('LT', 2026);
    $generator->next('LT', 2026);

    expect($generator->next('QT', 2026))->toBe('QT-2026-001')
        ->and($generator->next('LT', 2027))->toBe('LT-2027-001')
        ->and($generator->next('LT', 2026))->toBe('LT-2026-003');
});
