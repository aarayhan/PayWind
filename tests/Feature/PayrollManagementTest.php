<?php

namespace Tests\Feature;

use App\Livewire\EmployeeManager;
use App\Livewire\PayrollProcessor;
use App\Livewire\PayrollSettingsManager;
use App\Livewire\UserRoleManager;
use App\Models\AuditLog;
use App\Livewire\SalaryComponentManager;
use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\PayrollDetail;
use App\Models\PayrollSetting;
use App\Models\Pph21TerBracket;
use App\Models\SalaryComponent;
use App\Models\User;
use Database\Seeders\PayrollReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PayrollManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_employee_management_page(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('employees.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Karyawan');
    }

    public function test_employee_manager_can_store_employee(): void
    {
        Livewire::test(EmployeeManager::class)
            ->set('nip', 'EMP-001')
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('base_salary', '5500000')
            ->set('joined_at', '2026-04-01')
            ->set('status', 'permanent')
            ->set('ptkp_status', 'K/1')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('employees', [
            'nip' => 'EMP-001',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'status' => 'permanent',
            'ptkp_status' => 'K/1',
        ]);
    }

    public function test_authenticated_user_can_access_salary_component_management_page(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('salary-components.index'));

        $response->assertOk();
        $response->assertSee('Master Komponen Gaji');
    }

    public function test_salary_component_manager_can_store_component(): void
    {
        Livewire::test(SalaryComponentManager::class)
            ->set('name', 'BPJS Kesehatan')
            ->set('type', 'deduction')
            ->set('is_fixed', true)
            ->set('default_amount', '100000')
            ->set('formula', 'base_salary * 0.01')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('salary_components', [
            'name' => 'BPJS Kesehatan',
            'type' => 'deduction',
            'default_amount' => 100000,
            'formula' => 'base_salary * 0.01',
        ]);
    }

    public function test_authenticated_user_can_access_payroll_processor_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('payroll-batches.index'));

        $response->assertOk();
        $response->assertSee('Proses Payroll');
    }

    public function test_authenticated_user_can_access_payroll_settings_page(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->get(route('payroll-settings.index'));

        $response->assertOk();
        $response->assertSee('Payroll Settings');
    }

    public function test_regular_user_cannot_access_payroll_settings_page(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('payroll-settings.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_user_role_manager_page(): void
    {
        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->get(route('user-roles.index'));

        $response->assertOk();
        $response->assertSee('User Role Management');
    }

    public function test_non_super_admin_cannot_access_user_role_manager_page(): void
    {
        $user = User::factory()->payrollManager()->create();

        $response = $this->actingAs($user)->get(route('user-roles.index'));

        $response->assertForbidden();
    }

    public function test_payroll_processor_can_create_batch(): void
    {
        Livewire::test(PayrollProcessor::class)
            ->set('batchMonth', '4')
            ->set('batchYear', '2026')
            ->call('createBatch')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('payroll_batches', [
            'month' => '2026-04',
            'status' => 'draft',
        ]);
    }

    public function test_payroll_processor_generates_snapshot_for_each_employee(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $employee = Employee::query()->create([
            'nip' => 'EMP-100',
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'base_salary' => 7000000,
            'joined_at' => '2026-01-10',
            'status' => 'permanent',
            'ptkp_status' => 'TK/0',
        ]);

        SalaryComponent::query()->create([
            'name' => 'Tunjangan Transport',
            'type' => 'earning',
            'is_fixed' => true,
            'default_amount' => 500000,
        ]);

        $batch = PayrollBatch::query()->create([
            'month' => '2026-04',
            'status' => 'draft',
            'description' => 'Payroll periode April 2026',
        ]);

        $component = Livewire::test(PayrollProcessor::class)
            ->call('processBatch', $batch->id);

        $component->call('continueProcessing');
        $component->call('continueProcessing');

        $batch->refresh();
        $detail = PayrollDetail::query()->where('payroll_batch_id', $batch->id)->firstOrFail();

        $this->assertSame('processed', $batch->status);
        $this->assertSame(1, $batch->processed_employees);
        $this->assertEquals(7000000, $detail->base_salary_snapshot);
        $this->assertEquals(7500000, $detail->total_earning);
        $this->assertEquals(373750, $detail->total_deduction);
        $this->assertEquals(7126250, $detail->take_home_pay);
        $this->assertEquals(280000, $detail->employee_benefit_total);
        $this->assertEquals(716800, $detail->employer_benefit_total);
        $this->assertEquals(93750, $detail->pph21_amount);
        $this->assertDatabaseHas('payroll_items', [
            'payroll_detail_id' => $detail->id,
            'component_name' => 'Gaji Pokok',
            'component_type' => 'earning',
            'amount' => 7000000,
        ]);
        $this->assertDatabaseHas('payroll_items', [
            'payroll_detail_id' => $detail->id,
            'component_name' => 'Tunjangan Transport',
            'component_type' => 'earning',
            'amount' => 500000,
        ]);
        $this->assertDatabaseHas('payroll_items', [
            'payroll_detail_id' => $detail->id,
            'component_name' => 'BPJS Kesehatan',
            'component_type' => 'deduction',
            'paid_by' => 'employee',
            'amount' => 70000,
        ]);
        $this->assertDatabaseHas('payroll_items', [
            'payroll_detail_id' => $detail->id,
            'component_name' => 'PPh 21',
            'component_type' => 'deduction',
            'paid_by' => 'employee',
            'amount' => 93750,
        ]);

        $employee->update(['base_salary' => 8000000]);

        $this->assertEquals(7000000, $detail->fresh()->base_salary_snapshot);
    }

    public function test_recalculate_tax_rebuilds_statutory_items(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        Employee::query()->create([
            'nip' => 'EMP-200',
            'name' => 'Rina',
            'email' => 'rina@example.com',
            'base_salary' => 6000000,
            'joined_at' => '2026-04-01',
            'status' => 'permanent',
            'ptkp_status' => 'TK/0',
        ]);

        SalaryComponent::query()->create([
            'name' => 'Tunjangan Jabatan',
            'type' => 'earning',
            'is_fixed' => true,
            'default_amount' => 1000000,
        ]);

        $batch = PayrollBatch::query()->create([
            'month' => '2026-05',
            'status' => 'draft',
            'description' => 'Payroll periode Mei 2026',
        ]);

        $component = Livewire::test(PayrollProcessor::class);
        $component->call('processBatch', $batch->id);
        $component->call('continueProcessing');

        $detail = PayrollDetail::query()->where('payroll_batch_id', $batch->id)->firstOrFail();
        $detail->items()->where('component_name', 'Tunjangan Jabatan')->update(['amount' => 2000000]);

        $component->call('recalculateTax', $detail->id);

        $detail->refresh();

        $this->assertEquals(8000000, $detail->total_earning);
        $this->assertEquals(360000, $detail->total_deduction);
        $this->assertEquals(7640000, $detail->take_home_pay);
    }

    public function test_payroll_settings_manager_updates_setting_values(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $this->actingAs(User::factory()->superAdmin()->create());

        $healthCap = PayrollSetting::query()
            ->where('group', 'bpjs_kesehatan')
            ->where('key', 'salary_cap')
            ->firstOrFail();

        $roundingMethod = PayrollSetting::query()
            ->where('group', 'payroll_global')
            ->where('key', 'tax_rounding_method')
            ->firstOrFail();

        Livewire::test(PayrollSettingsManager::class)
            ->set("settings.{$healthCap->id}", '13000000')
            ->set("settings.{$roundingMethod->id}", 'floor')
            ->call('saveSettings');

        $this->assertDatabaseHas('payroll_settings', [
            'id' => $healthCap->id,
            'value' => '13000000',
        ]);

        $this->assertDatabaseHas('payroll_settings', [
            'id' => $roundingMethod->id,
            'value' => 'floor',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'updated',
            'auditable_type' => PayrollSetting::class,
            'auditable_id' => $healthCap->id,
        ]);
    }

    public function test_payroll_settings_manager_can_add_and_remove_ter_bracket(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $this->actingAs(User::factory()->superAdmin()->create());

        $component = Livewire::test(PayrollSettingsManager::class);

        $beforeCount = Pph21TerBracket::query()->where('category', 'A')->count();

        $component->call('addTerBracket', 'A');

        $afterAddCount = Pph21TerBracket::query()->where('category', 'A')->count();

        $this->assertSame($beforeCount + 1, $afterAddCount);

        $lastBracket = Pph21TerBracket::query()->where('category', 'A')->latest('id')->firstOrFail();

        $component->call('deleteTerBracket', $lastBracket->id);

        $this->assertSame($beforeCount, Pph21TerBracket::query()->where('category', 'A')->count());
        $this->assertTrue(AuditLog::query()->where('auditable_type', Pph21TerBracket::class)->exists());
    }

    public function test_tax_service_respects_rounding_setting(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        PayrollSetting::query()
            ->where('group', 'payroll_global')
            ->where('key', 'tax_rounding_method')
            ->update(['value' => 'ceil']);

        $taxService = app(\App\Services\TaxService::class);

        $result = $taxService->calculateMonthlyPph21(5650001, 'TK/0');

        $this->assertSame(28251.0, (float) $result['amount']);
    }

    public function test_ter_bracket_validation_rejects_gap_or_overlap(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $this->actingAs(User::factory()->superAdmin()->create());

        $brackets = Pph21TerBracket::query()
            ->where('category', 'A')
            ->orderBy('sort_order')
            ->take(2)
            ->get();

        $first = $brackets[0];
        $second = $brackets[1];

        Livewire::test(PayrollSettingsManager::class)
            ->set("terBrackets.{$first->id}.lower_bound", '0')
            ->set("terBrackets.{$first->id}.upper_bound", '5400000')
            ->set("terBrackets.{$second->id}.lower_bound", '5400000')
            ->set("terBrackets.{$second->id}.upper_bound", '5650000')
            ->call('saveTerBrackets')
            ->assertHasErrors(["terBrackets.{$second->id}.lower_bound"]);
    }

    public function test_payroll_settings_are_locked_when_batch_generation_is_active(): void
    {
        $this->seed(PayrollReferenceSeeder::class);

        $this->actingAs(User::factory()->payrollManager()->create());

        PayrollBatch::query()->create([
            'month' => '2026-12',
            'status' => 'draft',
            'description' => 'Locked batch',
            'total_employees' => 10,
            'processed_employees' => 3,
        ]);

        Livewire::test(PayrollSettingsManager::class)
            ->assertSet('isLocked', true);
    }

    public function test_super_admin_can_update_user_role_from_ui(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $targetUser = User::factory()->create(['role' => 'employee']);

        $this->actingAs($admin);

        Livewire::test(UserRoleManager::class)
            ->set("roles.{$targetUser->id}", 'payroll-manager')
            ->call('saveRole', $targetUser->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => 'payroll-manager',
        ]);
    }

    public function test_employee_model_has_payroll_detail_relation(): void
    {
        $employee = new Employee();

        $this->assertSame('payroll_details', $employee->payrollDetails()->getRelated()->getTable());
    }

    public function test_salary_component_model_has_payroll_item_relation(): void
    {
        $component = new SalaryComponent();

        $this->assertSame('payroll_items', $component->payrollItems()->getRelated()->getTable());
    }
}
