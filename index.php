<?php
require_once __DIR__ . '/includes/conexao.php';

/*********************************************************************
 * PROJETO:
 * Calendário Acadêmico SENAC
 *
 * ARQUIVO:
 * index.php
 *
 * OBJETIVO:
 * Este arquivo é o ponto de entrada do sistema.
 * Todo acesso ao sistema será ser feito por ele.
 *
 * Nesta página o layout é montado utilizando arquivos
 * separados (includes).
 *********************************************************************/

// Obtém a página solicitada
$page = $_GET['page'] ?? 'home';

// Lista de páginas permitidas
$allowedPages = ['home', 'calendario', 'sobre'];

// Verifica se a página é permitida
if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Define os caracteres utilizados -->
    <meta charset="UTF-8">

    <!-- Faz o site funcionar corretamente em celulares -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Nome exibido na aba do navegador -->
    <title>Calendário Acadêmico SENAC</title>

    <!-- Arquivo CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
/*======================================================
  Cabeçalho
======================================================*/
include __DIR__ . "/includes/header.php";

/*======================================================
  Conteúdo principal
======================================================*/
$pageFile = __DIR__ . "/pages/{$page}.php";

if (file_exists($pageFile)) {
    include $pageFile;
} else {
    include __DIR__ . "/pages/home.php";
}

/*======================================================
  Rodapé
======================================================*/
include __DIR__ . "/includes/footer.php";
?>

<!-- Arquivo JavaScript -->
<script src="js/script.js"></script>

</body>
</html>