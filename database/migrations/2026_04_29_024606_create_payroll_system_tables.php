<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // 1. Tabel Karyawan
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('nip')->unique(); // Nomor Induk Pegawai
        $table->string('name');
        $table->string('email')->unique();
        $table->string('bank_name')->nullable();
        $table->string('bank_account')->nullable();
        $table->decimal('base_salary', 15, 2); // Gaji Pokok inti
        $table->date('joined_at');
        $table->enum('status', ['permanent', 'contract', 'intern']);
        $table->timestamps();
    });

    // 2. Master Komponen Gaji (Tunjangan/Potongan)
    Schema::create('salary_components', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // misal: Tunjangan Makan, PPh21, BPJS
        $table->enum('type', ['earning', 'deduction']); 
        $table->boolean('is_fixed')->default(true); // Tetap tiap bulan atau variabel
        $table->string('formula')->nullable(); // Tempat simpan rumus string
        $table->timestamps();
    });

    // 3. Batch Payroll (Grup Penggajian Bulanan)
    Schema::create('payroll_batches', function (Blueprint $table) {
        $table->id();
        $table->string('month'); // misal: "04-2024"
        $table->enum('status', ['draft', 'processed', 'approved', 'paid'])->default('draft');
        $table->text('description')->nullable();
        $table->timestamps();
    });

    // 4. Detail Slip Gaji (Header)
    Schema::create('payroll_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('payroll_batch_id')->constrained()->onDelete('cascade');
        $table->foreignId('employee_id')->constrained();
        $table->decimal('total_earning', 15, 2)->default(0);
        $table->decimal('total_deduction', 15, 2)->default(0);
        $table->decimal('take_home_pay', 15, 2)->default(0);
        $table->timestamps();
    });

    // 5. Item Slip Gaji (Breakdown tiap komponen per orang)
    Schema::create('payroll_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('payroll_detail_id')->constrained()->onDelete('cascade');
        $table->foreignId('salary_component_id')->constrained();
        $table->decimal('amount', 15, 2);
        $table->timestamps();
    });
}

};
