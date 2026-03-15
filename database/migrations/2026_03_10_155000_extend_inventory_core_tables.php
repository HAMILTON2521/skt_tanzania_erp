<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'code')) {
                $table->string('code')->nullable()->unique();
            }

            if (! Schema::hasColumn('categories', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (! Schema::hasColumn('suppliers', 'supplier_code')) {
                $table->string('supplier_code')->nullable()->unique();
            }

            if (! Schema::hasColumn('suppliers', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable();
            }

            if (! Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (! Schema::hasColumn('suppliers', 'address')) {
                $table->text('address')->nullable();
            }

            if (! Schema::hasColumn('suppliers', 'status')) {
                $table->string('status')->default('active');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique();
            }

            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('products', 'reorder_level')) {
                $table->integer('reorder_level')->default(0);
            }

            if (! Schema::hasColumn('products', 'stock_on_hand')) {
                $table->integer('stock_on_hand')->default(0);
            }

            if (! Schema::hasColumn('products', 'status')) {
                $table->string('status')->default('active');
            }

            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_movements', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            }

            if (! Schema::hasColumn('stock_movements', 'movement_type')) {
                $table->string('movement_type')->default('in');
            }

            if (! Schema::hasColumn('stock_movements', 'quantity')) {
                $table->integer('quantity')->default(0);
            }

            if (! Schema::hasColumn('stock_movements', 'reference')) {
                $table->string('reference')->nullable();
            }

            if (! Schema::hasColumn('stock_movements', 'status')) {
                $table->string('status')->default('posted');
            }

            if (! Schema::hasColumn('stock_movements', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('stock_movements', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('stock_movements', 'reference')) {
                $table->dropColumn('reference');
            }

            if (Schema::hasColumn('stock_movements', 'quantity')) {
                $table->dropColumn('quantity');
            }

            if (Schema::hasColumn('stock_movements', 'movement_type')) {
                $table->dropColumn('movement_type');
            }

            if (Schema::hasColumn('stock_movements', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('products', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('products', 'stock_on_hand')) {
                $table->dropColumn('stock_on_hand');
            }

            if (Schema::hasColumn('products', 'reorder_level')) {
                $table->dropColumn('reorder_level');
            }

            if (Schema::hasColumn('products', 'unit_price')) {
                $table->dropColumn('unit_price');
            }

            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }

            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }

            if (Schema::hasColumn('products', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('products', 'sku')) {
                $table->dropUnique(['sku']);
                $table->dropColumn('sku');
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('suppliers', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('suppliers', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('suppliers', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('suppliers', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('suppliers', 'supplier_code')) {
                $table->dropUnique(['supplier_code']);
                $table->dropColumn('supplier_code');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('categories', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('categories', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('categories', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
