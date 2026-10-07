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
    <small class="form-text" id="<?= $id ?>-ajuda" class="<?= $erro !== '' ? 'erro-campo' : '' ?>"><?= $escapar($erro ?: $referencia) ?></small>
</div>
<?php
}; ?>

<h2 class="fs-4 fw-bold mb-3">Analisar amostra</h2>
<p class="introducao mb-4">
    Preencha as medições coletadas. As faixas de referência aparecem junto de cada parâmetro.
</p>
<form method="post" action="?atividade=amostra">
    <div class="grupos d-flex gap-4">
        <?php foreach (
            [
                'Medições físicas' => ['turbidez', 'cor', 'temperatura'],
                'Medições químicas' => ['ph', 'cloro', 'dureza'],
            ]
            as $titulo => $campos
        ): ?>
        <fieldset class="mb-4 flex-fill">
            <legend class="fs-5 fw-bold"><?= $titulo ?></legend>
            <?php foreach ($campos as $campo):
                $regra = $pagina['regras'][$campo] ?? ['nome' => 'Temperatura', 'unidade' => '°C'];
                $referencia =
                    $campo === 'temperatura'
                        ? 'Informativa: não há limite de potabilidade adotado para temperatura.'
                        : 'Referência adotada: ' .
                            $numero($regra['min']) .
                            ' a ' .
                            $numero($regra['max']) .
                            ($regra['unidade'] !== '-' ? ' ' . $regra['unidade'] : '');
                $campoAgua(
                    'medicoes',
                    $campo,
                    $regra['nome'] . ($regra['unidade'] !== '-' ? ' (' . $regra['unidade'] . ')' : ''),
                    $referencia,
                );
            endforeach; ?>
        </fieldset>
        <?php endforeach; ?>
    </div>
    <button class="btn btn-dark" type="submit">Analisar medições</button>
</form>
<?php if ($pagina['erros']): ?>
<p class="aviso alert alert-danger" role="alert">
    Confira os campos indicados. Suas medições foram mantidas.
</p>
<?php endif; ?>
<?php if ($pagina['resultado']):
$resultado = $pagina['resultado']; ?>
<section class="resultado border-top pt-4 mt-4" aria-labelledby="titulo-resultado">
    <h2 class="fs-4 fw-bold mb-3" id="titulo-resultado">Resultado da amostra</h2>
    <p class="parecer fw-bold"><?= $escapar($resultado['parecer']['titulo']) ?></p>
    <p><?= $escapar($resultado['parecer']['texto']) ?></p>
    <div class="rolagem">
        <table class="table table-bordered">
            <caption>Avaliação das medições informadas</caption>
            <thead>
                <tr>
                    <th>Parâmetro</th>
                    <th>Medida</th>
                    <th>Situação</th>
                    <th>Observação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultado['itens'] as $item): ?>
                <tr>
                    <th scope="row"><?= $escapar($item['parametro']) ?></th>
                    <td><?= $numero($item['valor']) ?> <?= $escapar($item['unidade']) ?></td>
                    <td><?= $escapar($item['situacao']) ?></td>
                    <td><?= $escapar($item['detalhe']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php
endif; ?>
<details class="referencia border-top pt-3 mt-4">
    <summary>Sobre as referências utilizadas</summary>
    <p>
        Faixas adotadas neste trabalho a partir da Portaria GM/MS nº 888/2021. O pH usa a faixa de referência
        de 6,0 a 9,5; temperatura é informativa. O parecer se limita aos parâmetros preenchidos nesta
        atividade escolar.
    </p>
</details>
