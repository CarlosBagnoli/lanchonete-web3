<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\CategoriaRequest;

class CategoriaController extends Controller
{
    public function exportCsv()
    {
        $fileName = 'categorias.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['nome', 'ativa', 'updated_at']);

            Categoria::orderBy('nome')->chunk(100, function ($categorias) use ($output) {
                foreach ($categorias as $categoria) {
                    fputcsv($output, [
                        $categoria->nome,
                        $categoria->ativa ? 'Sim' : 'Não',
                        $categoria->updated_at ? $categoria->updated_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            });

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

   public function index()
{
    $q = request('q');

    $categorias = Categoria::query()
        ->when($q, function ($query) use ($q) {
            $query->where('nome', 'like', "%{$q}%");
        })
        ->orderBy('nome')
        ->paginate(10)
        ->withQueryString();

    return view('categorias.index', compact('categorias', 'q'));
}

    public function create()
    {
        return view('categorias.create');
    }

    public function store(CategoriaRequest $request)
    {
        $dados = $request->validated();
        $dados['ativa'] = $request->boolean('ativa');

        Categoria::create($dados);

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria criada com sucesso!');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $dados = $request->validated();
        $dados['ativa'] = $request->boolean('ativa');

        $categoria->update($dados);

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria excluída com sucesso!');
    }
}