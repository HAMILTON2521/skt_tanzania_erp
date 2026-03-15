<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('chart_of_accounts', 'code')) {
                $table->string('code')->nullable()->unique();
            }

            if (! Schema::hasColumn('chart_of_accounts', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('chart_of_accounts', 'type')) {
                $table->string('type')->nullable();
            }

            if (! Schema::hasColumn('chart_of_accounts', 'category')) {
                $table->string('category')->nullable();
            }

            if (! Schema::hasColumn('chart_of_accounts', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            }

            if (! Schema::hasColumn('chart_of_accounts', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (! Schema::hasColumn('chart_of_accounts', 'description')) {
                $table->text('description')->nullable();
            }
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_entries', 'reference')) {
                $table->string('reference')->nullable()->unique();
            }

            if (! Schema::hasColumn('journal_entries', 'entry_date')) {
                $table->date('entry_date')->nullable();
            }

            if (! Schema::hasColumn('journal_entries', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('journal_entries', 'debit')) {
                $table->decimal('debit', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('journal_entries', 'credit')) {
                $table->decimal('credit', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('journal_entries', 'status')) {
                $table->string('status')->default('draft');
            }

            if (! Schema::hasColumn('journal_entries', 'chart_of_account_id')) {
                $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            }

            if (! Schema::hasColumn('journal_entries', 'posted_by')) {
                $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entries', 'posted_by')) {
                $table->dropConstrainedForeignId('posted_by');
            }

            if (Schema::hasColumn('journal_entries', 'chart_of_account_id')) {
                $table->dropConstrainedForeignId('chart_of_account_id');
            }

            if (Schema::hasColumn('journal_entries', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('journal_entries', 'credit')) {
                $table->dropColumn('credit');
            }

            if (Schema::hasColumn('journal_entries', 'debit')) {
                $table->dropColumn('debit');
            }

            if (Schema::hasColumn('journal_entries', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('journal_entries', 'entry_date')) {
                $table->dropColumn('entry_date');
            }

            if (Schema::hasColumn('journal_entries', 'reference')) {
                $table->dropUnique(['reference']);
                $table->dropColumn('reference');
            }
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('chart_of_accounts', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('chart_of_accounts', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('chart_of_accounts', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            if (Schema::hasColumn('chart_of_accounts', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('chart_of_accounts', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('chart_of_accounts', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('chart_of_accounts', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
