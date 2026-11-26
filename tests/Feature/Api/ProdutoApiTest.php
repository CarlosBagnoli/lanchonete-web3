<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Produto;
use App\Models\Categoria;

class ProdutoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_produtos_exige_autenticacao()
    {
        $this->getJson('/api/produtos')
            ->assertStatus(401);
    }

    public function test_lista_produtos_com_token_retorna_estrutura_paginada()
    {
        Sanctum::actingAs(User::factory()->create());
        Produto::factory()->count(15)->create();

        $this->getJson('/api/produtos')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    }

    public function test_cria_produto_com_validacao()
    {
        Sanctum::actingAs(User::factory()->create());
        $cat = Categoria::factory()->create();

        $payload = [
            'nome' => 'Coxinha',
            'preco' => 7.50,
            'categoria_id' => $cat->id,
        ];

        $this->postJson('/api/produtos', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.nome', 'Coxinha');
    }

    public function test_validacao_rejeita_payload_invalido()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/produtos', ['nome' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'preco', 'categoria_id']);
    }
}