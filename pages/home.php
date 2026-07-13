<!--========================================================
Página inicial
Nesta página construímos o layout do calendário do Módulo 3.
========================================================-->
<?php
require_once __DIR__ . '/../includes/conexao.php';

$curso = filter_input(INPUT_POST, 'curso', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$turma = filter_input(INPUT_POST, 'turma', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$turno = filter_input(INPUT_POST, 'turno', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$carga = filter_input(INPUT_POST, 'carga', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$docentes = filter_input(INPUT_POST, 'docentes', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$dataInicial = filter_input(INPUT_POST, 'data_inicial', FILTER_SANITIZE_STRING) ?: '';
$dataFinal = filter_input(INPUT_POST, 'data_final', FILTER_SANITIZE_STRING) ?: '';
$ucPlanejamentoInicio = filter_input(INPUT_POST, 'uc_planejamento_inicio', FILTER_SANITIZE_STRING) ?: '';
$ucPlanejamentoFim = filter_input(INPUT_POST, 'uc_planejamento_fim', FILTER_SANITIZE_STRING) ?: '';
$ucPlanejamentoCor = filter_input(INPUT_POST, 'uc_planejamento_cor', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '#bdd7ee';
$cadastroRealizado = $_SERVER['REQUEST_METHOD'] === 'POST';

$anoReferencia = obterConfiguracao('ano_referencia', date('Y'));
$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

$feriados = [
    "$anoReferencia-01-01" => 'Confraternização Universal',
    "$anoReferencia-02-12" => 'Carnaval',
    "$anoReferencia-04-21" => 'Tiradentes',
    "$anoReferencia-05-01" => 'Dia do Trabalho',
    "$anoReferencia-09-07" => 'Independência do Brasil',
    "$anoReferencia-10-12" => 'Nossa Senhora Aparecida',
    "$anoReferencia-11-02" => 'Finados',
    "$anoReferencia-11-15" => 'Proclamação da República',
    "$anoReferencia-12-25" => 'Natal'
];

$recessos = [
    [
        'inicio' => "$anoReferencia-07-01",
        'fim' => "$anoReferencia-07-15",
        'descricao' => 'Recesso de Julho'
    ]
];

$unidadesCurriculares = [
    [
        'nome' => 'Planejamento de Sistemas',
        'inicio' => "$anoReferencia-01-10",
        'fim' => "$anoReferencia-02-28",
        'cor' => '#bdd7ee'
    ],
    [
        'nome' => 'Banco de Dados',
        'inicio' => "$anoReferencia-02-15",
        'fim' => "$anoReferencia-04-10",
        'cor' => '#d9d2e9'
    ],
    [
        'nome' => 'Redes de Computadores',
        'inicio' => "$anoReferencia-04-06",
        'fim' => "$anoReferencia-05-25",
        'cor' => '#fff2cc'
    ],
    [
        'nome' => 'Desenvolvimento Web',
        'inicio' => "$anoReferencia-06-01",
        'fim' => "$anoReferencia-07-20",
        'cor' => '#f2dcdb'
    ],
    [
        'nome' => 'Projeto Integrador',
        'inicio' => "$anoReferencia-08-10",
        'fim' => "$anoReferencia-10-15",
        'cor' => '#d8e4bc'
    ]
];

// Se o formulário enviar valores para as UCs, sobrescreve os valores padrão
foreach ($unidadesCurriculares as $idx => $ucItem) {
    $iniKey = 'uc_' . $idx . '_inicio';
    $fimKey = 'uc_' . $idx . '_fim';
    $corKey = 'uc_' . $idx . '_cor';

    $ini = filter_input(INPUT_POST, $iniKey, FILTER_SANITIZE_STRING);
    $fim = filter_input(INPUT_POST, $fimKey, FILTER_SANITIZE_STRING);
    $cor = filter_input(INPUT_POST, $corKey, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($ini) {
        $unidadesCurriculares[$idx]['inicio'] = $ini;
    }
    if ($fim) {
        $unidadesCurriculares[$idx]['fim'] = $fim;
    }
    if ($cor) {
        $unidadesCurriculares[$idx]['cor'] = $cor;
    }
}

function ehFeriado(string $data, array $feriados): bool
{
    return isset($feriados[$data]);
}

function ehRecesso(string $data, array $recessos): bool
{
    foreach ($recessos as $recesso) {
        if ($data >= $recesso['inicio'] && $data <= $recesso['fim']) {
            return true;
        }
    }

    return false;
}

// Verificação geral de UC foi removida; a checagem por UC é feita dentro de gerarMes

function obterIntervaloMes(array $uc, int $numeroMes, string $ano): ?array
{
    $primeiroDiaMes = DateTimeImmutable::createFromFormat('Y-n-j', "$ano-$numeroMes-1");
    $ultimoDiaMes = DateTimeImmutable::createFromFormat('Y-n-j', "$ano-$numeroMes-" . $primeiroDiaMes->format('t'));

    $inicio = max($uc['inicio'], $primeiroDiaMes->format('Y-m-d'));
    $fim = min($uc['fim'], $ultimoDiaMes->format('Y-m-d'));

    if ($inicio > $fim) {
        return null;
    }

    $inicioDia = (int) DateTimeImmutable::createFromFormat('Y-m-d', $inicio)->format('j');
    $fimDia = (int) DateTimeImmutable::createFromFormat('Y-m-d', $fim)->format('j');
    $totalDias = (int) $primeiroDiaMes->format('t');

    return [
        'inicioDia' => $inicioDia,
        'fimDia' => $fimDia,
        'offset' => ($inicioDia - 1) / $totalDias * 100,
        'largura' => ($fimDia - $inicioDia + 1) / $totalDias * 100,
        'inicio' => $inicio,
        'fim' => $fim
    ];
}

function gerarMes(int $numeroMes, string $nomeMes, string $ano, array $feriados, array $recessos, array $unidades): void
{
    $primeiroDia = DateTimeImmutable::createFromFormat('Y-n-j', "$ano-$numeroMes-1");
    $totalDias = (int) $primeiroDia->format('t');
    $inicioSemana = (int) $primeiroDia->format('w');

    echo '<article class="mes">';
    echo '<h2>' . $nomeMes . '</h2>';
    echo '<div class="dias-semana">';
    echo '<div>D</div><div>S</div><div>T</div><div>Q</div><div>Q</div><div>S</div><div>S</div>';
    echo '</div>';
    echo '<div class="dias">';

    for ($i = 0; $i < $inicioSemana; $i++) {
        echo '<div class="dia vazio"></div>';
    }

    for ($dia = 1; $dia <= $totalDias; $dia++) {
        $dataAtual = $primeiroDia->format('Y-m-') . str_pad($dia, 2, '0', STR_PAD_LEFT);
        $diaData = DateTimeImmutable::createFromFormat('Y-m-d', $dataAtual);
        $diaSemana = (int) $diaData->format('w');

        $classes = ['dia'];
        $label = $dia;
        $style = '';

        if ($diaSemana === 0 || $diaSemana === 6) {
            $classes[] = 'fim-semana';
        }

        if (ehFeriado($dataAtual, $feriados)) {
            $classes[] = 'feriado';
            $label .= '<span class="tag">Feriado</span>';
        } elseif (ehRecesso($dataAtual, $recessos)) {
            $classes[] = 'recesso';
            $label .= '<span class="tag">Recesso</span>';
        } else {
            // verifica se a data pertence a alguma UC (dias úteis)
            foreach ($unidades as $ucIdx => $ucItem) {
                if ($dataAtual >= $ucItem['inicio'] && $dataAtual <= $ucItem['fim']) {
                    if ($diaSemana !== 0 && $diaSemana !== 6) {
                        $classes[] = 'uc-' . $ucIdx;
                        $style = ' style="color:' . htmlspecialchars($ucItem['cor']) . ';"';
                    }
                    break;
                }
            }
        }

        echo '<div class="' . implode(' ', $classes) . '"' . $style . '>' . $label . '</div>';
    }

    $totalCelas = $inicioSemana + $totalDias;
    while ($totalCelas % 7 !== 0) {
        echo '<div class="dia vazio"></div>';
        $totalCelas++;
    }

    echo '</div>';

    $unidadesMes = [];
    foreach ($unidades as $uc) {
        $intervalo = obterIntervaloMes($uc, $numeroMes, $ano);
        if ($intervalo !== null) {
            $unidadesMes[] = ['uc' => $uc, 'intervalo' => $intervalo];
        }
    }

    if (!empty($unidadesMes)) {
        echo '<div class="ucs">';
        foreach ($unidadesMes as $item) {
            $uc = $item['uc'];
            $intervalo = $item['intervalo'];
            $inicioLabel = DateTimeImmutable::createFromFormat('Y-m-d', $intervalo['inicio'])->format('d/m');
            $fimLabel = DateTimeImmutable::createFromFormat('Y-m-d', $intervalo['fim'])->format('d/m');
            echo '<div class="uc-barra" style="margin-left:' . number_format($intervalo['offset'], 4) . '%; width:' . number_format($intervalo['largura'], 4) . '%; background:' . $uc['cor'] . ';">';
            echo '<strong>' . htmlspecialchars($uc['nome']) . '</strong>';
            echo '<span class="tag">' . $inicioLabel . ' - ' . $fimLabel . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }

    echo '</article>';
}
?>
<main>
    <!-- ==========================================================
         PAINEL DE DADOS DA TURMA
    ========================================================== -->
    <form class="painel-turma" method="post">
        <div class="campo">
            <label for="curso">Curso</label>
            <input id="curso" name="curso" type="text" placeholder="Técnico em Informática" value="<?= htmlspecialchars($curso) ?>">
        </div>
        <div class="campo">
            <label for="turma">Turma</label>
            <input id="turma" name="turma" type="text" placeholder="2026.0001" value="<?= htmlspecialchars($turma) ?>">
        </div>
        <div class="campo">
            <label for="turno">Turno</label>
            <input id="turno" name="turno" type="text" placeholder="Noite" value="<?= htmlspecialchars($turno) ?>">
        </div>
        <div class="campo">
            <label for="carga">Carga Horária</label>
            <input id="carga" name="carga" type="text" placeholder="1200 horas" value="<?= htmlspecialchars($carga) ?>">
        </div>
        <div class="campo">
            <label for="docentes">Docentes</label>
            <textarea id="docentes" name="docentes" placeholder="Nome dos docentes"><?= htmlspecialchars($docentes) ?></textarea>
        </div>
        <div class="campo">
            <label for="data_inicial">Data Inicial</label>
            <input id="data_inicial" name="data_inicial" type="date" value="<?= htmlspecialchars($dataInicial) ?>">
        </div>
        <div class="campo">
            <label for="data_final">Data Final</label>
            <input id="data_final" name="data_final" type="date" value="<?= htmlspecialchars($dataFinal) ?>">
        </div>

        <?php foreach ($unidadesCurriculares as $i => $ucItem): ?>
            <div class="campo">
                <label for="uc_<?= $i ?>_inicio">Início <?= htmlspecialchars($ucItem['nome']) ?></label>
                <input id="uc_<?= $i ?>_inicio" name="uc_<?= $i ?>_inicio" type="date" value="<?= htmlspecialchars($_POST['uc_'.$i.'_inicio'] ?? $ucItem['inicio']) ?>">
            </div>
            <div class="campo">
                <label for="uc_<?= $i ?>_fim">Fim <?= htmlspecialchars($ucItem['nome']) ?></label>
                <input id="uc_<?= $i ?>_fim" name="uc_<?= $i ?>_fim" type="date" value="<?= htmlspecialchars($_POST['uc_'.$i.'_fim'] ?? $ucItem['fim']) ?>">
            </div>
            <div class="campo">
                <label for="uc_<?= $i ?>_cor">Cor <?= htmlspecialchars($ucItem['nome']) ?></label>
                <input id="uc_<?= $i ?>_cor" name="uc_<?= $i ?>_cor" type="color" value="<?= htmlspecialchars($_POST['uc_'.$i.'_cor'] ?? $ucItem['cor']) ?>">
            </div>
        <?php endforeach; ?>

        <div class="campo campo-botao">
            <button type="submit" class="btn-salvar">Salvar cadastro</button>
        </div>
    </form>

    <?php if ($cadastroRealizado): ?>
        <section class="mensagem-sucesso">
            <strong>Cadastro realizado com sucesso!</strong>
            <p>Turma <strong><?= htmlspecialchars($turma) ?></strong> cadastrada para o curso <strong><?= htmlspecialchars($curso) ?></strong>.</p>
        </section>
    <?php endif; ?>

    <!-- ==========================================================
         CALENDÁRIO
    ========================================================== -->
    <section class="calendario">
        <?php
        foreach ($meses as $numeroMes => $mes) {
            gerarMes($numeroMes, $mes, $anoReferencia, $feriados, $recessos, $unidadesCurriculares);
        }
        ?>
    </section>

    <!-- ==========================================================
         LEGENDA
    ========================================================== -->
    <section class="legenda">
        <div class="legenda-grupo">
            <h4>Unidades Curriculares</h4>
            <?php foreach ($unidadesCurriculares as $uc): ?>
                <div class="item-legenda">
                    <span class="cor" style="background: <?= $uc['cor'] ?>;"></span>
                    <?= htmlspecialchars($uc['nome']) ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="legenda-grupo">
            <h4>Tipos de dia</h4>
            <div class="item-legenda">
                <span class="cor fim-semana"></span>
                Fim de semana
            </div>
            <div class="item-legenda">
                <span class="cor feriado"></span>
                Feriado
            </div>
            <div class="item-legenda">
                <span class="cor recesso"></span>
                Recesso
            </div>
        </div>
    </section>
</main>
