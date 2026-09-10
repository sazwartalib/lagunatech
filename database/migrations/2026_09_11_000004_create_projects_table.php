<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique(); // LT-2026-001
            $table->string('name');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('users')->nullOnDelete(); // internal PIC
            $table->string('customer_pic_name')->nullable();
            $table->string('customer_pic_phone')->nullable();
            $table->string('type')->nullable();       // Web, Mobile App, Custom System, Service
            $table->string('technology')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('lead');
            $table->string('priority')->default('medium');
            $table->unsignedTinyInteger('progress')->default(0); // 0-100, manual override / rollup
            $table->date('start_date')->nullable();
            $table->date('target_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('value', 12, 2)->nullable(); // contract / project value
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('priority');
            $table->index(['status', 'target_end_date']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
