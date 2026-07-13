<?php
require_once __DIR__ . '/conexao.php';
$tituloSistema = obterConfiguracao('titulo_sistema', 'Calendário Acadêmico SENAC');
?>

<!--========================================================
Cabeçalho do sistema
Este arquivo será utilizado em todas as páginas.
Caso futuramente seja necessário alterar o cabeçalho,
basta modificar apenas este arquivo.
========================================================-->
<header>
    <!-- Área do logotipo -->
    <div class="logo">
        <img
            src="imagens/logo-senac.png"
            alt="Logo SENAC">
    </div>

    <!-- Área dos títulos -->
    <div class="titulo">
        <h1>
            <?php echo htmlspecialchars($tituloSistema); ?>
        </h1>
        <h2>
            SENAC Minas
        </h2>
    </div>

    <!-- Menu de navegação -->
    <nav class="menu">
        <a href="index.php?page=home">Home</a>
        <a href="index.php?page=calendario">Calendário</a>
        <a href="index.php?page=sobre">Sobre</a>
    </nav>
</header>
