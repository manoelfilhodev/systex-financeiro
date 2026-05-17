<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(Request $request): View
    {
        return view('categorias.index', [
            'categorias' => Categoria::query()
                ->where('user_id', $request->user()->id)
                ->withCount('lancamentos')
                ->orderBy('nome')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;

        Categoria::create($data);

        return back()->with('success', 'Categoria criada com sucesso.');
    }

    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $this->ensureOwner($request, $categoria);

        $categoria->update($this->validated($request));

        return back()->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Request $request, Categoria $categoria): RedirectResponse
    {
        $this->ensureOwner($request, $categoria);

        $categoria->delete();

        return back()->with('success', 'Categoria removida com sucesso.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'tipo' => ['required', Rule::in(['entrada', 'saida', 'ambos'])],
            'cor' => ['nullable', 'string', 'max:20'],
        ]);
    }

    private function ensureOwner(Request $request, Categoria $categoria): void
    {
        abort_unless($categoria->user_id === $request->user()->id, 404);
    }
}
