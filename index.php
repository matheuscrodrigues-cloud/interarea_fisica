<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller/PaginaAguaController.php';
$pagina = new PaginaAguaController()->preparar($_GET, $_POST, $_SERVER['REQUEST_METHOD']);
require __DIR__ . '/View/pagina.php';
