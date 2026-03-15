<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code')->nullable()->unique();
            }

            if (! Schema::hasColumn('customers', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable();
            }

            if (! Schema::hasColumn('customers', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (! Schema::hasColumn('customers', 'address')) {
                $table->text('address')->nullable();
            }

            if (! Schema::hasColumn('customers', 'status')) {
                $table->string('status')->default('active');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (! Schema::hasColumn('quotations', 'quotation_number')) {
                $table->string('quotation_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('quotations', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            }

            if (! Schema::hasColumn('quotations', 'issue_date')) {
                $table->date('issue_date')->nullable();
            }

            if (! Schema::hasColumn('quotations', 'valid_until')) {
                $table->date('valid_until')->nullable();
            }

            if (! Schema::hasColumn('quotations', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('quotations', 'status')) {
                $table->string('status')->default('draft');
            }

            if (! Schema::hasColumn('quotations', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_invoices', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('sales_invoices', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            }

            if (! Schema::hasColumn('sales_invoices', 'quotation_id')) {
                $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            }

            if (! Schema::hasColumn('sales_invoices', 'issue_date')) {
                $table->date('issue_date')->nullable();
            }

            if (! Schema::hasColumn('sales_invoices', 'due_date')) {
                $table->date('due_date')->nullable();
            }

            if (! Schema::hasColumn('sales_invoices', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('sales_invoices', 'status')) {
                $table->string('status')->default('draft');
            }

            if (! Schema::hasColumn('sales_invoices', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('sales_invoices', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('sales_invoices', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('sales_invoices', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('sales_invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }

            if (Schema::hasColumn('sales_invoices', 'issue_date')) {
                $table->dropColumn('issue_date');
            }

            if (Schema::hasColumn('sales_invoices', 'quotation_id')) {
                $table->dropConstrainedForeignId('quotation_id');
            }

            if (Schema::hasColumn('sales_invoices', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }

            if (Schema::hasColumn('sales_invoices', 'invoice_number')) {
                $table->dropUnique(['invoice_number']);
                $table->dropColumn('invoice_number');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('quotations', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('quotations', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('quotations', 'valid_until')) {
                $table->dropColumn('valid_until');
            }

            if (Schema::hasColumn('quotations', 'issue_date')) {
                $table->dropColumn('issue_date');
            }

            if (Schema::hasColumn('quotations', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }

            if (Schema::hasColumn('quotations', 'quotation_number')) {
                $table->dropUnique(['quotation_number']);
                $table->dropColumn('quotation_number');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('customers', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('customers', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('customers', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('customers', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('customers', 'customer_code')) {
                $table->dropUnique(['customer_code']);
                $table->dropColumn('customer_code');
            }
        });
    }
};
