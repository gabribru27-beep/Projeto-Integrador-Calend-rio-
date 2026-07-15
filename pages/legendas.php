<?php
require_once __DIR__ . '/../includes/conexao.php';

// Inicializa variáveis para o formulário
$nomeLegenda = filter_input(INPUT_POST, 'nome_legenda', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$tipoLegenda = filter_input(INPUT_POST, 'tipo_legenda', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'uc';
$corLegenda = filter_input(INPUT_POST, 'cor_legenda', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '#005CA9';
$dataInicio = filter_input(INPUT_POST, 'data_inicio', FILTER_SANITIZE_STRING) ?: '';
$dataFim = filter_input(INPUT_POST, 'data_fim', FILTER_SANITIZE_STRING) ?: '';
$cadastroRealizado = $_SERVER['REQUEST_METHOD'] === 'POST';

/* 
 * Aqui você faria a inserção no Banco de Dados.
 * Exemplo fictício:
 * se ($cadastroRealizado) {
 *     inserirLegendaNoBanco($nomeLegenda, $tipoLegenda, $corLegenda, $dataInicio, $dataFim);
 * }
 */

// Simulando dados que viriam do banco para listar as legendas já cadastradas
$legendasCadastradas = [
    ['nome' => 'Planejamento de Sistemas', 'tipo' => 'UC', 'cor' => '#bdd7ee', 'inicio' => '10/01/2026', 'fim' => '28/02/2026'],
    ['nome' => 'Feriado Municipal', 'tipo' => 'Feriado', 'cor' => '#FF4D4D', 'inicio' => '15/08/2026', 'fim' => '15/08/2026'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Legendas - SENAC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos específicos para a tabela de listagem desta página */
        .lista-legendas {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,.10);
            margin-top: 30px;
        }
        .lista-legendas h3 {
            color: var(--azul-senac);
            margin-bottom: 20px;
        }
        .tabela-legendas {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .tabela-legendas th, .tabela-legendas td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .tabela-legendas th {
            background-color: var(--cinza);
            color: var(--azul-senac);
        }
        .tabela-legendas .preview-cor {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            vertical-align: middle;
            margin-right: 8px;
            border: 1px solid #ccc;
        }
        .btn-excluir {
            color: #d9534f;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-excluir:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- ==========================================================
         CABEÇALHO
    ========================================================== -->
    <header>
        <div style="display: flex; align-items: center;">
            <div class="logo">
                <!-- <img src="logo-senac.png" alt="Logo SENAC"> -->
            </div>
            <div class="titulo">
                <h1>Calendário Acadêmico</h1>
                <h2>Gerenciador de Legendas</h2>
            </div>
        </div>
        <nav class="menu">
            <!-- O link "Nova Turma" voltaria para a página principal index.php -->
            <a href="index.php">Voltar ao Calendário</a>
            <a href="#">Relatórios</a>
            <a href="#">Configurações</a>
        </nav>
    </header>

    <main>
        <!-- ==========================================================
             FORMULÁRIO DE CRIAÇÃO DE NOVA LEGENDA
        ========================================================== -->
        <div class="calendario-container" style="margin-top: 0;">
            <h3>Criar Nova Legenda</h3>
            <p style="margin-bottom: 20px; color: #555;">Adicione novas Unidades Curriculares, Feriados ou Recessos para serem usados no calendário.</p>
            
            <form class="painel-turma" method="post" style="box-shadow: none; padding: 0; margin-bottom: 0;">
                
                <div class="campo">
                    <label for="nome_legenda">Nome da Legenda / UC</label>
                    <input id="nome_legenda" name="nome_legenda" type="text" placeholder="Ex: Programação Mobile" required value="<?= htmlspecialchars($nomeLegenda) ?>">
                </div>

                <div class="campo">
                    <label for="tipo_legenda">Tipo de Evento</label>
                    <select id="tipo_legenda" name="tipo_legenda" style="padding: 10px; border: 1px solid #CCC; border-radius: 5px; font-family: inherit; font-size: 1rem; background: white;" required>
                        <option value="uc" <?= $tipoLegenda === 'uc' ? 'selected' : '' ?>>Unidade Curricular (UC)</option>
                        <option value="feriado" <?= $tipoLegenda === 'feriado' ? 'selected' : '' ?>>Feriado</option>
                        <option value="recesso" <?= $tipoLegenda === 'recesso' ? 'selected' : '' ?>>Recesso Institucional</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="cor_legenda">Cor de Identificação</label>
                    <input id="cor_legenda" name="cor_legenda" type="color" value="<?= htmlspecialchars($corLegenda) ?>" required>
                </div>

                <div class="campo">
                    <label for="data_inicio">Data de Início</label>
                    <input id="data_inicio" name="data_inicio" type="date" value="<?= htmlspecialchars($dataInicio) ?>" required>
                </div>

                <div class="campo">
                    <label for="data_fim">Data de Término</label>
                    <input id="data_fim" name="data_fim" type="date" value="<?= htmlspecialchars($dataFim) ?>" required>
                </div>

                <div class="campo campo-botao">
                    <button type="submit" class="btn-salvar">Cadastrar Legenda</button>
                </div>

            </form>
        </div>

        <?php if ($cadastroRealizado): ?>
            <section class="mensagem-sucesso" style="margin-top: 30px;">
                <strong>Sucesso!</strong>
                <p>A legenda <strong><?= htmlspecialchars($nomeLegenda) ?></strong> foi cadastrada com a cor selecionada.</p>
            </section>
        <?php endif; ?>

        <!-- ==========================================================
             LISTAGEM DE LEGENDAS CADASTRADAS
        ========================================================== -->
        <section class="lista-legendas">
            <h3>Legendas Cadastradas no Sistema</h3>
            
            <div style="overflow-x: auto;">
                <table class="tabela-legendas">
                    <thead>
                        <tr>
                            <th>Cor</th>
                            <th>Nome da Legenda</th>
                            <th>Tipo</th>
                            <th>Período</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($legendasCadastradas as $item): ?>
                            <tr>
                                <td>
                                    <span class="preview-cor" style="background-color: <?= htmlspecialchars($item['cor']) ?>;"></span>
                                </td>
                                <td><strong><?= htmlspecialchars($item['nome']) ?></strong></td>
                                <td><?= htmlspecialchars($item['tipo']) ?></td>
                                <td><?= htmlspecialchars($item['inicio']) ?> até <?= htmlspecialchars($item['fim']) ?></td>
                                <td>
                                    <a href="#" class="btn-excluir">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- ==========================================================
         RODAPÉ
    ========================================================== -->
    <footer>
        <p>Desenvolvido para o Sistema Acadêmico SENAC &copy; <?php echo date('Y'); ?></p>
    </footer>

</body>
</html>