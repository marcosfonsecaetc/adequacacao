<?php
$title = 'Cadastrar Colaborador - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Cadastrar Colaborador</h4>
            <small class="text-muted">Adicione um novo colaborador ao sistema</small>
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
            <form action="index.php?action=processar_cadastrar_colaborador" method="POST" class="row g-3">

                <div class="col-md-6">
                    <label for="nome_completo" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nome_completo" id="nome_completo" class="form-control" required placeholder="Nome completo">
                </div>

                <div class="col-md-4">
                    <label for="email" class="form-label">E-mail Institucional <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="exemplo@etc.edu.br">
                </div>

                <div class="col-md-2">
                    <label for="matricula_servidor" class="form-label">Matrícula</label>
                    <input type="text" name="matricula_servidor" id="matricula_servidor" class="form-control" placeholder="Ex: 123456">
                </div>

                <div class="col-md-4">
                    <label for="papel_id" class="form-label">Perfil de Acesso <span class="text-danger">*</span></label>
                    <select name="papel_id" id="papel_id" class="form-select" required>
                        <option value="" disabled selected>Selecione o perfil...</option>
                        <option value="2">Gestão Escolar</option>
                        <option value="3">Sala de Recursos (AEE)</option>
                        <option value="4">Professor Regente</option>
                        <option value="5">Apoio</option>
                        <option value="6">Educador Social Voluntário (ESV)</option>
                        <option value="7">Monitor Educacional</option>
                        <option value="8">Secretário Escolar</option>
                        <option value="9">Coordenador Pedagógico</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                    <input type="password" name="senha" id="senha" class="form-control" required placeholder="Mínimo 6 caracteres">
                </div>

                <div class="col-md-4">
                    <label for="confirm_senha" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                    <input type="password" name="confirm_senha" id="confirm_senha" class="form-control" required placeholder="Repita a senha">
                </div>

                <div class="col-12 d-flex justify-content-between mt-2">
                    <a href="index.php?action=dashboard" class="btn btn-secondary">← Voltar</a>
                    <button type="submit" class="btn btn-success">Cadastrar Colaborador</button>
                </div>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
