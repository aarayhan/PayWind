<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $table->decimal('default_amount', 15, 2)->nullable()->after('is_fixed');
        });

        Schema::table('payroll_batches', function (Blueprint $table) {
            $table->unsignedInteger('total_employees')->default(0)->after('description');
            $table->unsignedInteger('processed_employees')->default(0)->after('total_employees');
            $table->timestamp('started_at')->nullable()->after('processed_employees');
            $table->timestamp('processed_at')->nullable()->after('started_at');
            $table->unique('month');
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('base_salary_snapshot', 15, 2)->default(0)->after('employee_id');
            $table->unique(['payroll_batch_id', 'employee_id']);
        });

        Schema::table('payroll_items', function (Blueprint $table) {
            $table->string('component_name')->after('salary_component_id');
            $table->enum('component_type', ['earning', 'deduction'])->after('component_name');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn(['component_name', 'component_type']);
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropUnique('payroll_details_payroll_batch_id_employee_id_unique');
            $table->dropColumn('base_salary_snapshot');
        });

        Schema::table('payroll_batches', function (Blueprint $table) {
            $table->dropUnique('payroll_batches_month_unique');
            $table->dropColumn(['total_employees', 'processed_employees', 'started_at', 'processed_at']);
        });

        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropColumn('default_amount');
        });
    }
};
