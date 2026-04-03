<?php
$title = 'Relatórios - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';

global $pdo;

// --- Relatório 1: Total de alunos por modalidade/turma ---
$stmtModalidade = $pdo->query("
    SELECT modalidade_ano_turma_turno AS modalidade, COUNT(*) AS total
    FROM aluno
    WHERE modalidade_ano_turma_turno IS NOT NULL AND modalidade_ano_turma_turno != ''
    GROUP BY modalidade_ano_turma_turno
    ORDER BY total DESC
");
$porModalidade = $stmtModalidade->fetchAll(PDO::FETCH_ASSOC);

// --- Relatório 2: Alunos com e sem ficha AEE preenchida ---
$stmtFicha = $pdo->query("
    SELECT
        SUM(CASE WHEN EXISTS (SELECT 1 FROM saude_escolarizacao WHERE aluno_id = a.id) THEN 1 ELSE 0 END) AS com_ficha,
        SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM saude_escolarizacao WHERE aluno_id = a.id) THEN 1 ELSE 0 END) AS sem_ficha,
        COUNT(*) AS total
    FROM aluno a
");
$fichaStatus = $stmtFicha->fetch(PDO::FETCH_ASSOC);

// --- Relatório 3: Alunos com uso de medicação ---
$stmtMed = $pdo->query("
    SELECT a.nome_completo, a.modalidade_ano_turma_turno, s.medicacao_especificacao
    FROM aluno a
    INNER JOIN saude_escolarizacao s ON s.aluno_id = a.id
    WHERE s.faz_uso_medicacao = 1
    ORDER BY a.nome_completo ASC
");
$comMedicacao = $stmtMed->fetchAll(PDO::FETCH_ASSOC);

// --- Relatório 4: Planos pedagógicos por professor ---
$stmtPlanos = $pdo->query("
    SELECT u.email AS professor, COUNT(pp.id) AS total_planos,
           COUNT(DISTINCT pp.aluno_id) AS total_alunos,
           GROUP_CONCAT(DISTINCT pp.disciplina_nome ORDER BY pp.disciplina_nome SEPARATOR ', ') AS disciplinas
    FROM plano_pedagogico pp
    INNER JOIN usuario u ON u.id = pp.professor_id
    GROUP BY pp.professor_id
    ORDER BY total_planos DESC
");
$porProfessor = $stmtPlanos->fetchAll(PDO::FETCH_ASSOC);

// --- Relatório 5: CIDs mais frequentes ---
$stmtCid = $pdo->query("
    SELECT cid_codigos, COUNT(*) AS total
    FROM aluno
    WHERE cid_codigos IS NOT NULL AND cid_codigos != ''
    GROUP BY cid_codigos
    ORDER BY total DESC
");
$porCid = $stmtCid->fetchAll(PDO::FETCH_ASSOC);

$totalAlunos = (int)($fichaStatus['total'] ?? 0);
$comFicha    = (int)($fichaStatus['com_ficha'] ?? 0);
$semFicha    = (int)($fichaStatus['sem_ficha'] ?? 0);
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Relatórios</h4>
            <small class="text-muted">Visão geral do sistema de adequações curriculares</small>
        </div>
        <a href="index.php?action=dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
    </div>

    <!-- Cards de resumo -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-success"><?php echo $totalAlunos; ?></div>
                    <div class="text-muted small">Total de Alunos</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-primary"><?php echo $comFicha; ?></div>
                    <div class="text-muted small">Com Ficha AEE</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-warning"><?php echo $semFicha; ?></div>
                    <div class="text-muted small">Sem Ficha AEE</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="fs-1 fw-bold text-danger"><?php echo count($comMedicacao); ?></div>
                    <div class="text-muted small">Usam Medicação</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Relatório 1: Alunos por Modalidade -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">📊 Alunos por Modalidade / Turma</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($porModalidade)): ?>
                        <p class="text-muted p-3 mb-0">Nenhum dado disponível.</p>
                    <?php else: ?>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Modalidade / Turma</th><th class="text-center">Alunos</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($porModalidade as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['modalidade'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?php echo $row['total']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Relatório 2: CIDs mais frequentes -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">🏥 CIDs Mais Frequentes</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($porCid)): ?>
                        <p class="text-muted p-3 mb-0">Nenhum dado disponível.</p>
                    <?php else: ?>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Código CID</th><th class="text-center">Ocorrências</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($porCid as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['cid_codigos'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?php echo $row['total']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Relatório 3: Alunos com Medicação -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">💊 Alunos que Fazem Uso de Medicação</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($comMedicacao)): ?>
                        <p class="text-muted p-3 mb-0">Nenhum aluno com medicação registrada.</p>
                    <?php else: ?>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Aluno</th><th>Turma</th><th>Medicação</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comMedicacao as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nome_completo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><small><?php echo htmlspecialchars($row['modalidade_ano_turma_turno'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small></td>
                                        <td><small><?php echo htmlspecialchars($row['medicacao_especificacao'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Relatório 4: Planos por Professor -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">👨‍🏫 Planos Pedagógicos por Professor</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($porProfessor)): ?>
                        <p class="text-muted p-3 mb-0">Nenhum plano pedagógico registrado.</p>
                    <?php else: ?>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Professor</th><th class="text-center">Planos</th><th class="text-center">Alunos</th><th>Disciplinas</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($porProfessor as $row): ?>
                                    <tr>
                                        <td><small><?php echo htmlspecialchars($row['professor'], ENT_QUOTES, 'UTF-8'); ?></small></td>
                                        <td class="text-center"><span class="badge bg-dark"><?php echo $row['total_planos']; ?></span></td>
                                        <td class="text-center"><span class="badge bg-secondary"><?php echo $row['total_alunos']; ?></span></td>
                                        <td><small><?php echo htmlspecialchars($row['disciplinas'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
