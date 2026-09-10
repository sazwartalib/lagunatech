<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // staff member
            $table->string('type')->default('whatsapp');
            $table->dateTime('communicated_at');
            $table->text('summary');
            $table->text('action_required')->nullable();
            $table->date('follow_up_on')->nullable();
            $table->boolean('follow_up_done')->default(false);
            $table->timestamps();

            $table->index(['customer_id', 'communicated_at']);
            $table->index('follow_up_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
