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
        Schema::create('animals', function (Blueprint $table): void {
            $table->id();
            $table->enum('type', ['sapi', 'kambing'])->index();
            $table->decimal('weight', 8, 2);
            $table->decimal('price', 14, 2);
            $table->string('supplier');
            $table->string('location');
            $table->enum('slaughter_type', ['onsite', 'penjagalan'])->default('onsite')->index();
            $table->string('vendor')->nullable();
            $table->decimal('vendor_cost', 14, 2)->nullable();
            $table->dateTime('pickup_schedule')->nullable();
            $table->enum('external_status', ['sent', 'processed', 'received'])->nullable()->index();
            $table->enum('status', ['available', 'assigned', 'slaughtered', 'distributed'])->default('available')->index();
            $table->uuid('qr_token')->unique();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
