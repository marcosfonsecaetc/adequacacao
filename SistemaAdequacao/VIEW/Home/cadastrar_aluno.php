<?php
$title = 'Cadastrar Aluno - ETC';

// Define brand e cor da navbar baseado no papel do usuário logado
$papelId = $_SESSION['usuario_papel'] ?? null;
if ($papelId == 4) {
    $brand = 'ETC - Professor Regente';
    $navbarClass = 'navbar-dark bg-dark'; // Professor
} elseif ($papelId == 3) {
    $brand = 'ETC - Sala de Recursos';
    $navbarClass = 'navbar-dark bg-primary'; // AEE / Sala de Recursos
} elseif ($papelId == 2) {
    $brand = 'ETC - Gestão Escolar';
    $navbarClass = 'navbar-dark bg-success'; // Gestor
} else {
    $brand = 'ETC - Sistema';
    $navbarClass = 'navbar-dark bg-secondary'; // Outro
}

include __DIR__ . '/../Layout/topo.php';
?>

    <h3>Cadastro de Aluno</h3>
    <p>Insira os dados do aluno para cadastro.</p>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form action="index.php?action=processar_cadastro_aluno" method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-12">
            <h5 class="text-primary">Dados Básicos (obrigatórios)</h5>
            <hr>
        </div>

        <div class="col-md-6">
            <label for="nome" class="form-label">Nome</label>
            <input id="nome" name="nome" type="text" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="matricula" class="form-label">Matrícula</label>
            <input id="matricula" name="matricula" type="text" class="form-control" required>
        </div>
        <div class="col-12">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email" class="form-control" required>
        </div>
        <div class="col-12">
            <label for="foto" class="form-label">Foto do Aluno</label>
            <input id="foto" name="foto" type="file" class="form-control" accept="image/*">
            <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.</div>
        </div>

        <div class="col-12 mt-4">
            <h5 class="text-secondary">Dados da ficha (preenchimento posterior)</h5>
            <hr>
        </div>

        <div class="col-md-4">
            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
            <input id="data_nascimento" name="data_nascimento" type="date" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="idade" class="form-label">Idade</label>
            <input id="idade" name="idade" type="number" class="form-control" min="1" max="150">
        </div>
        <div class="col-md-6">
            <label for="modalidade_ano_turma_turno" class="form-label">Modalidade / Ano / Turma / Turno</label>
            <input id="modalidade_ano_turma_turno" name="modalidade_ano_turma_turno" type="text" class="form-control">
        </div>
        <div class="col-12">
            <label for="endereco" class="form-label">Endereço</label>
            <input id="endereco" name="endereco" type="text" class="form-control">
        </div>
        <div class="col-12">
            <label for="telefones_responsaveis" class="form-label">Telefones dos Responsáveis</label>
            <input id="telefones_responsaveis" name="telefones_responsaveis" type="text" class="form-control" placeholder="(61) 99999-9999">
        </div>
        <div class="col-md-6">
            <label for="filiacao_mae" class="form-label">Filiação - Mãe</label>
            <input id="filiacao_mae" name="filiacao_mae" type="text" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="filiacao_pai" class="form-label">Filiação - Pai</label>
            <input id="filiacao_pai" name="filiacao_pai" type="text" class="form-control">
        </div>
        <div class="col-12">
            <label for="periodo_vigencia_adequacao" class="form-label">Período de Vigência da Adequação</label>
            <input id="periodo_vigencia_adequacao" name="periodo_vigencia_adequacao" type="text" class="form-control" placeholder="Ex: 12/02 a 10/07/26">
        </div>
        <div class="col-12">
            <label for="diagnostico_detalhado" class="form-label">Diagnóstico Detalhado</label>
            <textarea id="diagnostico_detalhado" name="diagnostico_detalhado" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12">
            <label for="cid_codigos" class="form-label">Códigos CID</label>
            <input id="cid_codigos" name="cid_codigos" type="text" class="form-control" placeholder="Ex: F84.0, F90">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Cadastrar Aluno</button>
            <a href="index.php?action=dashboard" class="btn btn-secondary">Voltar</a>
        </div>
    </form>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>