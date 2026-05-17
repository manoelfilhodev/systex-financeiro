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

        return view('dashboard', [
            'mes' => $mes,
            'totalEntradas' => $totalEntradas,
            'totalSaidas' => $totalSaidas,
            'saldoMes' => $totalEntradas - $totalSaidas,
            'quantidadeLancamentos' => (clone $baseQuery)->count(),
            'ultimosLancamentos' => (clone $baseQuery)
                ->with('categoria')
                ->latest('data_lancamento')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
