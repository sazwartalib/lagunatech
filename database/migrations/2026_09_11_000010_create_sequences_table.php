<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backs the reference-number generator (LT-2026-001, QT-2026-001, ...).
     * One row per prefix + period; incremented under a row lock.
     */
    public function up(): void
    {
        Schema::create('sequences', function (Blueprint $table): void {
            $table->id();
            $table->string('prefix');       // LT, QT, INV, CR, CUST
            $table->string('period');       // usually the year: 2026
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();

            $table->unique(['prefix', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
