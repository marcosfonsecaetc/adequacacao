<?php
$title = 'Dashboard Gestão Escolar - ETC';
$brand = 'ETC - Gestão Escolar';
$navbarClass = 'navbar-dark bg-success';
include __DIR__ . '/../Layout/topo.php';
?>

    <h3>Bem-vindo(a), Gestor(a)</h3>
    <p>Área administrativa para monitoramento de planos e desempenho.</p>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Visão Geral</h5>
                    <p class="card-text">Resumo de adequações e usuários registrados.</p>
                    <a href="index.php?action=listar_alunos" class="btn btn-primary">Listar Alunos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Relatórios</h5>
                    <p class="card-text">Gere relatórios e exporte dados.</p>
                    <a href="#" class="btn btn-primary">Abrir relatórios</a>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>