<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_MOSQUE_SLUG = 'default-masjid';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mosques', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address')->nullable();
            $table->string('city_id')->nullable()->comment('MyQuran API city ID');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        DB::table('mosques')->updateOrInsert(
            ['slug' => self::DEFAULT_MOSQUE_SLUG],
            [
                'name' => 'Masjid Utama',
                'address' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mosques');
    }
};
