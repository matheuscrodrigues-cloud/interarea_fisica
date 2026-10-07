<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../Controller/PaginaAguaController.php';
final class PaginaAguaTest extends TestCase
{
    public function testNavegacaoEValoresIniciais(): void
    {
        $controle = new PaginaAguaController();
        self::assertSame(
            'amostra',
            $controle->preparar(['atividade' => 'inexistente'], [], 'GET')['atividade'],
        );
        self::assertSame(
            'biofiltro',
            $controle->preparar(['atividade' => 'biofiltro'], [], 'GET')['atividade'],
        );
        self::assertNull($controle->preparar([], [], 'GET')['resultado']);
    }
    public function testAmostraCompletaEValoresPreservados(): void
    {
        $medicoes = [
            'ph' => '7',
            'turbidez' => '2',
            'cor' => '4',
            'cloro' => '1',
            'dureza' => '100',
            'temperatura' => '26',
        ];
        $pagina = new PaginaAguaController()->preparar([], ['medicoes' => $medicoes], 'POST');
        self::assertSame([], $pagina['erros']);
        foreach ($medicoes as $campo => $valor) {
            self::assertSame($valor, $pagina['valores']['medicoes'][$campo]);
        }
        self::assertSame('POTÁVEL', $pagina['resultado']['parecer']['titulo']);
    }
    public function testErrosPorCampoSemPerderValores(): void
    {
        $pagina = new PaginaAguaController()->preparar(
            [],
            ['medicoes' => ['ph' => '15', 'cor' => 'abc', 'turbidez' => '-2', 'cloro' => '1']],
            'POST',
        );
        self::assertArrayHasKey('medicoes.ph', $pagina['erros']);
        self::assertArrayHasKey('medicoes.cor', $pagina['erros']);
        self::assertArrayHasKey('medicoes.turbidez', $pagina['erros']);
        self::assertSame('1', $pagina['valores']['medicoes']['cloro']);
        self::assertNull($pagina['resultado']);
    }
    public function testEntradaMalformadaNaoQuebraPagina(): void
    {
        $pagina = new PaginaAguaController()->preparar(
            [],
            ['medicoes' => ['ph' => [], 'cor' => '1e999']],
            'POST',
        );
        self::assertArrayHasKey('medicoes.ph', $pagina['erros']);
        self::assertArrayHasKey('medicoes.cor', $pagina['erros']);
    }
    public function testBiofiltroComZeroEAumento(): void
    {
        $antes = array_fill_keys(['ph', 'turbidez', 'cloro', 'dureza', 'cor'], '10');
        $depois = array_fill_keys(array_keys($antes), '5');
        $antes['turbidez'] = '0';
        $depois['cor'] = '12';
        $pagina = new PaginaAguaController()->preparar(
            ['atividade' => 'biofiltro'],
            compact('antes', 'depois'),
            'POST',
        );
        self::assertSame([], $pagina['erros']);
        $linhas = array_column($pagina['resultado']['linhas'], null, 'parametro');
        self::assertNull($linhas['turbidez']['taxa']);
        self::assertEquals(-20, $linhas['cor']['taxa']);
    }
}
