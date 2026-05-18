<?php

namespace App\Services;

use App\Models\Insight;
use App\Models\Lancamento;
use App\Models\User;
use Carbon\CarbonImmutable;

class SmartInsightService
{
    public function generateForUser(User $user, string $month): void
    {
        $inicio = CarbonImmutable::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $fim = $inicio->endOfMonth();
        $inicioAnterior = $inicio->subMonthNoOverflow()->startOfMonth();
        $fimAnterior = $inicioAnterior->endOfMonth();

        $lancamentosMes = Lancamento::query()
            ->where('user_id', $user->id)
            ->whereBetween('data_lancamento', [$inicio->toDateString(), $fim->toDateString()]);

        $quantidadeLancamentos = (clone $lancamentosMes)->count();

        if ($quantidadeLancamentos === 0) {
            $this->createInsight(
                user: $user,
                type: 'neutral',
                title: 'Primeiros registros',
                message: 'Comece registrando suas movimentações para enxergar sua vida financeira com clareza.',
                month: $month,
                metadata: ['reason' => 'no_transactions'],
            );

            return;
        }

        $entradas = (float) (clone $lancamentosMes)->where('tipo', 'entrada')->sum('valor');
        $saidas = (float) (clone $lancamentosMes)->where('tipo', 'saida')->sum('valor');
        $saldo = $entradas - $saidas;

        $lancamentosMesAnterior = Lancamento::query()
            ->where('user_id', $user->id)
            ->whereBetween('data_lancamento', [$inicioAnterior->toDateString(), $fimAnterior->toDateString()]);

        $entradasAnteriores = (float) (clone $lancamentosMesAnterior)->where('tipo', 'entrada')->sum('valor');
        $saidasAnteriores = (float) (clone $lancamentosMesAnterior)->where('tipo', 'saida')->sum('valor');

        if ($saidasAnteriores > 0 && $saidas < $saidasAnteriores) {
            $this->createInsight(
                user: $user,
                type: 'positive',
                title: 'Despesas em queda',
                message: 'Parabéns! Suas despesas diminuíram em relação ao mês passado.',
                month: $month,
                metadata: ['current' => $saidas, 'previous' => $saidasAnteriores],
            );
        }

        if ($saidasAnteriores > 0 && $saidas > $saidasAnteriores) {
            $this->createInsight(
                user: $user,
                type: 'warning',
                title: 'Despesas em alta',
                message: 'Suas despesas aumentaram em relação ao mês passado. Vale revisar os principais gastos.',
                month: $month,
                metadata: ['current' => $saidas, 'previous' => $saidasAnteriores],
            );
        }

        if ($entradasAnteriores > 0 && $entradas > $entradasAnteriores) {
            $this->createInsight(
                user: $user,
                type: 'trend',
                title: 'Entradas evoluindo',
                message: 'Suas entradas cresceram em relação ao mês passado. Ótimo sinal para o caixa.',
                month: $month,
                metadata: ['current' => $entradas, 'previous' => $entradasAnteriores],
            );
        }

        if ($saldo >= 0) {
            $this->createInsight(
                user: $user,
                type: 'achievement',
                title: 'Saldo positivo',
                message: 'Você está fechando o mês com saldo positivo. Continue assim.',
                month: $month,
                metadata: ['saldo' => $saldo],
            );
        } else {
            $this->createInsight(
                user: $user,
                type: 'warning',
                title: 'Saldo negativo',
                message: 'Seu saldo está negativo neste mês. Analise suas maiores despesas.',
                month: $month,
                metadata: ['saldo' => $saldo],
            );
        }

        $maiorCategoria = $this->maiorCategoriaDeSaida($user, $inicio, $fim);

        if ($maiorCategoria) {
            $this->createInsight(
                user: $user,
                type: 'trend',
                title: 'Maior categoria de gasto',
                message: "Sua maior categoria de gasto este mês foi: {$maiorCategoria['nome']}.",
                month: $month,
                metadata: $maiorCategoria,
            );
        }

        if ($entradas > 0 && $saldo > 0) {
            $percentualEconomia = round(($saldo / $entradas) * 100);

            $this->createInsight(
                user: $user,
                type: 'positive',
                title: 'Economia do mês',
                message: "Você economizou aproximadamente {$percentualEconomia}% das suas entradas este mês.",
                month: $month,
                metadata: ['percentual' => $percentualEconomia, 'saldo' => $saldo, 'entradas' => $entradas],
            );
        }
    }

    /**
     * @return array{nome: string, total: float}|null
     */
    private function maiorCategoriaDeSaida(User $user, CarbonImmutable $inicio, CarbonImmutable $fim): ?array
    {
        $categoria = Lancamento::query()
            ->where('lancamentos.user_id', $user->id)
            ->whereBetween('lancamentos.data_lancamento', [$inicio->toDateString(), $fim->toDateString()])
            ->where('lancamentos.tipo', 'saida')
            ->leftJoin('categorias', 'lancamentos.categoria_id', '=', 'categorias.id')
            ->selectRaw('COALESCE(categorias.nome, ?) as nome, SUM(lancamentos.valor) as total', ['Sem categoria'])
            ->groupBy('categorias.id', 'categorias.nome')
            ->orderByDesc('total')
            ->first();

        if (! $categoria) {
            return null;
        }

        return [
            'nome' => $categoria->nome,
            'total' => round((float) $categoria->total, 2),
        ];
    }

    private function createInsight(User $user, string $type, string $title, string $message, string $month, array $metadata = []): Insight
    {
        return Insight::firstOrCreate(
            [
                'user_id' => $user->id,
                'reference_month' => $month,
                'type' => $type,
                'title' => $title,
            ],
            [
                'message' => $message,
                'metadata' => $metadata,
            ],
        );
    }
}
