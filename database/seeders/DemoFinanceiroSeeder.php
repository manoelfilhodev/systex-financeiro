<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Lancamento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoFinanceiroSeeder extends Seeder
{
    private const DEMO_MARKER = 'demo-financeiro-seeder';

    public function run(): void
    {
        if (app()->isProduction() && ! filter_var(env('ALLOW_DEMO_SEEDER', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->command?->warn('DemoFinanceiroSeeder não executado em produção. Defina ALLOW_DEMO_SEEDER=true apenas se tiver certeza.');

            return;
        }

        $email = env('DEMO_USER_EMAIL', 'demo@systexfinanceiro.local');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('DEMO_USER_NAME', 'Demo Systex'),
                'password' => Hash::make(env('DEMO_USER_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ],
        );

        $user->forceFill([
            'plan' => 'premium',
            'subscription_status' => 'active',
            'premium_until' => now()->addMonths(6),
            'theme' => env('DEMO_USER_THEME', 'midnight'),
        ])->save();

        $categorias = $this->categorias($user);

        Lancamento::query()
            ->where('user_id', $user->id)
            ->where('observacao', self::DEMO_MARKER)
            ->delete();

        foreach ($this->lancamentos() as $lancamento) {
            Lancamento::create([
                'user_id' => $user->id,
                'categoria_id' => $categorias[$lancamento['categoria']]->id,
                'tipo' => $lancamento['tipo'],
                'descricao' => $lancamento['descricao'],
                'valor' => $lancamento['valor'],
                'data_lancamento' => $lancamento['data'],
                'observacao' => self::DEMO_MARKER,
            ]);
        }

        $this->command?->info("Dados demo criados para {$user->email}.");
    }

    /**
     * @return array<string, Categoria>
     */
    private function categorias(User $user): array
    {
        $definitions = [
            ['nome' => 'Salário', 'tipo' => 'entrada', 'cor' => '#34d399'],
            ['nome' => 'Freelance', 'tipo' => 'entrada', 'cor' => '#22d3ee'],
            ['nome' => 'Reembolso', 'tipo' => 'entrada', 'cor' => '#a78bfa'],
            ['nome' => 'Alimentação', 'tipo' => 'saida', 'cor' => '#ff2a2a'],
            ['nome' => 'Transporte', 'tipo' => 'saida', 'cor' => '#60a5fa'],
            ['nome' => 'Moradia', 'tipo' => 'saida', 'cor' => '#f97316'],
            ['nome' => 'Lazer', 'tipo' => 'saida', 'cor' => '#ec4899'],
            ['nome' => 'Assinaturas', 'tipo' => 'saida', 'cor' => '#8b5cf6'],
            ['nome' => 'Mercado', 'tipo' => 'saida', 'cor' => '#facc15'],
            ['nome' => 'Saúde', 'tipo' => 'saida', 'cor' => '#10b981'],
        ];

        $categorias = [];

        foreach ($definitions as $definition) {
            $categorias[$definition['nome']] = Categoria::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'nome' => $definition['nome'],
                ],
                [
                    'tipo' => $definition['tipo'],
                    'cor' => $definition['cor'],
                ],
            );
        }

        return $categorias;
    }

    /**
     * @return array<int, array{data: string, tipo: string, categoria: string, descricao: string, valor: float}>
     */
    private function lancamentos(): array
    {
        return [
            ['data' => '2026-01-05', 'tipo' => 'entrada', 'categoria' => 'Salário', 'descricao' => 'Salário mensal', 'valor' => 5200.00],
            ['data' => '2026-01-12', 'tipo' => 'entrada', 'categoria' => 'Freelance', 'descricao' => 'Projeto landing page', 'valor' => 950.00],
            ['data' => '2026-01-06', 'tipo' => 'saida', 'categoria' => 'Moradia', 'descricao' => 'Aluguel', 'valor' => 1850.00],
            ['data' => '2026-01-09', 'tipo' => 'saida', 'categoria' => 'Mercado', 'descricao' => 'Compras do mês', 'valor' => 720.00],
            ['data' => '2026-01-14', 'tipo' => 'saida', 'categoria' => 'Transporte', 'descricao' => 'Combustível e app', 'valor' => 360.00],
            ['data' => '2026-01-18', 'tipo' => 'saida', 'categoria' => 'Assinaturas', 'descricao' => 'Streaming e ferramentas', 'valor' => 189.90],
            ['data' => '2026-01-24', 'tipo' => 'saida', 'categoria' => 'Lazer', 'descricao' => 'Cinema e restaurante', 'valor' => 310.00],

            ['data' => '2026-02-05', 'tipo' => 'entrada', 'categoria' => 'Salário', 'descricao' => 'Salário mensal', 'valor' => 5200.00],
            ['data' => '2026-02-11', 'tipo' => 'entrada', 'categoria' => 'Reembolso', 'descricao' => 'Reembolso corporativo', 'valor' => 280.00],
            ['data' => '2026-02-17', 'tipo' => 'entrada', 'categoria' => 'Freelance', 'descricao' => 'Consultoria financeira', 'valor' => 1350.00],
            ['data' => '2026-02-06', 'tipo' => 'saida', 'categoria' => 'Moradia', 'descricao' => 'Aluguel', 'valor' => 1850.00],
            ['data' => '2026-02-10', 'tipo' => 'saida', 'categoria' => 'Alimentação', 'descricao' => 'Restaurantes', 'valor' => 540.00],
            ['data' => '2026-02-13', 'tipo' => 'saida', 'categoria' => 'Mercado', 'descricao' => 'Supermercado', 'valor' => 690.00],
            ['data' => '2026-02-19', 'tipo' => 'saida', 'categoria' => 'Saúde', 'descricao' => 'Consulta e farmácia', 'valor' => 420.00],
            ['data' => '2026-02-25', 'tipo' => 'saida', 'categoria' => 'Assinaturas', 'descricao' => 'Software e streaming', 'valor' => 219.90],

            ['data' => '2026-03-05', 'tipo' => 'entrada', 'categoria' => 'Salário', 'descricao' => 'Salário mensal', 'valor' => 5450.00],
            ['data' => '2026-03-15', 'tipo' => 'entrada', 'categoria' => 'Freelance', 'descricao' => 'Projeto dashboard', 'valor' => 1600.00],
            ['data' => '2026-03-06', 'tipo' => 'saida', 'categoria' => 'Moradia', 'descricao' => 'Aluguel', 'valor' => 1850.00],
            ['data' => '2026-03-09', 'tipo' => 'saida', 'categoria' => 'Mercado', 'descricao' => 'Compras da quinzena', 'valor' => 760.00],
            ['data' => '2026-03-12', 'tipo' => 'saida', 'categoria' => 'Transporte', 'descricao' => 'Transporte mensal', 'valor' => 410.00],
            ['data' => '2026-03-20', 'tipo' => 'saida', 'categoria' => 'Lazer', 'descricao' => 'Viagem curta', 'valor' => 680.00],
            ['data' => '2026-03-27', 'tipo' => 'saida', 'categoria' => 'Alimentação', 'descricao' => 'Delivery e restaurantes', 'valor' => 590.00],

            ['data' => '2026-04-05', 'tipo' => 'entrada', 'categoria' => 'Salário', 'descricao' => 'Salário mensal', 'valor' => 5450.00],
            ['data' => '2026-04-16', 'tipo' => 'entrada', 'categoria' => 'Freelance', 'descricao' => 'Landing institucional', 'valor' => 1800.00],
            ['data' => '2026-04-06', 'tipo' => 'saida', 'categoria' => 'Moradia', 'descricao' => 'Aluguel', 'valor' => 1850.00],
            ['data' => '2026-04-08', 'tipo' => 'saida', 'categoria' => 'Mercado', 'descricao' => 'Supermercado', 'valor' => 710.00],
            ['data' => '2026-04-12', 'tipo' => 'saida', 'categoria' => 'Alimentação', 'descricao' => 'Restaurantes', 'valor' => 460.00],
            ['data' => '2026-04-18', 'tipo' => 'saida', 'categoria' => 'Assinaturas', 'descricao' => 'SaaS e streaming', 'valor' => 249.90],
            ['data' => '2026-04-23', 'tipo' => 'saida', 'categoria' => 'Saúde', 'descricao' => 'Exames', 'valor' => 330.00],

            ['data' => '2026-05-05', 'tipo' => 'entrada', 'categoria' => 'Salário', 'descricao' => 'Salário mensal', 'valor' => 5600.00],
            ['data' => '2026-05-13', 'tipo' => 'entrada', 'categoria' => 'Freelance', 'descricao' => 'Projeto premium', 'valor' => 1650.00],
            ['data' => '2026-05-21', 'tipo' => 'entrada', 'categoria' => 'Reembolso', 'descricao' => 'Reembolso de viagem', 'valor' => 400.00],
            ['data' => '2026-05-06', 'tipo' => 'saida', 'categoria' => 'Moradia', 'descricao' => 'Aluguel', 'valor' => 1850.00],
            ['data' => '2026-05-09', 'tipo' => 'saida', 'categoria' => 'Mercado', 'descricao' => 'Compras do mês', 'valor' => 680.00],
            ['data' => '2026-05-11', 'tipo' => 'saida', 'categoria' => 'Alimentação', 'descricao' => 'Restaurantes e delivery', 'valor' => 520.00],
            ['data' => '2026-05-17', 'tipo' => 'saida', 'categoria' => 'Transporte', 'descricao' => 'Combustível e app', 'valor' => 390.00],
            ['data' => '2026-05-22', 'tipo' => 'saida', 'categoria' => 'Assinaturas', 'descricao' => 'Ferramentas digitais', 'valor' => 259.90],
            ['data' => '2026-05-26', 'tipo' => 'saida', 'categoria' => 'Lazer', 'descricao' => 'Jantar e cinema', 'valor' => 360.00],
        ];
    }
}
