<?php
$campoAgua = static function (string $grupo, string $campo, string $rotulo, string $referencia = '') use (
    $pagina,
    $escapar,
): void {
    $id = $grupo . '-' . $campo;
    $chave = $grupo . '.' . $campo;
    $erro = $pagina['erros'][$chave] ?? '';
    $valor = $pagina['valores'][$grupo][$campo] ?? '';
    ?>
<div class="campo mb-3">
    <label class="form-label" for="<?= $id ?>"><?= $escapar($rotulo) ?></label>
    <input
        class="form-control"
        id="<?= $id ?>"
        name="<?= $grupo ?>[<?= $campo ?>]"
        type="number"
        step="any"
        required
        <?= $campo === 'ph' ? 'min="0" max="14"' : ($campo === 'temperatura' ? 'min="-5" max="60"' : 'min="0"') ?>
        value="<?= $escapar($valor) ?>"
        aria-describedby="<?= $id ?>-ajuda"
        <?= $erro !== '' ? 'aria-invalid="true"' : '' ?>
    />
    <small class="form-text <?= $erro !== '' ? 'erro-campo text-danger' : '' ?>" id="<?= $id ?>-ajuda"><?= $escapar($erro ?: $referencia) ?></small>
</div>
<?php
}; ?>

<h2 class="fs-4 fw-bold mb-3">Comparar biofiltro</h2>
<p class="introducao mb-4">
    Registre o mesmo parâmetro antes e depois da filtragem. Use as medições do seu experimento.
</p>
<form method="post" action="?atividade=biofiltro">
    <?php foreach ($pagina['regras'] as $campo => $regra): ?>
    <fieldset class="par-medidas border-bottom mb-3 pb-2 mb-4 flex-fill">
        <legend class="fs-5 fw-bold"><?= $escapar($regra['nome']) ?> <?= $regra['unidade'] !== '-' ? '(' . $escapar($regra['unidade']) . ')' : '' ?></legend>
        <div class="grupos d-flex gap-4"><?php
        $campoAgua('antes', $campo, 'Antes da filtragem');
        $campoAgua('depois', $campo, 'Depois da filtragem');
        ?></div>
    </fieldset>
    <?php endforeach; ?>
    <button class="btn btn-dark" type="submit">Comparar medições</button>
</form>
<?php if (
    $pagina['erros']
): ?>
<p class="aviso alert alert-danger" role="alert">
    Confira os campos indicados. Suas medições foram mantidas.
</p>
<?php endif; ?>
<?php if ($pagina['resultado']): ?>
<section class="resultado border-top pt-4 mt-4">
    <h2 class="fs-4 fw-bold mb-3">O que mudou após a filtragem?</h2>
    <dl class="comparacoes mt-3">
        <?php foreach ($pagina['resultado']['linhas'] as $linha): ?>
        <div>
            <dt><?= $escapar($pagina['regras'][$linha['parametro']]['nome']) ?></dt>
            <dd>
                <span><?= $numero($linha['antes']) ?> → <?= $numero($linha['depois']) ?></span>
                <strong><?= $linha[ 'taxa' ] === null ? 'Sem cálculo' : $numero($linha['taxa']) . '%' ?></strong>
                <p><?= $escapar($linha['julgamento']) ?></p>
                <?php if ($linha['nota']): ?>
                <small class="form-text"><?= $escapar($linha['nota']) ?></small>
                <?php endif; ?>
            </dd>
        </div>
        <?php endforeach; ?>
    </dl>
</section>
<?php endif; ?>
<p class="nota small text-secondary mt-4">
    Cálculo: ((antes − depois) ÷ antes) × 100. Medida inicial zero fica sem cálculo. Redução de pH nem sempre
    representa melhoria.
</p>
