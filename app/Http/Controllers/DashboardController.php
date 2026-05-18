<?php

namespace App\Http\Controllers;

use App\Models\Lancamento;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes = $validated['mes'] ?? now()->format('Y-m');
        $inicio = CarbonImmutable::createFromFormat('Y-m-d', $mes.'-01')->startOfMonth();
        $fim = $inicio->endOfMonth();

        $baseQuery = Lancamento::query()
            ->where('user_id', $request->user()->id)
            ->whereBetween('data_lancamento', [$inicio->toDateString(), $fim->toDateString()]);

        $totalEntradas = (clone $baseQuery)->where('tipo', 'entrada')->sum('valor');
        $totalSaidas = (clone $baseQuery)->where('tipo', 'saida')->sum('valor');
        $saldoMes = $totalEntradas - $totalSaidas;

        $totaisPorDia = (clone $baseQuery)
            ->selectRaw('data_lancamento, tipo, SUM(valor) as total')
            ->groupBy('data_lancamento', 'tipo')
            ->orderBy('data_lancamento')
            ->get()
            ->groupBy(fn (Lancamento $lancamento) => $lancamento->data_lancamento->format('Y-m-d'));

        $dias = collect(range(1, $inicio->daysInMonth));
        $saldoAcumulado = 0;
        $entradasPorDia = [];
        $saidasPorDia = [];
        $saldoAcumuladoPorDia = [];

        $labelsDias = $dias->map(function (int $dia) use ($inicio, $totaisPorDia, &$saldoAcumulado, &$entradasPorDia, &$saidasPorDia, &$saldoAcumuladoPorDia): string {
            $data = $inicio->setDay($dia);
            $chave = $data->format('Y-m-d');
            $totais = $totaisPorDia->get($chave, collect());

            $entrada = (float) ($totais->firstWhere('tipo', 'entrada')->total ?? 0);
            $saida = (float) ($totais->firstWhere('tipo', 'saida')->total ?? 0);

            $saldoAcumulado += $entrada - $saida;
            $entradasPorDia[] = round($entrada, 2);
            $saidasPorDia[] = round($saida, 2);
            $saldoAcumuladoPorDia[] = round($saldoAcumulado, 2);

            return $data->format('d/m');
        })->values();

        $saidasPorCategoria = Lancamento::query()
            ->where('lancamentos.user_id', $request->user()->id)
            ->whereBetween('lancamentos.data_lancamento', [$inicio->toDateString(), $fim->toDateString()])
            ->leftJoin('categorias', 'lancamentos.categoria_id', '=', 'categorias.id')
            ->where('lancamentos.tipo', 'saida')
            ->selectRaw('COALESCE(categorias.nome, ?) as categoria_nome, SUM(lancamentos.valor) as total', ['Sem categoria'])
            ->groupBy('categorias.nome')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $margemPercentual = $totalEntradas > 0
            ? round(($saldoMes / $totalEntradas) * 100, 1)
            : 0;

        return view('dashboard', [
            'mes' => $mes,
            'totalEntradas' => $totalEntradas,
            'totalSaidas' => $totalSaidas,
            'saldoMes' => $saldoMes,
            'quantidadeLancamentos' => (clone $baseQuery)->count(),
            'ultimosLancamentos' => (clone $baseQuery)
                ->with('categoria')
                ->latest('data_lancamento')
                ->latest()
                ->limit(8)
                ->get(),
            'chartData' => [
                'labelsDias' => $labelsDias,
                'entradasPorDia' => $entradasPorDia,
                'saidasPorDia' => $saidasPorDia,
                'saldoAcumuladoPorDia' => $saldoAcumuladoPorDia,
                'categoriasSaida' => $saidasPorCategoria->pluck('categoria_nome')->values(),
                'valoresPorCategoria' => $saidasPorCategoria->pluck('total')->map(fn ($total) => round((float) $total, 2))->values(),
                'margemPercentual' => $margemPercentual,
            ],
        ]);
    }
}
