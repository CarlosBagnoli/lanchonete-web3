<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CepCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_cep_response_includes_cached_flag_and_uses_cache(): void
    {
        $this->get('/cep/01001000')
            ->assertOk()
            ->assertJsonPath('cached', false);

        $this->get('/cep/01001000')
            ->assertOk()
            ->assertJsonPath('cached', true);
    }

    public function test_admin_can_clear_cep_cache(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Cache::put('cep:01001000', ['logradouro' => 'Rua de Teste'], now()->addMinutes(15));

        $this->actingAs($user)
            ->post(route('admin.gerente.cep.cache.clear'))
            ->assertRedirect();

        $this->assertNull(Cache::get('cep:01001000'));
    }
}
