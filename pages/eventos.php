<?php
// Inicializa a sessão para salvar temporariamente os eventos e as UCs
session_start();

// 1. DADOS PADRÃO (Para o calendário não iniciar totalmente vazio)
if (!isset($_SESSION['eventos'])) {
    $_SESSION['eventos'] = [
        '2026-01-01' => ['nome' => 'Ano Novo', 'tipo' => 'feriado'],
        '2026-04-03' => ['nome' => 'Sexta-feira Santa', 'tipo' => 'feriado'],
        '2026-05-01' => ['nome' => 'Dia do Trabalho', 'tipo' => 'feriado'],
        '2026-06-24' => ['nome' => 'São João (Recesso)', 'tipo' => 'recesso'],
    ];
}

if (!isset($_SESSION['ucs'])) {
    $_SESSION['ucs'] = [
        [
            'nome' => 'Planejamento de Sistemas',
            'inicio' => '2026-01-10',
            'fim' => '2026-02-28',
            'cor' => '#F58220' // Laranja SENAC
        ],
        [
            'nome' => 'Banco de Dados',
            'inicio' => '2026-02-15',
            'fim' => '2026-04-10',
            'cor' => '#005CA9' // Azul Claro SENAC
        ]
    ];
}

// 2. PROCESSAMENTO DOS FORMULÁRIOS (POST)

// A. Cadastrar Evento (Feriado ou Recesso)
if (isset($_POST['adicionar_evento'])) {
    $data = $_POST['data_evento'];
    $nome = $_POST['nome_evento'];
    $tipo = $_POST['tipo_evento'];

    if (!empty($data) && !empty($nome)) {
        $_SESSION['eventos'][$data] = [
            'nome' => $nome,
            'tipo' => $tipo
        ];
        $mensagem = "Evento '$nome' adicionado com sucesso!";
    }
}

// B. Cadastrar Unidade Curricular (UC)
if (isset($_POST['adicionar_uc'])) {
    $nome_uc = $_POST['nome_uc'];
    $inicio_uc = $_POST['inicio_uc'];
    $fim_uc = $_POST['fim_uc'];
    $cor_uc = $_POST['cor_uc'];

    if (!empty($nome_uc) && !empty($inicio_uc) && !empty($fim_uc)) {
        $_SESSION['ucs'][] = [
            'nome' => $nome_uc,
            'inicio' => $inicio_uc,
            'fim' => $fim_uc,
            'cor' => $cor_uc
        ];
        $mensagem = "UC '$nome_uc' cadastrada com sucesso!";
    }
}

