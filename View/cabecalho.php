<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Água Limpa — Derick e Matheus</title>
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        />

        <link rel="stylesheet" href="css/style.css?v=<?= substr(hash_file('sha256', __DIR__ . '/../css/style.css'), 0, 12) ?>" />
    </head>
    <body>
        <a class="pular visually-hidden-focusable" href="#atividade">Ir para a atividade</a>
        <div class="folha mx-auto my-4 p-5 bg-white">
            <header class="border-bottom pb-3 mb-3">
                <h1 class="fs-2 fw-bold">Água Limpa</h1>
                <p>Ficha de análise da água e comparação do biofiltro</p>
                <p class="autoria small text-secondary">Derick e Matheus · SENAI · Trabalho interárea</p>
            </header>
            <nav class="nav gap-3 border-bottom mb-4" aria-label="Atividades">
                <a href="?atividade=amostra" <?= $pagina['atividade'] === 'amostra' ? 'aria-current="page"' : '' ?>>Analisar amostra</a>
                <a href="?atividade=biofiltro" <?= $pagina['atividade'] === 'biofiltro' ? 'aria-current="page"' : '' ?>>Comparar biofiltro</a>
            </nav>
            <main id="atividade">
