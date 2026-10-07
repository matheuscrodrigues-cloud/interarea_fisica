<?php
$escapar = static fn($valor): string => htmlspecialchars(
    (string) $valor,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

$numero = static function ($valor): string {
    $texto = rtrim(rtrim(number_format((float) $valor, 4, ',', '.'), '0'), ',');

    return $texto === '-0' ? '0' : $texto;
};

require __DIR__ . '/cabecalho.php';
require __DIR__ . '/' . $pagina['atividade'] . '.php';
require __DIR__ . '/rodape.php';
