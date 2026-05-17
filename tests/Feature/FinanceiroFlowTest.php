<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Lancamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_calcula_totais_do_usuario_no_mes_filtrado(): void
    {
        $user = User::factory()->create();
        $outroUsuario = User::factory()->create();

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita mensal',
            'valor' => 1500,
            'data_lancamento' => '2026-05-10',
        ]);

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'saida',
            'descricao' => 'Despesa mensal',
            'valor' => 450,
            'data_lancamento' => '2026-05-12',
        ]);

        Lancamento::create([
            'user_id' => $outroUsuario->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita externa',
            'valor' => 9000,
            'data_lancamento' => '2026-05-10',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['mes' => '2026-05']))
            ->assertOk()
            ->assertSee('R$ 1.500,00')
            ->assertSee('R$ 450,00')
            ->assertSee('R$ 1.050,00')
            ->assertDontSee('R$ 9.000,00');
    }

    public function test_lancamento_nao_aceita_categoria_de_outro_usuario(): void
    {
        $user = User::factory()->create();
        $outroUsuario = User::factory()->create();

        $categoriaExterna = Categoria::create([
            'user_id' => $outroUsuario->id,
            'nome' => 'Categoria externa',
            'tipo' => 'ambos',
        ]);

        $this->actingAs($user)
            ->post(route('lancamentos.store'), [
                'tipo' => 'entrada',
                'descricao' => 'Tentativa inválida',
                'valor' => 100,
                'data_lancamento' => '2026-05-12',
                'categoria_id' => $categoriaExterna->id,
            ])
            ->assertSessionHasErrors('categoria_id');

        $this->assertDatabaseMissing('lancamentos', [
            'user_id' => $user->id,
            'descricao' => 'Tentativa inválida',
        ]);
    }

    public function test_usuario_nao_edita_lancamento_de_outro_usuario(): void
    {
        $user = User::factory()->create();
        $outroUsuario = User::factory()->create();

        $lancamentoExterno = Lancamento::create([
            'user_id' => $outroUsuario->id,
            'tipo' => 'saida',
            'descricao' => 'Dado protegido',
            'valor' => 200,
            'data_lancamento' => '2026-05-12',
        ]);

        $this->actingAs($user)
            ->get(route('lancamentos.edit', $lancamentoExterno))
            ->assertNotFound();
    }
}
