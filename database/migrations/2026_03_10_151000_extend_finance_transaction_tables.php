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
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('invoices', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }

            if (! Schema::hasColumn('invoices', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }

            if (! Schema::hasColumn('invoices', 'issue_date')) {
                $table->date('issue_date')->nullable();
            }

            if (! Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable();
            }

            if (! Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('invoices', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('invoices', 'status')) {
                $table->string('status')->default('draft');
            }

            if (! Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'payment_number')) {
                $table->string('payment_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('payments', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'payment_date')) {
                $table->date('payment_date')->nullable();
            }

            if (! Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('payments', 'method')) {
                $table->string('method')->nullable();
            }

            if (! Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable();
            }

            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('pending');
            }

            if (! Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'expense_number')) {
                $table->string('expense_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('expenses', 'expense_date')) {
                $table->date('expense_date')->nullable();
            }

            if (! Schema::hasColumn('expenses', 'category')) {
                $table->string('category')->nullable();
            }

            if (! Schema::hasColumn('expenses', 'vendor_name')) {
                $table->string('vendor_name')->nullable();
            }

            if (! Schema::hasColumn('expenses', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('expenses', 'status')) {
                $table->string('status')->default('pending');
            }

            if (! Schema::hasColumn('expenses', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('expenses', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('expenses', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('expenses', 'vendor_name')) {
                $table->dropColumn('vendor_name');
            }

            if (Schema::hasColumn('expenses', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('expenses', 'expense_date')) {
                $table->dropColumn('expense_date');
            }

            if (Schema::hasColumn('expenses', 'expense_number')) {
                $table->dropUnique(['expense_number']);
                $table->dropColumn('expense_number');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('payments', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('payments', 'reference')) {
                $table->dropColumn('reference');
            }

            if (Schema::hasColumn('payments', 'method')) {
                $table->dropColumn('method');
            }

            if (Schema::hasColumn('payments', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('payments', 'payment_date')) {
                $table->dropColumn('payment_date');
            }

            if (Schema::hasColumn('payments', 'invoice_id')) {
                $table->dropConstrainedForeignId('invoice_id');
            }

            if (Schema::hasColumn('payments', 'payment_number')) {
                $table->dropUnique(['payment_number']);
                $table->dropColumn('payment_number');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('invoices', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('invoices', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('invoices', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }

            if (Schema::hasColumn('invoices', 'subtotal')) {
                $table->dropColumn('subtotal');
            }

            if (Schema::hasColumn('invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }

            if (Schema::hasColumn('invoices', 'issue_date')) {
                $table->dropColumn('issue_date');
            }

            if (Schema::hasColumn('invoices', 'customer_email')) {
                $table->dropColumn('customer_email');
            }

            if (Schema::hasColumn('invoices', 'customer_name')) {
                $table->dropColumn('customer_name');
            }

            if (Schema::hasColumn('invoices', 'invoice_number')) {
                $table->dropUnique(['invoice_number']);
                $table->dropColumn('invoice_number');
            }
        });
    }
};
