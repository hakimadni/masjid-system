<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('distributions', function (Blueprint $table): void {
            $table->foreignId('slaughtering_id')->nullable()->constrained()->nullOnDelete()->after('animal_id');
        });
    }

    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('slaughtering_id');
        });
    }
};
