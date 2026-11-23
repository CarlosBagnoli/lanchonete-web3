<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function show(string $cep)
    {
        // Normaliza o parâmetro antes de validar e consultar
        $cep = preg_replace('/\D/', '', $cep) ?? '';

        if (! preg_match('/^\d{8}$/', $cep)) {
            return response()->json([
                'message' => 'CEP inválido. Informe 8 números válidos.',
                'cached' => false,
                'errors' => [
                    'cep' => ['CEP deve conter exatamente 8 números válidos.'],
                ],
            ], 422);
        }

        $cacheKey = "cep:$cep";
        $cached = Cache::has($cacheKey);
        $ttlMinutes = max(1, (int) config('services.cep.cache_ttl', 15));

        $data = Cache::remember($cacheKey, now()->addMinutes($ttlMinutes), function () use ($cep) {
            $resp = Http::timeout(3)->acceptJson()
                ->get("https://viacep.com.br/ws/{$cep}/json/");

            if (! $resp->ok()) {
                return ['_error' => 'Falha ao consultar serviço de CEP.'];
            }

            $json = $resp->json();

            if (isset($json['erro']) && $json['erro'] === true) {
                return ['_error' => 'CEP não encontrado.'];
            }

            return $json;
        });

        $keys = Cache::get('cep:keys', []);
        if (! in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;
            Cache::put('cep:keys', $keys, now()->addDays(7));
        }

        if (isset($data['_error'])) {
            return response()->json([
                'message' => $data['_error'],
                'cached' => $cached,
                'errors' => ['cep' => [$data['_error']]],
            ], 404);
        }

        return response()->json([
            'message' => $cached ? 'CEP encontrado em cache!' : 'CEP encontrado!',
            'cached' => $cached,
            'cep' => $data['cep'] ?? $cep,
            'logradouro' => $data['logradouro'] ?? '',
            'bairro' => $data['bairro'] ?? '',
            'localidade' => $data['localidade'] ?? '',
            'uf' => $data['uf'] ?? '',
            'ibge' => $data['ibge'] ?? null,
        ], 200);
    }

    public function clearCache()
    {
        $keys = Cache::get('cep:keys', []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget('cep:keys');

        return redirect()->back()->with('sucesso', 'Cache do CEP limpo com sucesso.');
    }
}