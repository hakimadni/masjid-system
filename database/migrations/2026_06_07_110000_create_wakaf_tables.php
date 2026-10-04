<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wakaf_wakif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('display_publicly')->default(false);
            $table->timestamps();
        });

        Schema::create('wakaf_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wakaf_wakif_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('wakaf_type', ['uang', 'aset', 'fidiyah']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('asset_description')->nullable();
            $table->string('purpose')->nullable();
            $table->date('pledged_date')->nullable();
            $table->date('received_date')->nullable();
            $table->date('allocated_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->enum('status', ['pledged', 'received', 'allocated', 'completed', 'cancelled'])->default('pledged');
            $table->text('usage_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wakaf_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wakaf_record_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('usage_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wakaf_usage');
        Schema::dropIfExists('wakaf_records');
        Schema::dropIfExists('wakaf_wakif');
    }
};