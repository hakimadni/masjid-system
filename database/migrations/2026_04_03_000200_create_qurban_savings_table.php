<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qurban_savings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->index();
            $table->decimal('target_amount', 14, 2);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->index();
            $table->boolean('eligible_kambing')->default(false)->index();
            $table->boolean('eligible_sapi_share')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qurban_savings');
    }
};
