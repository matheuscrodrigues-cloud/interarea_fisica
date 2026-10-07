<?php
declare(strict_types=1);
require_once __DIR__ . '/AnaliseController.php';
require_once __DIR__ . '/FiltroController.php';

final class PaginaAguaController
{
    public function preparar(array $consulta, array $entrada, string $metodo): array
    {
        $atividade = ($consulta['atividade'] ?? '') === 'biofiltro' ? 'biofiltro' : 'amostra';
        $regras = new AnaliseController()->tabela();
        $pagina = [
            'atividade' => $atividade,
            'regras' => $regras,
            'valores' => [],
            'erros' => [],
            'resultado' => null,
        ];
        if ($metodo !== 'POST') {
            return $pagina;
        }
        $grupos = $atividade === 'amostra' ? ['medicoes'] : ['antes', 'depois'];
        $campos = array_keys($regras);
        if ($atividade === 'amostra') {
            $campos[] = 'temperatura';
        }
        foreach ($grupos as $grupo) {
            $dados = is_array($entrada[$grupo] ?? null) ? $entrada[$grupo] : [];
            foreach ($campos as $campo) {
                $valor = $dados[$campo] ?? '';
                $chave = $grupo . '.' . $campo;
                $pagina['valores'][$grupo][$campo] = is_scalar($valor) ? (string) $valor : '';
                if (
                    !is_scalar($valor) ||
                    trim((string) $valor) === '' ||
                    !is_numeric($valor) ||
                    !is_finite((float) $valor)
                ) {
                    $pagina['erros'][$chave] = 'Informe um número válido.';
                } elseif ($campo === 'ph' && ((float) $valor < 0 || (float) $valor > 14)) {
                    $pagina['erros'][$chave] = 'O pH deve estar entre 0 e 14.';
                } elseif ($campo !== 'temperatura' && (float) $valor < 0) {
                    $pagina['erros'][$chave] = 'A medida não pode ser negativa.';
                } elseif ($campo === 'temperatura' && ((float) $valor < -5 || (float) $valor > 60)) {
                    $pagina['erros'][$chave] = 'Informe temperatura entre −5 e 60 °C.';
                }
            }
        }
        if ($pagina['erros'] !== []) {
            return $pagina;
        }
        $pagina['resultado'] =
            $atividade === 'amostra'
                ? new AnaliseController()->avaliar($pagina['valores']['medicoes'])
                : new FiltroController()->compararAntesDepois(
                    $pagina['valores']['antes'],
                    $pagina['valores']['depois'],
                );
        return $pagina;
    }
}
