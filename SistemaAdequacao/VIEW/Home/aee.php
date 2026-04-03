<?php
$title = 'Dashboard AEE - ETC';
$brand = 'ETC - Sala de Recursos';
$navbarClass = 'navbar-dark bg-primary';
include __DIR__ . '/../Layout/topo.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Sala de Recursos — AEE</h4>
            <small class="text-muted">Gerencie as informações biopsicossociais e adequações dos alunos</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">📋</div>
                    <h5 class="card-title">Informações do Aluno</h5>
                    <p class="card-text text-muted small flex-grow-1">Preencha as informações biopsicossociais e de escolarização.</p>
                    <a href="index.php?action=incluir_informacoes_aluno" class="btn btn-primary btn-sm mt-2">+ Incluir Informações</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">👥</div>
                    <h5 class="card-title">Alunos</h5>
                    <p class="card-text text-muted small flex-grow-1">Visualize e gerencie os alunos cadastrados no sistema.</p>
                    <a href="index.php?action=listar_alunos" class="btn btn-outline-primary btn-sm mt-2">Listar Alunos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">📄</div>
                    <h5 class="card-title">Lista de Adequações</h5>
                    <p class="card-text text-muted small flex-grow-1">Gere e visualize a lista completa de adequações curriculares.</p>
                    <a href="index.php?action=gerar_lista_adequacao" class="btn btn-outline-warning btn-sm mt-2">📋 Gerar Lista</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">📝</div>
                    <h5 class="card-title">Plano de AEE — PAEE</h5>
                    <p class="card-text text-muted small flex-grow-1">Preencha e gere o Formulário de Registro Anual do Plano de AEE.</p>
                    <a href="index.php?action=paee" class="btn btn-outline-primary btn-sm mt-2">📝 Abrir PAEE</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">👤</div>
                    <h5 class="card-title">Perfil do Estudante</h5>
                    <p class="card-text text-muted small flex-grow-1">Preencha a anamnese e gere o Formulário de Perfil de Entrada do estudante.</p>
                    <a href="index.php?action=perfil_estudante" class="btn btn-outline-secondary btn-sm mt-2">📋 Abrir Perfil</a>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>