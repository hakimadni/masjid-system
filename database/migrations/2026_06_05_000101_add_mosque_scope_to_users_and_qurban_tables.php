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
        $defaultMosqueId = DB::table('mosques')
            ->where('slug', self::DEFAULT_MOSQUE_SLUG)
            ->value('id');

        if (! $defaultMosqueId) {
            throw new RuntimeException('Default mosque record is required before mosque scoping can be applied.');
        }

        $this->addMosqueId('users', $defaultMosqueId, 'id');
        $this->addMosqueId('qurban_savings', $defaultMosqueId, 'user_id');
        $this->addMosqueId('animals', $defaultMosqueId, 'id');
        $this->addMosqueId('participants', $defaultMosqueId, 'animal_id');
        $this->addMosqueId('transactions', $defaultMosqueId, 'created_by');
        $this->addMosqueId('volunteers', $defaultMosqueId, 'user_id');
        $this->addMosqueId('slaughterings', $defaultMosqueId, 'animal_id');
        $this->addMosqueId('distributions', $defaultMosqueId, 'animal_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropMosqueId('distributions');
        $this->dropMosqueId('slaughterings');
        $this->dropMosqueId('volunteers');
        $this->dropMosqueId('transactions');
        $this->dropMosqueId('participants');
        $this->dropMosqueId('animals');
        $this->dropMosqueId('qurban_savings');
        $this->dropMosqueId('users');
    }

    private function addMosqueId(string $tableName, int $defaultMosqueId, string $afterColumn): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($defaultMosqueId, $afterColumn): void {
            $table->foreignId('mosque_id')
                ->after($afterColumn)
                ->default($defaultMosqueId)
                ->constrained('mosques')
                ->restrictOnDelete();
        });

        DB::table($tableName)
            ->whereNull('mosque_id')
            ->update(['mosque_id' => $defaultMosqueId]);
    }

    private function dropMosqueId(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table): void {
            $table->dropConstrainedForeignId('mosque_id');
        });
    }
};
