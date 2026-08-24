<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_pedido_pode_mudar_de_status_em_sequencia(): void
    {
        $user = User::factory()->create(['role' => 'atendente']);
        $pedido = Pedido::create([
            'user_id' => $user->id,
            'status' => 'aberto',
            'total' => 0,
            'observacoes' => 'Teste',
        ]);

        $this->actingAs($user)
            ->post(route('pedidos.status.update', $pedido), ['status' => 'em preparo'])
            ->assertRedirect();

        $pedido->refresh();
        $this->assertSame('em preparo', $pedido->status);
    }

    public function test_pedido_fechado_nao_pode_receber_itens_ou_alteracoes(): void
    {
        $user = User::factory()->create(['role' => 'atendente']);
        $produto = Produto::factory()->create(['preco' => 10.00]);
        $pedido = Pedido::create([
            'user_id' => $user->id,
            'status' => 'fechado',
            'total' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('pedidos.itens.store', $pedido), [
                'produto_id' => $produto->id,
                'quantidade' => 1,
            ])
            ->assertForbidden();
    }
}
