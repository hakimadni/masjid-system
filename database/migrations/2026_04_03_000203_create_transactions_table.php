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
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->morphs('transactionable');
            $table->enum('type', ['topup', 'withdrawal', 'payment', 'refund'])->index();
            $table->decimal('amount', 14, 2);
            $table->string('reference_no')->nullable()->index();
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->nullable()->index();
            $table->string('gateway_provider')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
