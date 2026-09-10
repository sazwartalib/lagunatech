<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->string('billing_cycle')->default('monthly');
            $table->decimal('fee', 12, 2)->default(0);
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->date('next_renewal_on')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('next_renewal_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
