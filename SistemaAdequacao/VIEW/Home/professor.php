<?php
$title = 'Dashboard Professor - ETC';
$brand = 'ETC - Professor Regente';
$navbarClass = 'navbar-dark bg-dark';
include __DIR__ . '/../Layout/topo.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Professor Regente</h4>
            <small class="text-muted">Gerencie os planos pedagógicos e adequações das suas disciplinas</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">✏️</div>
                    <h5 class="card-title">Criar Adequação</h5>
                    <p class="card-text text-muted small flex-grow-1">Inicie o preenchimento do plano pedagógico para um aluno.</p>
                    <a href="index.php?action=criar_adequacao" class="btn btn-success btn-sm mt-2">+ Criar Adequação</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">👥</div>
                    <h5 class="card-title">Alunos</h5>
                    <p class="card-text text-muted small flex-grow-1">Visualize os alunos e suas informações cadastradas.</p>
                    <a href="index.php?action=listar_alunos" class="btn btn-outline-secondary btn-sm mt-2">Listar Alunos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">📄</div>
                    <h5 class="card-title">Lista de Adequações</h5>
                    <p class="card-text text-muted small flex-grow-1">Consulte a lista completa de adequações curriculares.</p>
                    <a href="index.php?action=gerar_lista_adequacao" class="btn btn-outline-warning btn-sm mt-2">📋 Ver Lista</a>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>