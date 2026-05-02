<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('key');
            $table->string('label');
            $table->string('value')->nullable();
            $table->timestamps();
            $table->unique(['group', 'key']);
        });

        Schema::create('ptkp_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');
            $table->string('ter_category', 1);
            $table->decimal('annual_ptkp', 15, 2);
            $table->timestamps();
        });

        Schema::create('pph21_ter_brackets', function (Blueprint $table) {
            $table->id();
            $table->string('category', 1);
            $table->unsignedBigInteger('lower_bound');
            $table->unsignedBigInteger('upper_bound')->nullable();
            $table->decimal('rate', 6, 4);
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('ptkp_status')->default('TK/0')->after('status');
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('gross_earning', 15, 2)->default(0)->after('base_salary_snapshot');
            $table->decimal('employee_benefit_total', 15, 2)->default(0)->after('gross_earning');
            $table->decimal('employer_benefit_total', 15, 2)->default(0)->after('employee_benefit_total');
            $table->decimal('pph21_amount', 15, 2)->default(0)->after('employer_benefit_total');
        });

        Schema::table('payroll_items', function (Blueprint $table) {
            $table->string('paid_by')->default('employee')->after('component_type');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn('paid_by');
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn(['gross_earning', 'employee_benefit_total', 'employer_benefit_total', 'pph21_amount']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('ptkp_status');
        });

        Schema::dropIfExists('pph21_ter_brackets');
        Schema::dropIfExists('ptkp_statuses');
        Schema::dropIfExists('payroll_settings');
    }
};
