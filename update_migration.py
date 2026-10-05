import glob

migrations = glob.glob('/Users/user/Work/masjid-system/database/migrations/*_add_attachment_to_finance_transactions_table.php')
if migrations:
    migration_file = migrations[-1]
    with open(migration_file, 'r') as f:
        content = f.read()
    
    content = content.replace(
        'public function up(): void\n    {\n        Schema::table(\'finance_transactions\', function (Blueprint $table) {\n            //\n        });\n    }',
        'public function up(): void\n    {\n        Schema::table(\'finance_transactions\', function (Blueprint $table) {\n            $table->string(\'attachment\')->nullable()->after(\'notes\');\n        });\n    }'
    )
    content = content.replace(
        'public function down(): void\n    {\n        Schema::table(\'finance_transactions\', function (Blueprint $table) {\n            //\n        });\n    }',
        'public function down(): void\n    {\n        Schema::table(\'finance_transactions\', function (Blueprint $table) {\n            $table->dropColumn(\'attachment\');\n        });\n    }'
    )
    
    with open(migration_file, 'w') as f:
        f.write(content)
