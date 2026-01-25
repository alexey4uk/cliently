<?php

namespace Tests\Feature;

use App\Jobs\SendBroadcastJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'panel.broadcasts.send', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'panel.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->syncPermissions(['panel.broadcasts.send', 'panel.access']);
    }

    public function test_broadcast_store_dispatches_job_and_redirects(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)
            ->post(route('panel.broadcasts.store'), [
                'title' => 'Тест',
                'message' => 'Текст рассылки',
                'target' => 'owners',
                'channels' => ['system'],
            ]);

        $response->assertRedirect(route('panel.broadcasts.index'));
        $response->assertSessionHas('success', 'Рассылка поставлена в очередь. Уведомления будут разосланы в ближайшее время.');

        Queue::assertPushed(SendBroadcastJob::class);
        $this->assertDatabaseHas('notification_broadcasts', ['title' => 'Тест']);
    }
}
