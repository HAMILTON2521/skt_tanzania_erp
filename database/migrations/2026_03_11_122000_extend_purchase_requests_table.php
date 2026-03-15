<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_requests', 'pr_number')) {
                $table->string('pr_number')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('purchase_requests', 'request_date')) {
                $table->date('request_date')->nullable()->after('pr_number');
            }

            if (! Schema::hasColumn('purchase_requests', 'requester_id')) {
                $table->foreignId('requester_id')->nullable()->after('request_date')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchase_requests', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('requester_id')->constrained('departments')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchase_requests', 'description')) {
                $table->text('description')->nullable()->after('department_id');
            }

            if (! Schema::hasColumn('purchase_requests', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('description');
            }

            if (! Schema::hasColumn('purchase_requests', 'status')) {
                $table->string('status')->default('draft')->after('total_amount');
            }

            if (! Schema::hasColumn('purchase_requests', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('purchase_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('purchase_requests', 'notes')) {
                $table->text('notes')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_requests', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('purchase_requests', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('purchase_requests', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('purchase_requests', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('purchase_requests', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('purchase_requests', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('purchase_requests', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }

            if (Schema::hasColumn('purchase_requests', 'requester_id')) {
                $table->dropConstrainedForeignId('requester_id');
            }

            if (Schema::hasColumn('purchase_requests', 'request_date')) {
                $table->dropColumn('request_date');
            }

            if (Schema::hasColumn('purchase_requests', 'pr_number')) {
                $table->dropUnique(['pr_number']);
                $table->dropColumn('pr_number');
            }
        });
    }
};
