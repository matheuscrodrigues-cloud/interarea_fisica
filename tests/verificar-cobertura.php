<?php
declare(strict_types=1);
// A cobertura mede exclusivamente as classes de cálculo listadas em phpunit.xml.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
$arquivo = $argv[1] ?? '';
if (!is_file($arquivo)) {
    fwrite(STDERR, "Relatório de cobertura não encontrado. Ative o Xdebug em modo coverage.\n");
    exit(1);
}
$relatorio = simplexml_load_file($arquivo);
if ($relatorio === false) {
    fwrite(STDERR, "Relatório de cobertura inválido.\n");
    exit(1);
}
$metricas = $relatorio->project->metrics;
$linhas = (int) $metricas['statements'];
$cobertas = (int) $metricas['coveredstatements'];
$percentual = $linhas > 0 ? (100 * $cobertas) / $linhas : 0;
printf(
    "Cobertura de linhas dos algoritmos: %.2f%% (%d/%d). Mínimo: 80%%.\n",
    $percentual,
    $cobertas,
    $linhas,
);
exit($percentual >= 80 ? 0 : 1);
