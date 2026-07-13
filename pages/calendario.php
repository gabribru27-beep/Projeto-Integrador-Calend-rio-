<?php
require_once __DIR__ . '/../includes/conexao.php';
?>

<!--========================================================
Página do calendário
Esta página mostra o calendário do mês atual isoladamente.
========================================================-->
<main>
    <section class="apresentacao">
        <h2>Calendário</h2>
        <p>Veja abaixo o calendário acadêmico do mês atual:</p>
    </section>

    <?php include __DIR__ . '/../includes/calendario.php'; ?>
</main>
