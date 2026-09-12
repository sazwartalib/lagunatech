<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drawings no longer require a parent project.
     */
    public function up(): void
    {
        Schema::table('drawings', function (Blueprint $table): void {
            $table->string('drawable_type')->nullable()->change();
            $table->unsignedBigInteger('drawable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('drawings', function (Blueprint $table): void {
            $table->string('drawable_type')->nullable(false)->change();
            $table->unsignedBigInteger('drawable_id')->nullable(false)->change();
        });
    }
};
