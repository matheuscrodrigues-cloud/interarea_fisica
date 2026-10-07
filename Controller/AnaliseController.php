<?php

declare(strict_types=1);

class AnaliseController
{
    public function tabela(): array
    {
        return [
            'ph' => ['nome' => 'pH', 'unidade' => '-', 'min' => 6.0, 'max' => 9.5],
            'turbidez' => ['nome' => 'Turbidez', 'unidade' => 'uT', 'min' => 0.0, 'max' => 5.0],
            'cloro' => ['nome' => 'Cloro residual', 'unidade' => 'mg/L', 'min' => 0.2, 'max' => 5.0],
            'dureza' => ['nome' => 'Dureza total', 'unidade' => 'mg/L CaCO3', 'min' => 0.0, 'max' => 500.0],
            'cor' => ['nome' => 'Cor aparente', 'unidade' => 'uH', 'min' => 0.0, 'max' => 15.0],
        ];
    }

    public function avaliar(array $medicoes): array
    {
        $erros = $this->conferirPreenchimento($medicoes);
        if ($erros !== []) {
            return ['ok' => false, 'erros' => $erros, 'itens' => [], 'parecer' => null];
        }

        $itens = [];
        foreach ($this->tabela() as $chave => $regra) {
            $itens[] = $this->avaliarParametro($chave, (float) $medicoes[$chave], $regra);
        }
        $itens[] = $this->avaliarTemperatura((float) $medicoes['temperatura']);

        return [
            'ok' => true,
            'erros' => [],
            'itens' => $itens,
            'parecer' => $this->parecer($itens),
        ];
    }

    public function parecer(array $itens): array
    {
        $parametrosForaDoPadrao = [];
        foreach ($itens as $item) {
            if ($item['situacao'] === 'inadequado' || $item['situacao'] === 'inválido') {
                $parametrosForaDoPadrao[] = $item['parametro'];
            }
        }
        if ($parametrosForaDoPadrao === []) {
            return [
                'titulo' => 'POTÁVEL',
                'texto' => 'Amostra dentro dos padrões da Portaria GM/MS nº 888/2021.',
            ];
        }
        return [
            'titulo' => 'NÃO POTÁVEL',
            'texto' =>
                'Fora do padrão: ' . implode(', ', $parametrosForaDoPadrao) . '. Não consumir sem tratar.',
        ];
    }

    private function conferirPreenchimento(array $medicoes): array
    {
        $erros = [];
        foreach (array_keys($this->tabela()) as $chave) {
            $erros = array_merge($erros, $this->conferirNumero($medicoes[$chave] ?? null, $chave));
        }
        return array_merge($erros, $this->conferirNumero($medicoes['temperatura'] ?? null, 'temperatura'));
    }

    private function conferirNumero(mixed $valor, string $campo): array
    {
        if ($valor === null || $valor === '') {
            return ["Preencha o campo {$campo}."];
        }
        if (!is_numeric($valor) || is_nan((float) $valor) || is_infinite((float) $valor)) {
            return ["O campo {$campo} deve conter um número válido."];
        }
        return [];
    }

    private function avaliarParametro(string $chave, float $valor, array $regra): array
    {
        if ($chave === 'ph' && ($valor < 0 || $valor > 14)) {
            return $this->item(
                $regra['nome'],
                $valor,
                $regra['unidade'],
                'inválido',
                'pH impossível: a escala vai de 0 a 14.',
            );
        }
        if ($valor < 0) {
            return $this->item(
                $regra['nome'],
                $valor,
                $regra['unidade'],
                'inválido',
                'Valor negativo impossível para este parâmetro.',
            );
        }
        if ($valor < $regra['min'] || $valor > $regra['max']) {
            return $this->item(
                $regra['nome'],
                $valor,
                $regra['unidade'],
                'inadequado',
                "Fora da faixa {$regra['min']} a {$regra['max']} {$regra['unidade']}.",
            );
        }
        return $this->item(
            $regra['nome'],
            $valor,
            $regra['unidade'],
            'adequado',
            "Dentro da faixa {$regra['min']} a {$regra['max']} {$regra['unidade']}.",
        );
    }

    private function avaliarTemperatura(float $valor): array
    {
        if ($valor < -5 || $valor > 60) {
            return $this->item(
                'Temperatura',
                $valor,
                '°C',
                'inválido',
                'Temperatura impossível para análise em ambiente.',
            );
        }
        if ($valor > 25) {
            return $this->item(
                'Temperatura',
                $valor,
                '°C',
                'informativo',
                'Acima de 25 °C: dado informativo, não reprova a amostra.',
            );
        }
        return $this->item(
            'Temperatura',
            $valor,
            '°C',
            'informativo',
            'Temperatura em condição normal de análise.',
        );
    }

    private function item(
        string $parametro,
        float $valor,
        string $unidade,
        string $situacao,
        string $detalhe,
    ): array {
        return [
            'parametro' => $parametro,
            'valor' => $valor,
            'unidade' => $unidade,
            'situacao' => $situacao,
            'detalhe' => $detalhe,
        ];
    }
}
