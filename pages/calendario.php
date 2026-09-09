<?php
session_start();

if (!isset($_SESSION['cursos_cadastrados']) || empty($_SESSION['cursos_cadastrados'])) {
    $_SESSION['cursos_cadastrados'] = [
        [
            'nome' => 'Técnico em Informática',
            'turma' => '2026.0001',
            'turno' => 'Noturno',
            'modalidade' => 'Presencial',
            'carga_horaria' => '1200 horas',
            'docentes' => 'Ana Paula, Bruno Silva',
            'data_inicial' => '2026-01-10',
            'periodo' => '2026/1',
            'status' => 'Ativo',
            'cor' => '#005CA9'
        ],
        [
            'nome' => 'Administração',
            'turma' => '2026.0002',
            'turno' => 'Manhã',
            'modalidade' => 'Presencial',
            'carga_horaria' => '900 horas',
            'docentes' => 'Carla Mendes, Fernando Costa',
            'data_inicial' => '2026-01-10',
            'periodo' => '2026/1',
            'status' => 'Ativo',
            'cor' => '#F58220'
        ],
        [
            'nome' => 'Desenvolvimento de Sistemas',
            'turma' => '2026.0003',
            'turno' => 'Tarde',
            'modalidade' => 'Híbrido',
            'carga_horaria' => '1400 horas',
            'docentes' => 'Rafael Dias, Mônica Souza',
            'data_inicial' => '2026-07-01',
            'periodo' => '2026/2',
            'status' => 'Em andamento',
            'cor' => '#0F9D58'
        ]
    ];
}

$mensagemCurso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_curso'])) {
    $nome = trim($_POST['curso_nome'] ?? '');
    $turma = trim($_POST['curso_turma'] ?? '');

    if ($nome !== '' && $turma !== '') {
        $_SESSION['cursos_cadastrados'][] = [
            'nome' => $nome,
            'turma' => $turma,
            'turno' => trim($_POST['curso_turno'] ?? 'Não informado'),
            'modalidade' => trim($_POST['curso_modalidade'] ?? 'Presencial'),
            'carga_horaria' => trim($_POST['curso_carga'] ?? 'Não informado'),
            'docentes' => trim($_POST['curso_docentes'] ?? 'Não informado'),
            'data_inicial' => trim($_POST['curso_data_inicial'] ?? ''),
            'periodo' => trim($_POST['curso_periodo'] ?? '2026/1'),
            'status' => 'Ativo',
            'cor' => trim($_POST['curso_cor'] ?? '#005CA9')
        ];

        $mensagemCurso = 'Curso cadastrado com sucesso.';
    } else {
        $mensagemCurso = 'Preencha ao menos o nome e a turma do curso.';
    }
}

$cursos = $_SESSION['cursos_cadastrados'];
$totalCursos = count($cursos);
$ativos = 0;
foreach ($cursos as $curso) {
    if (($curso['status'] ?? 'Ativo') === 'Ativo') {
        $ativos++;
    }
}
?>

<main class="calendar-dashboard">
    <section class="dashboard-header">
        <div>
            <p class="eyebrow">Painel acadêmico</p>
            <h2>Cursos cadastrados</h2>
        </div>
        <a href="?page=home" class="btn-primary">Ir para Calendário</a>
    </section>

    <section class="dashboard-summary">
        <div class="summary-card">
            <span>Total</span>
            <strong><?= htmlspecialchars((string) $totalCursos) ?></strong>
        </div>
        <div class="summary-card">
            <span>Ativos</span>
            <strong><?= htmlspecialchars((string) $ativos) ?></strong>
        </div>
        <div class="summary-card">
            <span>Turnos</span>
            <strong>3</strong>
        </div>
    </section>

    <?php if ($mensagemCurso !== ''): ?>
        <div class="alerta"><?= htmlspecialchars($mensagemCurso) ?></div>
    <?php endif; ?>

    <section class="dashboard-layout">
        <form method="post" class="dashboard-form">
            <h3>Novo curso</h3>

            <div class="campo">
                <label for="curso_nome">Nome do curso</label>
                <input id="curso_nome" name="curso_nome" type="text" placeholder="Técnico em Informática">
            </div>

            <div class="campo">
                <label for="curso_turma">Turma</label>
                <input id="curso_turma" name="curso_turma" type="text" placeholder="2026.0004">
            </div>

            <div class="campo-group">
                <div class="campo">
                    <label for="curso_turno">Turno</label>
                    <input id="curso_turno" name="curso_turno" type="text" placeholder="Manhã">
                </div>
                <div class="campo">
                    <label for="curso_modalidade">Modalidade</label>
                    <input id="curso_modalidade" name="curso_modalidade" type="text" placeholder="Presencial">
                </div>
            </div>

            <div class="campo-group">
                <div class="campo">
                    <label for="curso_carga">Carga horária</label>
                    <input id="curso_carga" name="curso_carga" type="text" placeholder="1200 horas">
                </div>
                <div class="campo">
                    <label for="curso_periodo">Período</label>
                    <input id="curso_periodo" name="curso_periodo" type="text" placeholder="2026/1">
                </div>
            </div>

            <div class="campo">
                <label for="curso_docentes">Docentes</label>
                <textarea id="curso_docentes" name="curso_docentes" rows="3" placeholder="Nome dos docentes"></textarea>
            </div>

            <div class="campo">
                <label for="curso_data_inicial">Data inicial</label>
                <input id="curso_data_inicial" name="curso_data_inicial" type="date">
            </div>

            <div class="campo">
                <label for="curso_cor">Cor do curso</label>
                <input id="curso_cor" name="curso_cor" type="color" value="#005CA9">
            </div>

            <button type="submit" name="novo_curso" class="btn-primary btn-block">Salvar curso</button>
        </form>

        <section class="courses-grid">
            <?php foreach ($cursos as $indiceCurso => $curso): ?>
                <article class="course-card">
                    <div class="course-top" style="background: linear-gradient(135deg, <?= htmlspecialchars($curso['cor']) ?>, #ebf3ff);">
                        <span class="status-badge"><?= htmlspecialchars($curso['status'] ?? 'Ativo') ?></span>
                        <span class="period-badge"><?= htmlspecialchars($curso['periodo'] ?? '2026/1') ?></span>
                    </div>

                    <div class="course-body">
                        <h3><?= htmlspecialchars($curso['nome']) ?></h3>

                        <div class="course-info">
                            <div>
                                <span>Turma</span>
                                <strong><?= htmlspecialchars($curso['turma']) ?></strong>
                            </div>
                            <div>
                                <span>Turno</span>
                                <strong><?= htmlspecialchars($curso['turno']) ?></strong>
                            </div>
                            <div>
                                <span>Modalidade</span>
                                <strong><?= htmlspecialchars($curso['modalidade']) ?></strong>
                            </div>
                            <div>
                                <span>Carga</span>
                                <strong><?= htmlspecialchars($curso['carga_horaria']) ?></strong>
                            </div>
                        </div>

                        <p class="docentes">
                            <strong>Docentes:</strong> <?= htmlspecialchars($curso['docentes']) ?>
                        </p>

                        <div class="course-actions">
                            <a href="?page=home&amp;curso=<?= $indiceCurso ?>" class="btn-secondary">Ver calendário</a>
                            <a href="?page=eventos" class="btn-link">Gerenciar UCs</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </section>
</main>
