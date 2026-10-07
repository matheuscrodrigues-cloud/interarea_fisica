<?php

declare(strict_types=1);

class FiltroController
{
    public function remocao(float $antes, float $depois): ?float
    {
        if ($antes < 0 || $depois < 0) {
            throw new InvalidArgumentException('Medida negativa não existe na análise da água.');
        }
        if ($antes == 0.0) {
            return null;
        }
        return (($antes - $depois) / $antes) * 100;
    }

    public function julgar(?float $taxa): string
    {
        if ($taxa === null) {
            return 'sem cálculo (medida inicial zerada)';
        }
        if ($taxa < 0) {
            return 'aumentou depois do filtro';
        }
        if ($taxa < 20) {
            return 'efeito pequeno';
        }
        if ($taxa < 60) {
            return 'efeito médio';
        }
        return 'bom efeito';
    }

    public function compararAntesDepois(array $antes, array $depois): array
    {
        $parametros = ['ph', 'turbidez', 'cloro', 'dureza', 'cor'];
        $erros = [];
        foreach ($parametros as $campo) {
            foreach (['antes' => $antes, 'depois' => $depois] as $fase => $grupo) {
                $valor = $grupo[$campo] ?? null;
                if ($valor === null || $valor === '') {
                    $erros[] = "Informe {$campo} ({$fase}).";
                } elseif (!is_numeric($valor)) {
                    $erros[] = "O valor de {$campo} ({$fase}) deve ser numérico.";
                } elseif ((float) $valor < 0) {
                    $erros[] = "O valor de {$campo} ({$fase}) não pode ser negativo.";
                }
            }
        }
        if ($erros !== []) {
            return ['ok' => false, 'erros' => $erros, 'linhas' => []];
        }

        $linhas = [];
        foreach ($parametros as $campo) {
            $medidaAntes = (float) $antes[$campo];
            $medidaDepois = (float) $depois[$campo];
            $taxa = $this->remocao($medidaAntes, $medidaDepois);
            $linhas[] = [
                'parametro' => $campo,
                'antes' => $medidaAntes,
                'depois' => $medidaDepois,
                'taxa' => $taxa,
                'julgamento' => $this->julgar($taxa),
                'nota' => $campo === 'ph' ? 'pH tem faixa ideal: nem toda redução é melhoria.' : '',
            ];
        }
        return ['ok' => true, 'erros' => [], 'linhas' => $linhas];
    }
}
