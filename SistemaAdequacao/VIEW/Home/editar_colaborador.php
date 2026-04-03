<?php
$title = 'Editar Colaborador - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Editar Colaborador</h4>
            <small class="text-muted">Atualize os dados do colaborador</small>
        </div>
        <a href="index.php?action=listar_colaboradores" class="btn btn-outline-success btn-sm">Ver Colaboradores</a>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Dados do Colaborador</h5>
        </div>
        <div class="card-body">
            <form action="index.php?action=processar_editar_colaborador" method="POST" class="row g-3">
                <input type="hidden" name="id" value="<?php echo (int)$colaborador['id']; ?>">

                <div class="col-md-5">
                    <label for="nome_completo" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nome_completo" id="nome_completo" class="form-control" required
                           value="<?php echo htmlspecialchars($colaborador['nome_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-5">
                    <label for="email" class="form-label">E-mail Institucional <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" required
                           value="<?php echo htmlspecialchars($colaborador['email'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-2">
                    <label for="matricula_servidor" class="form-label">Matrícula</label>
                    <input type="text" name="matricula_servidor" id="matricula_servidor" class="form-control"
                           value="<?php echo htmlspecialchars($colaborador['matricula_servidor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-6">
                    <label for="papel_id" class="form-label">Perfil de Acesso <span class="text-danger">*</span></label>
                    <select name="papel_id" id="papel_id" class="form-select" required>
                        <option value="2" <?php echo $colaborador['papel_id'] == 2 ? 'selected' : ''; ?>>Gestão Escolar</option>
                        <option value="3" <?php echo $colaborador['papel_id'] == 3 ? 'selected' : ''; ?>>Sala de Recursos (AEE)</option>
                        <option value="4" <?php echo $colaborador['papel_id'] == 4 ? 'selected' : ''; ?>>Professor Regente</option>
                        <option value="5" <?php echo $colaborador['papel_id'] == 5 ? 'selected' : ''; ?>>Apoio</option>
                        <option value="6" <?php echo $colaborador['papel_id'] == 6 ? 'selected' : ''; ?>>Educador Social Voluntário (ESV)</option>
                        <option value="7" <?php echo $colaborador['papel_id'] == 7 ? 'selected' : ''; ?>>Monitor Educacional</option>
                        <option value="8" <?php echo $colaborador['papel_id'] == 8 ? 'selected' : ''; ?>>Secretário Escolar</option>
                        <option value="9" <?php echo $colaborador['papel_id'] == 9 ? 'selected' : ''; ?>>Coordenador Pedagógico</option>
                    </select>
                </div>

                <div class="col-12">
                    <p class="text-muted small mb-1">Deixe os campos de senha em branco para manter a senha atual.</p>
                </div>

                <div class="col-md-6">
                    <label for="senha" class="form-label">Nova Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Nova senha (opcional)">
                </div>

                <div class="col-md-6">
                    <label for="confirm_senha" class="form-label">Confirmar Nova Senha</label>
                    <input type="password" name="confirm_senha" id="confirm_senha" class="form-control" placeholder="Confirme a nova senha">
                </div>

                <div class="col-12 d-flex justify-content-between mt-2">
                    <a href="index.php?action=listar_colaboradores" class="btn btn-secondary">← Voltar</a>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
