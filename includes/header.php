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
    <div class="logo">
        <!-- Sua imagem do SENAC -->
        <img src="imagens/logo-senac.png" alt="Logo SENAC">
    </div>
    
    <div class="titulo">
        <h1>Calendário Acadêmico SENAC</h1>
        <h2>SENAC Minas</h2>
    </div>

    <!-- AQUI ESTÁ O SEU MENU -->
    <div class="menu">
        <a href="?page=home">Home</a>
        <a href="?page=calendario">Calendário</a>
        
        <!-- OS NOVOS BOTÕES AQUI -->
        <a href="?page=eventos">Eventos e UCs</a>
        <a href="?page=legendas">Legendas</a>
        
        <a href="?page=sobre">Sobre</a>
    </div>
</header>