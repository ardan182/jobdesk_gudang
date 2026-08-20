<?php

namespace Tests\Feature;

use App\Filament\Resources\TaskDatangMobilSuppliers\Pages\ListTaskDatangMobilSuppliers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ExportColumnSelectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'jobdesk_gudang',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
    }

    public function test_datang_mobil_supplier_export_has_column_selector(): void
    {
        try {
            DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL tidak aktif — test dilewati.');
        }

        $user = User::whereHas('roles', fn ($q) => $q->where('is_super_admin', true))->first();
        $this->assertNotNull($user, 'No super admin user found.');
        $this->actingAs($user);

        $this
            ->get('/admin/task-datang-mobil-suppliers')
            ->assertOk()
            ->assertSee('Export XLSX')
            ->assertSee('Export PDF');

        Livewire::test(ListTaskDatangMobilSuppliers::class)
            ->mountTableAction('export_xlsx')
            ->assertHasNoErrors();

        Livewire::test(ListTaskDatangMobilSuppliers::class)
            ->mountTableAction('export_pdf')
            ->assertHasNoErrors();
    }
}