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
        Schema::create('slaughterings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete()->unique();
            $table->date('date')->index();
            $table->string('location');
            $table->dateTime('cut_time')->nullable();
            $table->decimal('meat_total_kg', 8, 2)->nullable();
            $table->enum('distribution_status', ['pending', 'in_progress', 'completed'])->default('pending')->index();
            $table->timestamps();
        });

        Schema::create('slaughtering_volunteer', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('slaughtering_id')->constrained()->cascadeOnDelete();
            $table->foreignId('volunteer_id')->constrained()->cascadeOnDelete();
            $table->enum('team_role', ['administrasi', 'penyembelih', 'distribusi', 'dokumentasi']);
            $table->timestamps();
            $table->unique(['slaughtering_id', 'volunteer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slaughtering_volunteer');
        Schema::dropIfExists('slaughterings');
    }
};
