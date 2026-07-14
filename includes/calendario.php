<?php
require_once __DIR__ . '/conexao.php';

/*========================================================
MÓDULO: Calendário do mês atual
Este arquivo gera uma grade com os dias do mês atual.
Ele usa informações do banco de dados para exibir feriados,
recessos e unidades curriculares.
========================================================*/

$mesAtual = date('n');
$anoAtual = date('Y');

$nomesMeses = [
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

$primeiroDia = mktime(0, 0, 0, $mesAtual, 1, $anoAtual);
$totalDias = cal_days_in_month(CAL_GREGORIAN, $mesAtual, $anoAtual);
$diaSemanaInicio = date('w', $primeiroDia);
$nomeMesAtual = $nomesMeses[$mesAtual];

$eventos = obterEventosCalendario($anoAtual);
$feriados = [];
$recessos = [];
$unidadesCurriculares = [];

foreach ($eventos as $evento) {
    $inicio = $evento['data_inicio'];
    $fim = $evento['data_fim'] ?: $inicio;
    $tipo = $evento['tipo'];
    $cor = $evento['cor'] ?: '#bdd7ee';

    if ($tipo === 'feriado') {
        $feriados[$inicio] = $evento['titulo'];
    } elseif ($tipo === 'recesso') {
        $recessos[] = [
            'inicio' => $inicio,
            'fim' => $fim,
            'descricao' => $evento['titulo'],
            'cor' => $cor
        ];
    } elseif ($tipo === 'uc') {
        $unidadesCurriculares[] = [
            'nome' => $evento['titulo'],
            'inicio' => $inicio,
            'fim' => $fim,
            'cor' => $cor
        ];
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

function obterUnidadePorData(string $data, array $unidades)
{
    foreach ($unidades as $uc) {
        if ($data >= $uc['inicio'] && $data <= $uc['fim']) {
            return $uc;
        }
    }

    return null;
}
?>

<section class="calendario">
    <h3>Calendário acadêmico</h3>

    <div class="mes-atual">
        <h4><?php echo $nomeMesAtual . ' de ' . $anoAtual; ?></h4>
    </div>

    <div class="dias-semana">
        <span>Dom</span>
        <span>Seg</span>
        <span>Ter</span>
        <span>Qua</span>
        <span>Qui</span>
        <span>Sex</span>
        <span>Sáb</span>
    </div>

    <div class="grade-calendario">
        <?php
        for ($i = 0; $i < $diaSemanaInicio; $i++) {
            echo '<div class="dia vazio"></div>';
        }

        for ($dia = 1; $dia <= $totalDias; $dia++) {
            $dataAtual = sprintf('%04d-%02d-%02d', $anoAtual, $mesAtual, $dia);
            $diaSemana = date('w', strtotime($dataAtual));
            $classes = ['dia'];
            $style = '';
            $label = $dia;

            if ($diaSemana === '0' || $diaSemana === '6') {
                $classes[] = 'fim-semana';
            }

            if (ehFeriado($dataAtual, $feriados)) {
                $classes[] = 'feriado';
                $label .= '<span class="tag">Feriado</span>';
            } elseif (ehRecesso($dataAtual, $recessos)) {
                $classes[] = 'recesso';
                $label .= '<span class="tag">Recesso</span>';
            } else {
                $uc = obterUnidadePorData($dataAtual, $unidadesCurriculares);
                if ($uc !== null && $diaSemana !== '0' && $diaSemana !== '6') {
                    $classes[] = 'uc';
                    $style = ' style="color:' . htmlspecialchars($uc['cor']) . ';"';
                    $label .= '<span class="tag">' . htmlspecialchars($uc['nome']) . '</span>';
                }
            }

            echo '<div class="' . implode(' ', $classes) . '"' . $style . '>' . $label . '</div>';
        }

        $totalCelas = $diaSemanaInicio + $totalDias;
        while ($totalCelas % 7 !== 0) {
            echo '<div class="dia vazio"></div>';
            $totalCelas++;
        }
        ?>
    </div>
</section>
