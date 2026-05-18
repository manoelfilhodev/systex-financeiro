<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Lancamento;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_dashboard_envia_dados_dos_graficos_isolados_por_usuario(): void
    {
        $user = User::factory()->create();
        $outroUsuario = User::factory()->create();

        $categoria = Categoria::create([
            'user_id' => $user->id,
            'nome' => 'Infraestrutura',
            'tipo' => 'saida',
        ]);

        Lancamento::create([
            'user_id' => $user->id,
            'categoria_id' => $categoria->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita',
            'valor' => 1000,
            'data_lancamento' => '2026-05-01',
        ]);

        Lancamento::create([
            'user_id' => $user->id,
            'categoria_id' => $categoria->id,
            'tipo' => 'saida',
            'descricao' => 'Servidor',
            'valor' => 250,
            'data_lancamento' => '2026-05-02',
        ]);

        Lancamento::create([
            'user_id' => $outroUsuario->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita externa',
            'valor' => 9000,
            'data_lancamento' => '2026-05-01',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['mes' => '2026-05']))
            ->assertOk()
            ->assertViewHas('chartData', function (array $chartData): bool {
                return $chartData['entradasPorDia'][0] === 1000.0
                    && $chartData['saidasPorDia'][1] === 250.0
                    && $chartData['saldoAcumuladoPorDia'][1] === 750.0
                    && $chartData['categoriasSaida']->contains('Infraestrutura')
                    && $chartData['valoresPorCategoria']->contains(250.0)
                    && $chartData['margemPercentual'] === 75.0
                    && ! in_array(9000.0, $chartData['entradasPorDia'], true);
            });
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

    public function test_usuario_pode_salvar_tema_visual(): void
    {
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addMonth(),
        ]);

        $this->actingAs($user)
            ->post(route('theme.update'), [
                'theme' => 'midnight',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'midnight',
        ]);
    }

    public function test_layout_aplica_classe_do_tema_salvo_no_body(): void
    {
        $user = User::factory()->create([
            'theme' => 'pink-neon',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('theme-pink-neon font-sans', false);
    }

    public function test_tema_visual_invalido_e_rejeitado(): void
    {
        $user = User::factory()->create([
            'theme' => 'systex-default',
        ]);

        $this->actingAs($user)
            ->post(route('theme.update'), [
                'theme' => 'tema-inexistente',
            ])
            ->assertSessionHasErrors('theme');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'systex-default',
        ]);
    }

    public function test_tema_visual_e_individual_por_usuario(): void
    {
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addMonth(),
            'theme' => 'aurora',
        ]);
        $outroUsuario = User::factory()->create([
            'theme' => 'office-clean',
        ]);

        $this->actingAs($user)
            ->post(route('theme.update'), [
                'theme' => 'cyberpunk',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'cyberpunk',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $outroUsuario->id,
            'theme' => 'office-clean',
        ]);
    }

    public function test_trial_expirado_vira_starter_sem_perder_acesso(): void
    {
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->subDay(),
            'premium_until' => null,
            'theme' => 'cyberpunk',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Seu trial expirou.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan' => 'starter',
            'subscription_status' => 'expired',
            'theme' => 'systex-default',
        ]);
    }

    public function test_starter_nao_aplica_tema_premium(): void
    {
        $user = User::factory()->create([
            'plan' => 'starter',
            'subscription_status' => 'expired',
            'theme' => 'systex-default',
        ]);

        $this->actingAs($user)
            ->post(route('theme.update'), [
                'theme' => 'pink-neon',
            ])
            ->assertSessionHasErrors('theme');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'systex-default',
        ]);
    }

    public function test_usuario_envia_comprovante_pix_manual(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'plan' => 'starter',
            'subscription_status' => 'expired',
        ]);

        $this->actingAs($user)
            ->post(route('payments.store'), [
                'comprovante' => UploadedFile::fake()->image('pix.png'),
                'observacao' => 'Pagamento realizado',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $payment = Payment::where('user_id', $user->id)->first();

        $this->assertSame('pending', $payment->status);
        Storage::disk('local')->assertExists($payment->comprovante_path);
    }

    public function test_admin_aprova_pagamento_e_libera_premium(): void
    {
        $admin = User::factory()->create([
            'plan' => 'admin',
            'subscription_status' => 'active',
        ]);
        $user = User::factory()->create([
            'plan' => 'starter',
            'subscription_status' => 'pending',
        ]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'valor' => 49.90,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.approve', $payment))
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan' => 'premium',
            'subscription_status' => 'active',
        ]);

        $this->assertTrue($user->fresh()->premium_until->isFuture());
    }
}
