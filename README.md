# Água Limpa — Derick e Matheus
Trabalho interárea SENAI. PHP >= 8.4, HTML5, CSS3 e Bootstrap 5.3.8, Laravel Herd e PHPUnit. Bootstrap CSS via CDN; sem JavaScript, banco de dados ou framework PHP.

## Instalação
1. Instale PHP 8.4 ou superior, Composer e Laravel Herd.
2. Na pasta desta aplicação, execute `composer install` com PHP 8.4 e confira `php -v`.
3. Para medir cobertura, habilite Xdebug compatível com a versão de PHP. Neste computador, PHP 8.4.26 e Xdebug 3.5.3 estão instalados em `%LOCALAPPDATA%\Programs\PHP84`; o caminho do executável é `%LOCALAPPDATA%\Programs\PHP84\php.exe`.

No PowerShell deste computador, para usar explicitamente a versão instalada:
```powershell
$php84 = "$env:LOCALAPPDATA\Programs\PHP84\php.exe"
& $php84 -v
& $php84 C:\php83\composer.phar install
```
O caminho do Composer acima pertence a este computador; em outra máquina, use a instalação local do Composer com PHP >= 8.4.

## Executar no Laravel Herd
No gerenciador de sites do Herd, adicione um projeto existente e selecione esta pasta, que contém index.php. Escolha PHP 8.4 ou superior para o site. Como nome sugerido, use `agua-derick` e abra `http://agua-derick.test` se esse nome estiver configurado. Use o domínio mostrado pelo Herd caso ele seja diferente.
O usuário confirmou que as aplicações funcionam no Herd. A validação automatizada adicional foi feita com servidor PHP local.
Alternativa para prévia:
```powershell
& "$env:LOCALAPPDATA\Programs\PHP84\php.exe" -S 127.0.0.1:8002
```
Endereço atual: http://127.0.0.1:8002/.

## Algoritmos implementados
- **Classificação da amostra:** Valida entradas e aplica uma tabela de referências aos parâmetros; agrega as classificações no parecer.
- **pH:** Escala de entrada 0–14; faixa adotada 6,0–9,5.
- **Turbidez, cor e dureza:** Limites adotados: 5 uT, 15 uH e 500 mg/L CaCO3, respectivamente.
- **Cloro residual:** Faixa adotada: 0,2–5,0 mg/L.
- **Temperatura:** Dado informativo; validação de entrada entre −5 e 60 °C, distinta de limite de potabilidade.
- **Biofiltro:** ((antes − depois) / antes) × 100. Início zero fica sem cálculo; taxa negativa indica aumento.
- **Interpretação da remoção:** Faixas didáticas existentes: abaixo de 20%, efeito pequeno; abaixo de 60%, efeito médio; a partir de 60%, bom efeito. Para pH, redução não significa automaticamente melhoria.

## Organização e uso
Controller contém as classes de cálculo e um controlador de página. index.php delega o processamento; View contém as telas separadas por atividade e o cabeçalho/rodapé; templates/css contém o estilo próprio. Testes em tests e documentação em docs.
Escolha Analisar amostra ou Comparar biofiltro. A amostra agrupa medições físicas e químicas; o biofiltro recebe pares antes/depois.
Resultado abaixo da atividade, erros junto dos campos, valores mantidos após envio. Sem persistência entre atividades.

## Testes e cobertura
Execução simples: `php vendor/bin/phpunit --do-not-cache-result` (PHP >= 8.4).
Execução com cobertura e verificação automática do mínimo de 80%, neste computador:
```powershell
.\testar.ps1
```
Em outra instalação: `.\testar.ps1 -PHP "C:\caminho\php.exe"`, com Xdebug habilitado. O script ativa o modo coverage apenas nessa execução.
Comando equivalente em qualquer terminal com PHP >= 8.4 e Xdebug:
```text
php -d xdebug.mode=coverage vendor/bin/phpunit --do-not-cache-result --coverage-html docs/evidencias/cobertura --coverage-clover docs/evidencias/cobertura.xml --coverage-text=docs/evidencias/cobertura.txt
php tests/verificar-cobertura.php docs/evidencias/cobertura.xml
```
Resultado real em 06/10/2026: **OK (19 tests, 67 assertions)**, PHP 8.4.26, PHPUnit 12.5.38, Xdebug 3.5.3.
Cobertura de linhas executáveis dos algoritmos: **97.37% (111/114)**. A medição exclui controlador de página, views e dependências. Os percentuais de métodos/classes no relatório representam cobertura integral dessas unidades; o critério aqui é cobertura de linhas.

| Classe | Linhas cobertas/total | Cobertura |
|---|---:|---:|
| AnaliseController | 72/73 | 98.63% |
| FiltroController | 39/41 | 95.12% |

[Relatório HTML](docs/evidencias/cobertura/index.html) · [Resumo de cobertura](docs/evidencias/cobertura.txt) · [Clover XML](docs/evidencias/cobertura.xml) · [Execução dos testes](docs/evidencias/testes.txt).
Casos conhecidos, bordas, entradas inválidas e precisão numérica constam da suíte. A interface também foi conferida em desktop, celular e por teclado.

## Relatório técnico
Estrutura ABNT: folha de rosto, resumo, sumário, três páginas de conteúdo técnico e referências; sete páginas no total.
[PDF em formato ABNT](docs/relatorio-tecnico.pdf) · [Markdown](docs/relatorio-tecnico.md) · [DOCX editável](docs/relatorio-tecnico.docx).
Não foram disponibilizadas amostras ou medições para o grupo. Não há dataset real. Valores dos testes e capturas são simulados e identificados como tal; não são resultados experimentais. A ausência de coleta é explicada no relatório. Git e GitHub serão tratados separadamente.

## Referências de instalação
[PHP oficial](https://www.php.net/downloads.php?os=windows&version=8.4) · [Xdebug oficial](https://xdebug.org/download) · [Sites no Herd](https://latest.herdphp.com/docs/windows/1/getting-started/sites).

## Estilização atual
Controles e espaçamentos usam classes Bootstrap 5.3.8 (CSS via CDN, requer internet). Layout fixo para computador, sem adaptações próprias para celular. Arial na interface. Nenhuma pasta removida foi recriada.
