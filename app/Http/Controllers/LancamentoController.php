<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Lancamento;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LancamentoController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes = $validated['mes'] ?? now()->format('Y-m');

        return view('lancamentos.index', [
            'mes' => $mes,
            'lancamentos' => Lancamento::query()
                ->where('user_id', $request->user()->id)
                ->whereBetween('data_lancamento', [
                    $mes.'-01',
                    CarbonImmutable::createFromFormat('Y-m-d', $mes.'-01')->endOfMonth()->toDateString(),
                ])
                ->with('categoria')
                ->latest('data_lancamento')
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('lancamentos.create', [
            'categorias' => $this->categoriasDoUsuario($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;

        Lancamento::create($data);

        return redirect()->route('lancamentos.index')->with('success', 'Lançamento criado com sucesso.');
    }

    public function edit(Request $request, Lancamento $lancamento): View
    {
        $this->ensureOwner($request, $lancamento);

        return view('lancamentos.edit', [
            'lancamento' => $lancamento,
            'categorias' => $this->categoriasDoUsuario($request),
        ]);
    }

    public function update(Request $request, Lancamento $lancamento): RedirectResponse
    {
        $this->ensureOwner($request, $lancamento);

        $lancamento->update($this->validated($request));

        return redirect()->route('lancamentos.index')->with('success', 'Lançamento atualizado com sucesso.');
    }

    public function destroy(Request $request, Lancamento $lancamento): RedirectResponse
    {
        $this->ensureOwner($request, $lancamento);

        $lancamento->delete();

        return back()->with('success', 'Lançamento removido com sucesso.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'tipo' => ['required', Rule::in(['entrada', 'saida'])],
            'descricao' => ['required', 'string', 'max:180'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'data_lancamento' => ['required', 'date'],
            'categoria_id' => [
                'nullable',
                Rule::exists('categorias', 'id')->where('user_id', $request->user()->id),
            ],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function categoriasDoUsuario(Request $request)
    {
        return Categoria::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('nome')
            ->get();
    }

    private function ensureOwner(Request $request, Lancamento $lancamento): void
    {
        abort_unless($lancamento->user_id === $request->user()->id, 404);
    }
}
