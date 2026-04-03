<?php
$title = 'Vincular Professores ao Aluno - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';

global $pdo;

$alunoId = (int)($_GET['aluno_id'] ?? 0);

// Lista todos os alunos para seleção
$alunoDAO = new AlunoDAO($pdo);
$alunos   = $alunoDAO->listarTodos();

// Professores disponíveis (papel 4)
$stmtProfs = $pdo->query("SELECT u.id, u.nome_completo, u.email, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 4 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
$professores = $stmtProfs->fetchAll(PDO::FETCH_ASSOC);

// Vínculos existentes do aluno selecionado
$vinculos = [];
$aluno    = null;
if ($alunoId) {
    $aluno = $alunoDAO->buscarPorId($alunoId);
    $stmtV = $pdo->prepare("SELECT ap.id, ap.disciplina, u.nome_completo, u.email, u.matricula_servidor FROM aluno_professor ap INNER JOIN usuario u ON u.id = ap.professor_id WHERE ap.aluno_id = :id ORDER BY u.nome_completo ASC");
    $stmtV->execute([':id' => $alunoId]);
    $vinculos = $stmtV->fetchAll(PDO::FETCH_ASSOC);
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Vincular Professores ao Aluno</h4>
            <small class="text-muted">Gerencie quais professores atendem cada aluno</small>
        </div>
        <a href="index.php?action=dashboard" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <!-- Seleção de aluno -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white"><h6 class="mb-0">Selecionar Aluno</h6></div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-2">
                <input type="hidden" name="action" value="vincular_professores">
                <div class="col-md-9">
                    <select name="aluno_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">-- Selecione um aluno --</option>
                        <?php foreach ($alunos as $a): ?>
                            <option value="<?php echo $a['id']; ?>" <?php echo $alunoId == $a['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($a['nome_completo']); ?> — <?php echo htmlspecialchars($a['matricula'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if ($alunoId && $aluno): ?>

    <!-- Adicionar vínculo -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><h6 class="mb-0">Adicionar Professor — <?php echo htmlspecialchars($aluno['nome_completo']); ?></h6></div>
        <div class="card-body">
            <form method="POST" action="index.php?action=processar_vincular_professor" class="row g-3">
                <input type="hidden" name="aluno_id" value="<?php echo $alunoId; ?>">
                <div class="col-md-6">
                    <label class="form-label">Professor <span class="text-danger">*</span></label>
                    <select name="professor_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php foreach ($professores as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['nome_completo'] ?: $p['email']); ?>
                                <?php if (!empty($p['matricula_servidor'])): ?> — <?php echo htmlspecialchars($p['matricula_servidor']); ?><?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Disciplina</label>
                    <input type="text" name="disciplina" class="form-control" placeholder="Ex: Matemática, Português...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">+ Vincular</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Vínculos existentes -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white"><h6 class="mb-0">Professores Vinculados</h6></div>
        <div class="card-body p-0">
            <?php if (empty($vinculos)): ?>
                <p class="text-muted p-3 mb-0">Nenhum professor vinculado ainda.</p>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Professor</th><th>Matrícula</th><th>Disciplina</th><th class="text-center">Ação</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vinculos as $v): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($v['nome_completo'] ?: $v['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><small><?php echo htmlspecialchars($v['matricula_servidor'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></small></td>
                                <td><?php echo htmlspecialchars($v['disciplina'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center">
                                    <a href="index.php?action=remover_vinculo&id=<?php echo $v['id']; ?>&aluno_id=<?php echo $alunoId; ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Remover este vínculo?')">🗑 Remover</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php endif; ?>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
