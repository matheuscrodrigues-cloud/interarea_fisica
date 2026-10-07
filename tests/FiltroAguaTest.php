<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/FiltroController.php';

class FiltroAguaTest extends TestCase
{
    private FiltroController $filtro;

    protected function setUp(): void
    {
        $this->filtro = new FiltroController();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function taxa_conhecida_bate_com_o_calculo(): void
    {
        $this->assertEqualsWithDelta(80.0, $this->filtro->remocao(10.0, 2.0), 0.0001);
        $this->assertEqualsWithDelta(50.0, $this->filtro->remocao(8.0, 4.0), 0.0001);
        $this->assertEqualsWithDelta(0.0, $this->filtro->remocao(3.0, 3.0), 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function inicio_zerado_nao_calcula(): void
    {
        $this->assertNull($this->filtro->remocao(0.0, 0.0));
        $this->assertSame('sem cálculo (medida inicial zerada)', $this->filtro->julgar(null));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function negativo_nao_e_aceito(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filtro->remocao(5.0, -1.0);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function julgamento_por_faixa(): void
    {
        $this->assertSame('aumentou depois do filtro', $this->filtro->julgar(-10.0));
        $this->assertSame('efeito pequeno', $this->filtro->julgar(5.0));
        $this->assertSame('efeito médio', $this->filtro->julgar(45.0));
        $this->assertSame('bom efeito', $this->filtro->julgar(90.0));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function comparacao_traz_cinco_linhas(): void
    {
        $saida = $this->filtro->compararAntesDepois(
            ['ph' => 7.5, 'turbidez' => 12.0, 'cloro' => 2.0, 'dureza' => 250.0, 'cor' => 18.0],
            ['ph' => 7.0, 'turbidez' => 3.0, 'cloro' => 1.0, 'dureza' => 200.0, 'cor' => 6.0],
        );

        $this->assertTrue($saida['ok']);
        $this->assertCount(5, $saida['linhas']);
        $this->assertEqualsWithDelta(75.0, $saida['linhas'][1]['taxa'], 0.0001);
        $this->assertSame('bom efeito', $saida['linhas'][1]['julgamento']);
        $this->assertNotSame('', $saida['linhas'][0]['nota']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function falta_de_dado_impede_a_comparacao(): void
    {
        $saida = $this->filtro->compararAntesDepois(['ph' => 7.0], []);
        $this->assertFalse($saida['ok']);
        $this->assertNotEmpty($saida['erros']);
    }
}
