<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drawings', function (Blueprint $table): void {
            $table->id();
            $table->morphs('drawable');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('canvas_data')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drawings');
    }
};
