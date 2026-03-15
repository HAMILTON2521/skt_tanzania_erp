<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('attendances', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('id')->constrained('employees')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('attendances', 'date')) {
                $table->date('date')->nullable()->after('employee_id');
            }

            if (! Schema::hasColumn('attendances', 'check_in')) {
                $table->time('check_in')->nullable()->after('date');
            }

            if (! Schema::hasColumn('attendances', 'check_out')) {
                $table->time('check_out')->nullable()->after('check_in');
            }

            if (! Schema::hasColumn('attendances', 'hours_worked')) {
                $table->decimal('hours_worked', 5, 2)->nullable()->after('check_out');
            }

            if (! Schema::hasColumn('attendances', 'status')) {
                $table->string('status')->default('present')->after('hours_worked');
            }

            if (! Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });

        if (! $this->hasAttendanceUniqueIndex()) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(['employee_id', 'date'], 'attendances_employee_id_date_unique');
            });
        }

        Schema::table('leaves', function (Blueprint $table) {
            if (! Schema::hasColumn('leaves', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('id')->constrained('employees')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('leaves', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->nullable()->after('employee_id')->constrained('leave_types');
            }

            if (! Schema::hasColumn('leaves', 'start_date')) {
                $table->date('start_date')->nullable()->after('leave_type_id');
            }

            if (! Schema::hasColumn('leaves', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (! Schema::hasColumn('leaves', 'days_requested')) {
                $table->integer('days_requested')->default(0)->after('end_date');
            }

            if (! Schema::hasColumn('leaves', 'reason')) {
                $table->text('reason')->nullable()->after('days_requested');
            }

            if (! Schema::hasColumn('leaves', 'status')) {
                $table->string('status')->default('pending')->after('reason');
            }

            if (! Schema::hasColumn('leaves', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('leaves', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (! Schema::hasColumn('leaves', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (! Schema::hasColumn('payrolls', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('id')->constrained('employees')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('payrolls', 'payroll_period')) {
                $table->string('payroll_period')->nullable()->after('employee_id');
            }

            if (! Schema::hasColumn('payrolls', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('payroll_period');
            }

            if (! Schema::hasColumn('payrolls', 'basic_salary')) {
                $table->decimal('basic_salary', 15, 2)->default(0)->after('payment_date');
            }

            if (! Schema::hasColumn('payrolls', 'allowances')) {
                $table->decimal('allowances', 15, 2)->default(0)->after('basic_salary');
            }

            if (! Schema::hasColumn('payrolls', 'overtime')) {
                $table->decimal('overtime', 15, 2)->default(0)->after('allowances');
            }

            if (! Schema::hasColumn('payrolls', 'bonus')) {
                $table->decimal('bonus', 15, 2)->default(0)->after('overtime');
            }

            if (! Schema::hasColumn('payrolls', 'gross_pay')) {
                $table->decimal('gross_pay', 15, 2)->default(0)->after('bonus');
            }

            if (! Schema::hasColumn('payrolls', 'paye')) {
                $table->decimal('paye', 15, 2)->default(0)->after('gross_pay');
            }

            if (! Schema::hasColumn('payrolls', 'nssf')) {
                $table->decimal('nssf', 15, 2)->default(0)->after('paye');
            }

            if (! Schema::hasColumn('payrolls', 'wcf')) {
                $table->decimal('wcf', 15, 2)->default(0)->after('nssf');
            }

            if (! Schema::hasColumn('payrolls', 'other_deductions')) {
                $table->decimal('other_deductions', 15, 2)->default(0)->after('wcf');
            }

            if (! Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->decimal('total_deductions', 15, 2)->default(0)->after('other_deductions');
            }

            if (! Schema::hasColumn('payrolls', 'net_pay')) {
                $table->decimal('net_pay', 15, 2)->default(0)->after('total_deductions');
            }

            if (! Schema::hasColumn('payrolls', 'status')) {
                $table->string('status')->default('draft')->after('net_pay');
            }

            if (! Schema::hasColumn('payrolls', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payrolls', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by');
            }
        });

        if (! $this->hasPayrollUniqueIndex()) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->unique(['employee_id', 'payroll_period'], 'payrolls_employee_id_payroll_period_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->hasPayrollUniqueIndex()) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropUnique('payrolls_employee_id_payroll_period_unique');
            });
        }

        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            if (Schema::hasColumn('payrolls', 'processed_by')) {
                $table->dropConstrainedForeignId('processed_by');
            }
            if (Schema::hasColumn('payrolls', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('payrolls', 'net_pay')) {
                $table->dropColumn('net_pay');
            }
            if (Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->dropColumn('total_deductions');
            }
            if (Schema::hasColumn('payrolls', 'other_deductions')) {
                $table->dropColumn('other_deductions');
            }
            if (Schema::hasColumn('payrolls', 'wcf')) {
                $table->dropColumn('wcf');
            }
            if (Schema::hasColumn('payrolls', 'nssf')) {
                $table->dropColumn('nssf');
            }
            if (Schema::hasColumn('payrolls', 'paye')) {
                $table->dropColumn('paye');
            }
            if (Schema::hasColumn('payrolls', 'gross_pay')) {
                $table->dropColumn('gross_pay');
            }
            if (Schema::hasColumn('payrolls', 'bonus')) {
                $table->dropColumn('bonus');
            }
            if (Schema::hasColumn('payrolls', 'overtime')) {
                $table->dropColumn('overtime');
            }
            if (Schema::hasColumn('payrolls', 'allowances')) {
                $table->dropColumn('allowances');
            }
            if (Schema::hasColumn('payrolls', 'basic_salary')) {
                $table->dropColumn('basic_salary');
            }
            if (Schema::hasColumn('payrolls', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
            if (Schema::hasColumn('payrolls', 'payroll_period')) {
                $table->dropColumn('payroll_period');
            }
            if (Schema::hasColumn('payrolls', 'employee_id')) {
                $table->dropConstrainedForeignId('employee_id');
            }
        });

        Schema::table('leaves', function (Blueprint $table) {
            if (Schema::hasColumn('leaves', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('leaves', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('leaves', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }
            if (Schema::hasColumn('leaves', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('leaves', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('leaves', 'days_requested')) {
                $table->dropColumn('days_requested');
            }
            if (Schema::hasColumn('leaves', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('leaves', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('leaves', 'leave_type_id')) {
                $table->dropConstrainedForeignId('leave_type_id');
            }
            if (Schema::hasColumn('leaves', 'employee_id')) {
                $table->dropConstrainedForeignId('employee_id');
            }
        });

        if ($this->hasAttendanceUniqueIndex()) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('attendances_employee_id_date_unique');
            });
        }

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('attendances', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('attendances', 'hours_worked')) {
                $table->dropColumn('hours_worked');
            }
            if (Schema::hasColumn('attendances', 'check_out')) {
                $table->dropColumn('check_out');
            }
            if (Schema::hasColumn('attendances', 'check_in')) {
                $table->dropColumn('check_in');
            }
            if (Schema::hasColumn('attendances', 'date')) {
                $table->dropColumn('date');
            }
            if (Schema::hasColumn('attendances', 'employee_id')) {
                $table->dropConstrainedForeignId('employee_id');
            }
        });
    }

    private function hasAttendanceUniqueIndex(): bool
    {
        return $this->indexExists('attendances', 'attendances_employee_id_date_unique');
    }

    private function hasPayrollUniqueIndex(): bool
    {
        return $this->indexExists('payrolls', 'payrolls_employee_id_payroll_period_unique');
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            return DB::table('pg_indexes')
                ->where('schemaname', 'public')
                ->where('tablename', $table)
                ->where('indexname', $indexName)
                ->exists();
        }

        if ($driver === 'sqlite') {
            return DB::table('sqlite_master')
                ->where('type', 'index')
                ->where('tbl_name', $table)
                ->where('name', $indexName)
                ->exists();
        }

        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();

    }
};
