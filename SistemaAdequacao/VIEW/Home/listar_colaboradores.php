<?php
$title = 'Colaboradores - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';

global $pdo;

$stmt = $pdo->query("
    SELECT u.id, u.email, u.ativo, up.papel_id, p.nome AS papel_nome
    FROM usuario u
    LEFT JOIN usuario_papel up ON up.usuario_id = u.id
    LEFT JOIN papel p ON p.id = up.papel_id
    ORDER BY p.id ASC, u.email ASC
");
$colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$papelLabels = [
    1 => ['label' => 'Admin',                        'badge' => 'bg-dark'],
    2 => ['label' => 'Gestão Escolar',               'badge' => 'bg-success'],
    3 => ['label' => 'Sala de Recursos (AEE)',        'badge' => 'bg-primary'],
    4 => ['label' => 'Professor Regente',             'badge' => 'bg-secondary'],
    5 => ['label' => 'Apoio',                         'badge' => 'bg-info text-dark'],
    6 => ['label' => 'Educador Social Voluntário',    'badge' => 'bg-warning text-dark'],
    7 => ['label' => 'Monitor Educacional',           'badge' => 'bg-info text-dark'],
    8 => ['label' => 'Secretário Escolar',           'badge' => 'bg-danger'],
    9 => ['label' => 'Coordenador Pedagógico',       'badge' => 'bg-purple text-white'],
];
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Colaboradores</h4>
            <small class="text-muted">Gerencie os usuários do sistema</small>
        </div>
        <a href="index.php?action=cadastrar_colaborador" class="btn btn-success btn-sm">+ Cadastrar Colaborador</a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (empty($colaboradores)): ?>
        <div class="alert alert-info">Nenhum colaborador cadastrado.</div>
    <?php else: ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($colaboradores as $col): ?>
                        <?php
                        $papelId = (int)($col['papel_id'] ?? 0);
                        $badge   = $papelLabels[$papelId]['badge']  ?? 'bg-secondary';
                        $label   = $papelLabels[$papelId]['label']  ?? $col['papel_nome'] ?? '-';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($col['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($label); ?></span></td>
                            <td>
                                <?php if ($col['ativo']): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?action=editar_colaborador&id=<?php echo (int)$col['id']; ?>"
                                   class="btn btn-sm btn-outline-primary">✏️ Editar</a>
                                <a href="index.php?action=toggle_colaborador&id=<?php echo (int)$col['id']; ?>"
                                   class="btn btn-sm <?php echo $col['ativo'] ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                   onclick="return confirm('<?php echo $col['ativo'] ? 'Desativar' : 'Ativar'; ?> este colaborador?')">
                                    <?php echo $col['ativo'] ? '🔒 Desativar' : '🔓 Ativar'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>

    <div class="mt-3">
        <a href="index.php?action=dashboard" class="btn btn-secondary btn-sm">← Voltar ao Dashboard</a>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
