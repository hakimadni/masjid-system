<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('donations', function (Blueprint $table): void {
            $table->foreignId('donation_category_id')->nullable()->after('donor_id')->constrained('donation_categories')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false)->after('campaign')->index();
        });

        Schema::create('jamaahs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mosque_id')->constrained('mosques')->restrictOnDelete();
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable()->index();
            $table->string('family_role')->nullable();
            $table->enum('status', ['active', 'inactive', 'deceased', 'moved'])->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaahs');

        Schema::table('donations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('donation_category_id');
            $table->dropColumn('is_anonymous');
        });

        Schema::dropIfExists('donation_categories');
    }
};
