<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            // Internal notes are never exposed to the future customer portal.
            $table->boolean('is_internal')->default(true);
            $table->timestamps();

            $table->index(['project_id', 'is_internal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_notes');
    }
};
