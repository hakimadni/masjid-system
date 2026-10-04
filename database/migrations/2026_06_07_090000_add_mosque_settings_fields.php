<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mosques', function (Blueprint $table): void {
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->text('description')->nullable()->after('email');
            $table->string('logo_url')->nullable()->after('description');
            $table->json('settings')->nullable()->after('logo_url');
        });
    }

    public function down(): void
    {
        Schema::table('mosques', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'email', 'description', 'logo_url', 'settings']);
        });
    }
};