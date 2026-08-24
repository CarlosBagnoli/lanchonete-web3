<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoRelatorioDiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_relatorio_do_dia_exibe_pedidos_do_dia(): void
    {
        $user = User::factory()->create(['role' => 'atendente']);
        $produto = Produto::factory()->create(['preco' => 15.00]);

        $pedidoHoje = Pedido::create([
            'user_id' => $user->id,
            'status' => 'aberto',
            'total' => 15.00,
            'observacoes' => 'Hoje',
        ]);

        $pedidoHoje->itens()->create([
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => 15.00,
            'subtotal' => 15.00,
        ]);

        Pedido::factory()->create([
            'user_id' => $user->id,
            'status' => 'fechado',
            'total' => 30.00,
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->get(route('pedidos.relatorio.dia'))
            ->assertOk()
            ->assertSeeText('Relatório do Dia')
            ->assertSeeText('Pedido #')
            ->assertSeeText('aberto');
    }
}
