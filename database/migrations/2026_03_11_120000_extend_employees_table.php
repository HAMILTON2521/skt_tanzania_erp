<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'employee_code')) {
                $table->string('employee_code')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('employees', 'first_name')) {
                $table->string('first_name')->nullable()->after('employee_code');
            }

            if (! Schema::hasColumn('employees', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('employees', 'email')) {
                $table->string('email')->nullable()->unique()->after('last_name');
            }

            if (! Schema::hasColumn('employees', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('employees', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('address');
            }

            if (! Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('date_of_birth');
            }

            if (! Schema::hasColumn('employees', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('hire_date')->constrained('departments')->nullOnDelete();
            }

            if (! Schema::hasColumn('employees', 'position')) {
                $table->string('position')->nullable()->after('department_id');
            }

            if (! Schema::hasColumn('employees', 'salary')) {
                $table->decimal('salary', 15, 2)->default(0)->after('position');
            }

            if (! Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('salary');
            }

            if (! Schema::hasColumn('employees', 'bank_account')) {
                $table->string('bank_account')->nullable()->after('bank_name');
            }

            if (! Schema::hasColumn('employees', 'tin_number')) {
                $table->string('tin_number')->nullable()->after('bank_account');
            }

            if (! Schema::hasColumn('employees', 'nssf_number')) {
                $table->string('nssf_number')->nullable()->after('tin_number');
            }

            if (! Schema::hasColumn('employees', 'wcf_number')) {
                $table->string('wcf_number')->nullable()->after('nssf_number');
            }

            if (! Schema::hasColumn('employees', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('wcf_number');
            }

            if (! Schema::hasColumn('employees', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('emergency_contact');
            }

            if (! Schema::hasColumn('employees', 'status')) {
                $table->string('status')->default('active')->after('emergency_phone');
            }

            if (! Schema::hasColumn('employees', 'photo')) {
                $table->string('photo')->nullable()->after('status');
            }

            if (! Schema::hasColumn('employees', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('photo')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('employees', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('employees', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }

            if (Schema::hasColumn('employees', 'photo')) {
                $table->dropColumn('photo');
            }

            if (Schema::hasColumn('employees', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('employees', 'emergency_phone')) {
                $table->dropColumn('emergency_phone');
            }

            if (Schema::hasColumn('employees', 'emergency_contact')) {
                $table->dropColumn('emergency_contact');
            }

            if (Schema::hasColumn('employees', 'wcf_number')) {
                $table->dropColumn('wcf_number');
            }

            if (Schema::hasColumn('employees', 'nssf_number')) {
                $table->dropColumn('nssf_number');
            }

            if (Schema::hasColumn('employees', 'tin_number')) {
                $table->dropColumn('tin_number');
            }

            if (Schema::hasColumn('employees', 'bank_account')) {
                $table->dropColumn('bank_account');
            }

            if (Schema::hasColumn('employees', 'bank_name')) {
                $table->dropColumn('bank_name');
            }

            if (Schema::hasColumn('employees', 'salary')) {
                $table->dropColumn('salary');
            }

            if (Schema::hasColumn('employees', 'position')) {
                $table->dropColumn('position');
            }

            if (Schema::hasColumn('employees', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }

            if (Schema::hasColumn('employees', 'hire_date')) {
                $table->dropColumn('hire_date');
            }

            if (Schema::hasColumn('employees', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }

            if (Schema::hasColumn('employees', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('employees', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('employees', 'email')) {
                $table->dropUnique(['email']);
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('employees', 'last_name')) {
                $table->dropColumn('last_name');
            }

            if (Schema::hasColumn('employees', 'first_name')) {
                $table->dropColumn('first_name');
            }

            if (Schema::hasColumn('employees', 'employee_code')) {
                $table->dropUnique(['employee_code']);
                $table->dropColumn('employee_code');
            }
        });
    }
};
