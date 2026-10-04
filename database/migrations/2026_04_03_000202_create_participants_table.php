<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('qurban_saving_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 30)->index();
            $table->decimal('amount_due', 14, 2)->default(0);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('unpaid')->index();
            $table->unsignedTinyInteger('slot_number')->nullable();
            $table->timestamps();

            $table->unique(['animal_id', 'slot_number']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE participants ADD CONSTRAINT chk_participants_slot_number CHECK (slot_number IS NULL OR (slot_number BETWEEN 1 AND 7))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
