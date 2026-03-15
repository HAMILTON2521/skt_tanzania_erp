<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('bank_accounts', 'account_name')) {
                $table->string('account_name')->nullable();
            }

            if (! Schema::hasColumn('bank_accounts', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }

            if (! Schema::hasColumn('bank_accounts', 'account_number')) {
                $table->string('account_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('bank_accounts', 'currency')) {
                $table->string('currency', 10)->default('TZS');
            }

            if (! Schema::hasColumn('bank_accounts', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('tax_rates', function (Blueprint $table) {
            if (! Schema::hasColumn('tax_rates', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('tax_rates', 'code')) {
                $table->string('code')->nullable()->unique();
            }

            if (! Schema::hasColumn('tax_rates', 'rate')) {
                $table->decimal('rate', 5, 2)->default(0);
            }

            if (! Schema::hasColumn('tax_rates', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'tax_rate_id')) {
                $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->nullOnDelete();
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'bank_account_id')) {
                $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'bank_account_id')) {
                $table->dropConstrainedForeignId('bank_account_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'tax_rate_id')) {
                $table->dropConstrainedForeignId('tax_rate_id');
            }
        });

        Schema::table('tax_rates', function (Blueprint $table) {
            if (Schema::hasColumn('tax_rates', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('tax_rates', 'rate')) {
                $table->dropColumn('rate');
            }

            if (Schema::hasColumn('tax_rates', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }

            if (Schema::hasColumn('tax_rates', 'name')) {
                $table->dropColumn('name');
            }
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('bank_accounts', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('bank_accounts', 'currency')) {
                $table->dropColumn('currency');
            }

            if (Schema::hasColumn('bank_accounts', 'account_number')) {
                $table->dropUnique(['account_number']);
                $table->dropColumn('account_number');
            }

            if (Schema::hasColumn('bank_accounts', 'bank_name')) {
                $table->dropColumn('bank_name');
            }

            if (Schema::hasColumn('bank_accounts', 'account_name')) {
                $table->dropColumn('account_name');
            }
        });
    }
};
