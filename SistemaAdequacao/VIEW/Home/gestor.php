<?php
$title = 'Dashboard Gestão Escolar - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Gestão Escolar</h4>
            <small class="text-muted">Monitoramento de planos, alunos e adequações curriculares</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">👥</div>
                    <h5 class="card-title">Alunos</h5>
                    <p class="card-text text-muted small flex-grow-1">Visualize e gerencie todos os alunos cadastrados.</p>
                    <a href="index.php?action=listar_alunos" class="btn btn-success btn-sm mt-2">Listar Alunos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">📄</div>
                    <h5 class="card-title">Lista de Adequações</h5>
                    <p class="card-text text-muted small flex-grow-1">Gere e exporte a lista completa de adequações curriculares.</p>
                    <a href="index.php?action=gerar_lista_adequacao" class="btn btn-outline-warning btn-sm mt-2">📋 Gerar Lista</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">📊</div>
                    <h5 class="card-title">Relatórios</h5>
                    <p class="card-text text-muted small flex-grow-1">Gere relatórios e exporte dados do sistema.</p>
                    <a href="index.php?action=relatorios" class="btn btn-outline-secondary btn-sm mt-2">Abrir Relatórios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">🔗</div>
                    <h5 class="card-title">Vincular Professores</h5>
                    <p class="card-text text-muted small flex-grow-1">Vincule professores aos alunos por disciplina.</p>
                    <a href="index.php?action=vincular_professores" class="btn btn-outline-primary btn-sm mt-2">Gerenciar Vínculos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="fs-2 mb-2">👤</div>
                    <h5 class="card-title">Colaboradores</h5>
                    <p class="card-text text-muted small flex-grow-1">Cadastre e gerencie os colaboradores do sistema.</p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="index.php?action=cadastrar_colaborador" class="btn btn-success btn-sm">+ Cadastrar</a>
                        <a href="index.php?action=listar_colaboradores" class="btn btn-outline-success btn-sm">Listar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>