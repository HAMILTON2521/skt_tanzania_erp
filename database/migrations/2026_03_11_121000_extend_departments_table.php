<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (! Schema::hasColumn('departments', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('departments', 'code')) {
                $table->string('code')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('departments', 'description')) {
                $table->text('description')->nullable()->after('code');
            }

            if (! Schema::hasColumn('departments', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->after('description')->constrained('employees')->nullOnDelete();
            }

            if (! Schema::hasColumn('departments', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('manager_id')->constrained('departments')->nullOnDelete();
            }

            if (! Schema::hasColumn('departments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('parent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('departments', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            if (Schema::hasColumn('departments', 'manager_id')) {
                $table->dropConstrainedForeignId('manager_id');
            }

            if (Schema::hasColumn('departments', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('departments', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }

            if (Schema::hasColumn('departments', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
