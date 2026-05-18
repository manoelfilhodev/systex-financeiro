<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Insight;
use App\Models\Lancamento;
use App\Models\Payment;
use App\Models\User;
use App\Services\SmartInsightService;
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

    public function test_lancamento_aceita_valor_formatado_em_brl(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('lancamentos.store'), [
                'tipo' => 'entrada',
                'descricao' => 'Recebimento formatado',
                'valor' => '1.234,56',
                'data_lancamento' => '2026-05-12',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('lancamentos.index'));

        $this->assertDatabaseHas('lancamentos', [
            'user_id' => $user->id,
            'descricao' => 'Recebimento formatado',
            'valor' => 1234.56,
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
        $this->travelTo('2026-05-17 10:00:00');

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
        $this->assertSame('2026-06-16', $user->fresh()->premium_until->toDateString());
    }

    public function test_usuario_comum_nao_acessa_admin(): void
    {
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addMonth(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_admin_acessa_dashboard_financeiro(): void
    {
        $admin = User::factory()->create([
            'plan' => 'admin',
            'subscription_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Controle de entradas')
            ->assertSee('Receita recebida');
    }

    public function test_aprovacao_soma_trinta_dias_quando_usuario_ja_tem_premium(): void
    {
        $this->travelTo('2026-05-17 10:00:00');

        $admin = User::factory()->create([
            'plan' => 'admin',
            'subscription_status' => 'active',
        ]);
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addDays(10),
        ]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'valor' => 49.90,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.approve', $payment))
            ->assertRedirect();

        $this->assertSame('2026-06-26', $user->fresh()->premium_until->toDateString());
    }

    public function test_admin_rejeita_pagamento_sem_alterar_plano_do_usuario(): void
    {
        $admin = User::factory()->create([
            'plan' => 'admin',
            'subscription_status' => 'active',
        ]);
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addDays(20),
        ]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'valor' => 49.90,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.reject', $payment), [
                'observacao' => 'Comprovante ilegível',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'rejected',
            'observacao' => 'Comprovante ilegível',
        ]);

        $user->refresh();

        $this->assertSame('premium', $user->plan);
        $this->assertSame('active', $user->subscription_status);
        $this->assertTrue($user->premium_until->isFuture());
    }

    public function test_admin_cards_calculam_receita_operacional(): void
    {
        $this->travelTo('2026-05-17 10:00:00');

        $admin = User::factory()->create([
            'plan' => 'admin',
            'subscription_status' => 'active',
        ]);
        $trial = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);
        $starter = User::factory()->create([
            'plan' => 'starter',
            'subscription_status' => 'expired',
        ]);

        $approved = Payment::create([
            'user_id' => $trial->id,
            'valor' => 49.90,
            'status' => 'approved',
        ]);
        $approved->forceFill(['updated_at' => now()->subDay()])->save();

        Payment::create([
            'user_id' => $starter->id,
            'valor' => 99.80,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertViewHas('stats', function (array $stats): bool {
                return $stats['total_users'] === 3
                    && $stats['trial_users'] === 1
                    && $stats['starter_users'] === 1
                    && $stats['pending_payments'] === 1
                    && $stats['approved_payments_month'] === 1
                    && (float) $stats['revenue_month'] === 49.9
                    && (float) $stats['pending_revenue'] === 99.8;
            });
    }

    public function test_smart_insights_gera_insight_sem_lancamento(): void
    {
        $user = User::factory()->create();

        app(SmartInsightService::class)->generateForUser($user, '2026-05');

        $this->assertDatabaseHas('insights', [
            'user_id' => $user->id,
            'reference_month' => '2026-05',
            'type' => 'neutral',
            'title' => 'Primeiros registros',
        ]);
    }

    public function test_smart_insights_gera_insight_de_saldo_positivo(): void
    {
        $user = User::factory()->create();

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita',
            'valor' => 1200,
            'data_lancamento' => '2026-05-10',
        ]);

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'saida',
            'descricao' => 'Despesa',
            'valor' => 300,
            'data_lancamento' => '2026-05-12',
        ]);

        app(SmartInsightService::class)->generateForUser($user, '2026-05');

        $this->assertDatabaseHas('insights', [
            'user_id' => $user->id,
            'reference_month' => '2026-05',
            'type' => 'achievement',
            'title' => 'Saldo positivo',
        ]);
    }

    public function test_smart_insights_gera_warning_de_saldo_negativo(): void
    {
        $user = User::factory()->create();

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita',
            'valor' => 200,
            'data_lancamento' => '2026-05-10',
        ]);

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'saida',
            'descricao' => 'Despesa',
            'valor' => 500,
            'data_lancamento' => '2026-05-12',
        ]);

        app(SmartInsightService::class)->generateForUser($user, '2026-05');

        $this->assertDatabaseHas('insights', [
            'user_id' => $user->id,
            'reference_month' => '2026-05',
            'type' => 'warning',
            'title' => 'Saldo negativo',
        ]);
    }

    public function test_smart_insights_nao_duplica_insight_do_mesmo_mes(): void
    {
        $user = User::factory()->create();

        Lancamento::create([
            'user_id' => $user->id,
            'tipo' => 'entrada',
            'descricao' => 'Receita',
            'valor' => 1200,
            'data_lancamento' => '2026-05-10',
        ]);

        app(SmartInsightService::class)->generateForUser($user, '2026-05');
        app(SmartInsightService::class)->generateForUser($user, '2026-05');

        $this->assertSame(1, Insight::where([
            'user_id' => $user->id,
            'reference_month' => '2026-05',
            'type' => 'achievement',
            'title' => 'Saldo positivo',
        ])->count());
    }

    public function test_usuario_nao_marca_insight_de_outro_usuario_como_lido(): void
    {
        $user = User::factory()->create([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addMonth(),
        ]);
        $outroUsuario = User::factory()->create();
        $insight = Insight::create([
            'user_id' => $outroUsuario->id,
            'type' => 'neutral',
            'title' => 'Protegido',
            'message' => 'Insight de outro usuário.',
            'reference_month' => '2026-05',
        ]);

        $this->actingAs($user)
            ->post(route('insights.read', $insight))
            ->assertForbidden();

        $this->assertNull($insight->fresh()->read_at);
    }

    public function test_starter_ve_no_maximo_dois_insights_e_cta(): void
    {
        $user = User::factory()->create([
            'plan' => 'starter',
            'subscription_status' => 'expired',
        ]);

        foreach (range(1, 3) as $index) {
            Insight::create([
                'user_id' => $user->id,
                'type' => 'neutral',
                'title' => "Insight {$index}",
                'message' => "Mensagem {$index}",
                'reference_month' => '2026-05',
            ]);
        }

        $this->actingAs($user)
            ->get(route('dashboard', ['mes' => '2026-05']))
            ->assertOk()
            ->assertViewHas('insights', fn ($insights): bool => $insights->count() === 2)
            ->assertViewHas('showInsightUpgradeCta', true);
    }
}
