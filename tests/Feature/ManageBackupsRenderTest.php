<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageBackups;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ManageBackupsRenderTest extends TestCase
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

    public function test_page_renders_with_filament_table(): void
    {
        try {
            DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL tidak aktif — test dilewati.');
        }

        $user = User::whereHas('roles', fn ($q) => $q->where('is_super_admin', true))->first();

        $this->assertNotNull($user, 'No super admin user found in database.');

        $this->actingAs($user);

        Livewire::test(ManageBackups::class)
            ->assertOk()
            ->assertSee('Buat Backup Baru')
            ->assertSee('Nama File');
    }
}
