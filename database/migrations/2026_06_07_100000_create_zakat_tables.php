<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zakat_muzakki', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->enum('zakat_type', ['fitrah', 'mal']);
            $table->enum('payment_form', ['uang', 'beras']);
            $table->decimal('money_amount', 15, 2)->default(0);
            $table->decimal('rice_amount', 10, 2)->default(0);
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->timestamps();
            $table->index(['mosque_id', 'zakat_type']);
            $table->index(['payment_date', 'status']);
        });

        Schema::create('zakat_mustahik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('category');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('zakat_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mustahik_id')->nullable()->constrained('zakat_mustahik')->nullOnDelete();
            $table->foreignId('muzakki_id')->nullable()->constrained('zakat_muzakki')->nullOnDelete();
            $table->enum('zakat_type', ['fitrah', 'mal']);
            $table->decimal('money_amount', 15, 2)->default(0);
            $table->decimal('rice_amount', 10, 2)->default(0);
            $table->date('distribution_date');
            $table->boolean('received')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zakat_distributions');
        Schema::dropIfExists('zakat_mustahik');
        Schema::dropIfExists('zakat_muzakki');
    }
};