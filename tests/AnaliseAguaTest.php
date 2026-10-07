<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/AnaliseController.php';

class AnaliseAguaTest extends TestCase
{
    private AnaliseController $analise;

    protected function setUp(): void
    {
        $this->analise = new AnaliseController();
    }

    private function amostraBoa(): array
    {
        return [
            'ph' => 7.0,
            'turbidez' => 2.0,
            'cloro' => 1.0,
            'dureza' => 150.0,
            'cor' => 8.0,
            'temperatura' => 23.0,
        ];
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function amostra_boa_recebe_parecer_potavel(): void
    {
        $saida = $this->analise->avaliar($this->amostraBoa());

        $this->assertTrue($saida['ok']);
        $this->assertCount(6, $saida['itens']);
        $this->assertSame('POTÁVEL', $saida['parecer']['titulo']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function limites_exatos_sao_aceitos(): void
    {
        $saida = $this->analise->avaliar([
            'ph' => 6.0,
            'turbidez' => 5.0,
            'cloro' => 0.2,
            'dureza' => 500.0,
            'cor' => 15.0,
            'temperatura' => 25.0,
        ]);

        $this->assertTrue($saida['ok']);
        $this->assertSame('POTÁVEL', $saida['parecer']['titulo']);

        $saida = $this->analise->avaliar([
            'ph' => 9.5,
            'turbidez' => 5.0,
            'cloro' => 5.0,
            'dureza' => 500.0,
            'cor' => 15.0,
            'temperatura' => 25.0,
        ]);

        $this->assertTrue($saida['ok']);
        $this->assertSame('POTÁVEL', $saida['parecer']['titulo']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function cada_parametro_fora_da_faixa_reprova(): void
    {
        foreach (
            ['ph' => 5.5, 'turbidez' => 9.0, 'cloro' => 0.0, 'dureza' => 600.0, 'cor' => 20.0]
            as $campo => $valor
        ) {
            $dados = $this->amostraBoa();
            $dados[$campo] = $valor;
            $saida = $this->analise->avaliar($dados);

            $this->assertTrue($saida['ok']);
            $this->assertSame('NÃO POTÁVEL', $saida['parecer']['titulo'], "campo {$campo} deveria reprovar");
        }
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function valores_impossiveis_sao_marcados(): void
    {
        $dados = $this->amostraBoa();
        $dados['ph'] = 20.0;
        $saida = $this->analise->avaliar($dados);
        $this->assertSame('inválido', $saida['itens'][0]['situacao']);
        $this->assertSame('NÃO POTÁVEL', $saida['parecer']['titulo']);

        $dados = $this->amostraBoa();
        $dados['turbidez'] = -3.0;
        $saida = $this->analise->avaliar($dados);
        $this->assertSame('inválido', $saida['itens'][1]['situacao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function temperatura_alta_nao_reprova(): void
    {
        $dados = $this->amostraBoa();
        $dados['temperatura'] = 33.0;
        $saida = $this->analise->avaliar($dados);

        $this->assertTrue($saida['ok']);
        $this->assertSame('informativo', $saida['itens'][5]['situacao']);
        $this->assertSame('POTÁVEL', $saida['parecer']['titulo']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function campos_faltando_geram_erro(): void
    {
        $saida = $this->analise->avaliar(['ph' => 7.0]);
        $this->assertFalse($saida['ok']);
        $this->assertNotEmpty($saida['erros']);
        $this->assertNull($saida['parecer']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function texto_nao_numerico_gera_erro(): void
    {
        $dados = $this->amostraBoa();
        $dados['cloro'] = 'muito';
        $saida = $this->analise->avaliar($dados);
        $this->assertFalse($saida['ok']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function parecer_lista_quem_reprovou(): void
    {
        $dados = $this->amostraBoa();
        $dados['dureza'] = 800.0;
        $saida = $this->analise->avaliar($dados);

        $this->assertSame('NÃO POTÁVEL', $saida['parecer']['titulo']);
        $this->assertStringContainsString('Dureza total', $saida['parecer']['texto']);
    }
}
