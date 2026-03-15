<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'po_number')) {
                $table->string('po_number')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('purchase_orders', 'order_date')) {
                $table->date('order_date')->nullable()->after('po_number');
            }

            if (! Schema::hasColumn('purchase_orders', 'purchase_request_id')) {
                $table->foreignId('purchase_request_id')->nullable()->after('order_date')->constrained('purchase_requests')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('purchase_request_id')->constrained('suppliers')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchase_orders', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable()->after('supplier_id');
            }

            if (! Schema::hasColumn('purchase_orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('expected_delivery_date');
            }

            if (! Schema::hasColumn('purchase_orders', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('shipping_address');
            }

            if (! Schema::hasColumn('purchase_orders', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal');
            }

            if (! Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('tax_amount');
            }

            if (! Schema::hasColumn('purchase_orders', 'status')) {
                $table->string('status')->default('draft')->after('total_amount');
            }

            if (! Schema::hasColumn('purchase_orders', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('purchase_orders', 'notes')) {
                $table->text('notes')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('purchase_orders', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('purchase_orders', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('purchase_orders', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }

            if (Schema::hasColumn('purchase_orders', 'subtotal')) {
                $table->dropColumn('subtotal');
            }

            if (Schema::hasColumn('purchase_orders', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }

            if (Schema::hasColumn('purchase_orders', 'expected_delivery_date')) {
                $table->dropColumn('expected_delivery_date');
            }

            if (Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }

            if (Schema::hasColumn('purchase_orders', 'purchase_request_id')) {
                $table->dropConstrainedForeignId('purchase_request_id');
            }

            if (Schema::hasColumn('purchase_orders', 'order_date')) {
                $table->dropColumn('order_date');
            }

            if (Schema::hasColumn('purchase_orders', 'po_number')) {
                $table->dropUnique(['po_number']);
                $table->dropColumn('po_number');
            }
        });
    }
};
