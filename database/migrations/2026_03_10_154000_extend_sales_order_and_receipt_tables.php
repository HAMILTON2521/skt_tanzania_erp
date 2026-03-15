<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_orders', 'order_number')) {
                $table->string('order_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('sales_orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            }

            if (! Schema::hasColumn('sales_orders', 'quotation_id')) {
                $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            }

            if (! Schema::hasColumn('sales_orders', 'order_date')) {
                $table->date('order_date')->nullable();
            }

            if (! Schema::hasColumn('sales_orders', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable();
            }

            if (! Schema::hasColumn('sales_orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('sales_orders', 'status')) {
                $table->string('status')->default('draft');
            }

            if (! Schema::hasColumn('sales_orders', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('receipts', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('receipts', 'sales_invoice_id')) {
                $table->foreignId('sales_invoice_id')->nullable()->constrained('sales_invoices')->nullOnDelete();
            }

            if (! Schema::hasColumn('receipts', 'receipt_date')) {
                $table->date('receipt_date')->nullable();
            }

            if (! Schema::hasColumn('receipts', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('receipts', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (! Schema::hasColumn('receipts', 'status')) {
                $table->string('status')->default('received');
            }

            if (! Schema::hasColumn('receipts', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('receipts', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('receipts', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('receipts', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('receipts', 'receipt_date')) {
                $table->dropColumn('receipt_date');
            }

            if (Schema::hasColumn('receipts', 'sales_invoice_id')) {
                $table->dropConstrainedForeignId('sales_invoice_id');
            }

            if (Schema::hasColumn('receipts', 'receipt_number')) {
                $table->dropUnique(['receipt_number']);
                $table->dropColumn('receipt_number');
            }
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sales_orders', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('sales_orders', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('sales_orders', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('sales_orders', 'expected_delivery_date')) {
                $table->dropColumn('expected_delivery_date');
            }

            if (Schema::hasColumn('sales_orders', 'order_date')) {
                $table->dropColumn('order_date');
            }

            if (Schema::hasColumn('sales_orders', 'quotation_id')) {
                $table->dropConstrainedForeignId('quotation_id');
            }

            if (Schema::hasColumn('sales_orders', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }

            if (Schema::hasColumn('sales_orders', 'order_number')) {
                $table->dropUnique(['order_number']);
                $table->dropColumn('order_number');
            }
        });
    }
};
