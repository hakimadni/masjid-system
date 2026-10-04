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
        Schema::create('distributions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('animal_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->string('recipient_name');
            $table->enum('recipient_type', ['mustahik', 'penerima'])->index();
            $table->unsignedInteger('package_count');
            $table->enum('status', ['pending', 'delivered'])->default('pending')->index();
            $table->dateTime('delivered_at')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
