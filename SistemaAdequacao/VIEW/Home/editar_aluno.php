<?php
$title = 'Editar Aluno - ETC';

$papelId = $_SESSION['usuario_papel'] ?? null;
if ($papelId == 4) {
    $brand = 'ETC - Professor Regente';
    $navbarClass = 'navbar-dark bg-dark';
} elseif ($papelId == 3) {
    $brand = 'ETC - Sala de Recursos';
    $navbarClass = 'navbar-dark bg-primary';
} elseif ($papelId == 2) {
    $brand = 'ETC - Gestão Escolar';
    $navbarClass = 'navbar-dark bg-success';
} else {
    $brand = 'ETC - Sistema';
    $navbarClass = 'navbar-dark bg-secondary';
}

include __DIR__ . '/../Layout/topo.php';
?>

    <h3>Editar Aluno</h3>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form action="index.php?action=processar_editar_aluno" method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="aluno_id" value="<?php echo (int)$aluno['id']; ?>">

        <div class="col-12">
            <h5 class="text-primary">Dados Básicos</h5>
            <hr>
        </div>

        <div class="col-md-6">
            <label for="nome" class="form-label">Nome</label>
            <input id="nome" name="nome" type="text" class="form-control" required value="<?php echo htmlspecialchars($aluno['nome_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
            <label for="matricula" class="form-label">Matrícula</label>
            <input id="matricula" name="matricula" type="text" class="form-control" required value="<?php echo htmlspecialchars($aluno['matricula'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email" class="form-control" required value="<?php echo htmlspecialchars($aluno['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <label for="foto" class="form-label">Foto do Aluno</label>
            <?php if (!empty($aluno['foto'])): ?>
                <div class="mb-2">
                    <img src="/SistemaAdequacao/<?php echo htmlspecialchars($aluno['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto atual" style="max-height:80px;" class="img-thumbnail">
                    <small class="text-muted ms-2">Foto atual — envie uma nova para substituir</small>
                </div>
            <?php endif; ?>
            <input id="foto" name="foto" type="file" class="form-control" accept="image/*">
            <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</div>
        </div>

        <div class="col-12 mt-4">
            <h5 class="text-secondary">Dados da Ficha</h5>
            <hr>
        </div>

        <div class="col-md-4">
            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
            <input id="data_nascimento" name="data_nascimento" type="date" class="form-control" value="<?php echo htmlspecialchars($aluno['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-2">
            <label for="idade" class="form-label">Idade</label>
            <input id="idade" name="idade" type="number" class="form-control" min="1" max="150" value="<?php echo htmlspecialchars($aluno['idade'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
            <label for="modalidade_ano_turma_turno" class="form-label">Modalidade / Ano / Turma / Turno</label>
            <input id="modalidade_ano_turma_turno" name="modalidade_ano_turma_turno" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['modalidade_ano_turma_turno'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <label for="endereco" class="form-label">Endereço</label>
            <input id="endereco" name="endereco" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['endereco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <label for="telefones_responsaveis" class="form-label">Telefones dos Responsáveis</label>
            <input id="telefones_responsaveis" name="telefones_responsaveis" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['telefones_responsaveis'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
            <label for="filiacao_mae" class="form-label">Filiação - Mãe</label>
            <input id="filiacao_mae" name="filiacao_mae" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['filiacao_mae'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
            <label for="filiacao_pai" class="form-label">Filiação - Pai</label>
            <input id="filiacao_pai" name="filiacao_pai" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['filiacao_pai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <label for="periodo_vigencia_adequacao" class="form-label">Período de Vigência da Adequação</label>
            <input id="periodo_vigencia_adequacao" name="periodo_vigencia_adequacao" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['periodo_vigencia_adequacao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <label for="diagnostico_detalhado" class="form-label">Diagnóstico Detalhado</label>
            <textarea id="diagnostico_detalhado" name="diagnostico_detalhado" class="form-control" rows="3"><?php echo htmlspecialchars($aluno['diagnostico_detalhado'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="col-12">
            <label for="cid_codigos" class="form-label">Códigos CID</label>
            <input id="cid_codigos" name="cid_codigos" type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['cid_codigos'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <a href="index.php?action=listar_alunos" class="btn btn-secondary">Voltar</a>
        </div>
    </form>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