// C. Limpar dados da Sessão (Botão de Reset)
if (isset($_POST['limpar_dados'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// 3. FUNÇÃO AUXILIAR PARA GERAR O MÊS NO CALENDÁRIO
function desenhar_mes($mes, $ano, $eventos, $ucs) {
    $primeiro_dia_mes = mktime(0, 0, 0, $mes, 1, $ano);
    $total_dias = date('t', $primeiro_dia_mes);
    $dia_semana_inicio = date('w', $primeiro_dia_mes); // 0 (domingo) a 6 (sábado)
    
    $nomes_meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];

    echo "<div class='mes'>";
    echo "<h2>" . $nomes_meses[$mes] . " " . $ano . "</h2>";
    
    // Cabeçalho dos dias da semana
    echo "<div class='dias-semana'>";
    echo "<div>D</div><div>S</div><div>T</div><div>Q</div><div>Q</div><div>S</div><div>S</div>";
    echo "</div>";

    echo "<div class='dias'>";

    // Dias vazios no início do mês
    for ($i = 0; $i < $dia_semana_inicio; $i++) {
        echo "<div class='dia vazio'></div>";
    }

    // Dias reais do mês
    for ($dia = 1; $dia <= $total_dias; $dia++) {
        $data_atual = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
        $dia_semana = date('w', mktime(0, 0, 0, $mes, $dia, $ano));
        
        $classes = ['dia'];
        $tooltip = "";
        $estilo_uc = "";

        // Regra 1: Fim de semana
        if ($dia_semana == 0 || $dia_semana == 6) {
            $classes[] = 'fim-semana';
        }

        // Regra 2: Eventos personalizados (Feriado ou Recesso)
        if (isset($eventos[$data_atual])) {
            $classes[] = $eventos[$data_atual]['tipo']; // 'feriado' ou 'recesso'
            $tooltip = $eventos[$data_atual]['nome'];
        }

        // Regra 3: Verificar se o dia pertence a alguma Unidade Curricular (UC)
        foreach ($ucs as $uc) {
            if ($data_atual >= $uc['inicio'] && $data_atual <= $uc['fim']) {
                $classes[] = 'uc';
                // Adiciona uma borda inferior ou fundo sutil com a cor da UC cadastrada
                $estilo_uc = "border-bottom: 4px solid " . $uc['cor'] . ";";
                $tooltip .= ($tooltip ? " | " : "") . $uc['nome'];
                break; // Mostra a primeira UC encontrada para o dia
            }
        }

        $classe_final = implode(' ', $classes);
        echo "<div class='{$classe_final}' style='{$estilo_uc}' title='{$tooltip}'>";
        echo $dia;
        echo "</div>";
    }

    echo "</div>"; // Fim .dias
    echo "</div>"; // Fim .mes
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calendário Acadêmico SENAC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="https://www.mg.senac.br/hotsite/matriculas/images/logo-senac-branco.png" alt="Logo SENAC" style="height: 50px;">
    </div>
    <div class="titulo">
        <h1>Calendário Acadêmico</h1>
        <h2>SENAC Minas</h2>
    </div>
    <div class="menu">
        <a href="index.php">Atualizar Calendário</a>
        <a href="legenda.php" class="btn-legenda" style="margin-top: 0; padding: 10px 14px;">Legendas</a>
    </div>
</header>

<main>

    <?php if (isset($mensagem)): ?>
        <div class="mensagem-sucesso">
            <strong>Sucesso!</strong> <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <!-- PAINEL SUPERIOR: CADASTRO DE UCs E EVENTOS -->
    <div class="painel-turma">
        <!-- Formulário para Criar Eventos (Feriados/Recessos) -->
        <form action="index.php" method="POST" class="campo" style="grid-column: span 1; gap: 15px;">
            <h3>Cadastrar Feriado/Recesso</h3>
            
            <div class="campo">
                <label>Nome do Evento</label>
                <input type="text" name="nome_evento" required placeholder="Ex: Páscoa, Recesso Escolar">
            </div>

            <div class="campo">
                <label>Data</label>
                <input type="date" name="data_evento" required>
            </div>

            <div class="campo">
                <label>Tipo</label>
                <select name="tipo_evento" style="padding:10px; border:1px solid #CCC; border-radius:5px;">
                    <option value="feriado">Feriado (Vermelho)</option>
                    <option value="recesso">Recesso (Azul Claro)</option>
                </select>
            </div>

            <button type="submit" name="adicionar_evento" class="btn-salvar">Adicionar Evento</button>
        </form>

        <!-- Formulário para Criar Unidades Curriculares (UCs) -->
        <form action="index.php" method="POST" class="campo" style="grid-column: span 2; gap: 15px;">
            <h3>Cadastrar Unidade Curricular (UC)</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="campo">
                    <label>Nome da UC</label>
                    <input type="text" name="nome_uc" required placeholder="Ex: Modelagem de Dados">
                </div>

                <div class="campo">
                    <label>Selecione uma Cor para a UC</label>
                    <input type="color" name="cor_uc" value="#F58220" style="height: 45px; padding: 2px; cursor: pointer;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="campo">
                    <label>Data de Início</label>
                    <input type="date" name="inicio_uc" required>
                </div>

                <div class="campo">
                    <label>Data de Término</label>
                    <input type="date" name="fim_uc" required>
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="submit" name="adicionar_uc" class="btn-salvar" style="flex-grow: 1;">Salvar Unidade Curricular</button>
                <button type="submit" name="limpar_dados" class="btn-salvar" style="background-color: #8a1f1f;">Limpar Tudo</button>
            </div>
        </form>
    </div>

    <!-- CALENDÁRIO DINÂMICO -->
    <div class="calendario-container">
        <h3>Calendário Letivo de 2026</h3>
        
        <div class="calendario">
            <?php
            // Gera os meses de Janeiro (1) a Junho (6) de 2026 dinamicamente
            for ($m = 1; $m <= 6; $m++) {
                desenhar_mes($m, 2026, $_SESSION['eventos'], $_SESSION['ucs']);
            }
            ?>
        </div>
    </div>

    <!-- SEÇÃO DE LEGENDAS DINÂMICAS -->
    <div class="legenda">
        <div class="legenda-grupo">
            <h4>Legenda de Eventos</h4>
            <div class="item-legenda">
                <div class="cor" style="background: #ffe5e5; border: 1px solid #ff9999;"></div>
                <span>Feriado Nacional / Regional</span>
            </div>
            <div class="item-legenda">
                <div class="cor" style="background: #e9f3ff; border: 1px solid #99c2ff;"></div>
                <span>Recesso Institucional</span>
            </div>
            <div class="item-legenda">
                <div class="cor" style="background: #f5f5f5; border: 1px solid #ddd;"></div>
                <span>Finais de Semana</span>
            </div>
        </div>

        <div class="legenda-grupo">
            <h4>Unidades Curriculares Ativas</h4>
            <?php if (empty($_SESSION['ucs'])): ?>
                <span>Nenhuma UC cadastrada para este período.</span>
            <?php else: ?>
                <?php foreach ($_SESSION['ucs'] as $uc): ?>
                    <div class="item-legenda">
                        <div class="cor" style="background-color: <?php echo $uc['cor']; ?>;"></div>
                        <span>
                            <strong><?php echo $uc['nome']; ?></strong> 
                            (<?php echo date('d/m', strtotime($uc['inicio'])) . ' a ' . date('d/m', strtotime($uc['fim'])); ?>)
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</main>

<footer>
    <p>© 2026 SENAC Minas - Todos os direitos reservados.</p>
</footer>

</body>
</html>