<?php
$title = 'Dashboard Professor - ETC';
$brand = 'ETC - Professor Regente';
$navbarClass = 'navbar-dark bg-dark';
include __DIR__ . '/../Layout/topo.php';
?>

    <h3>Meus Planos Pedagógicos</h3>
    <p>Preencha os objetivos e avaliações das suas disciplinas.</p>
    <div class="alert alert-info">Nenhum plano pendente para este semestre.</div>
    
    <!-- Buttons Bootstrap para ações -->
    <div class="mt-4">
        <a href="index.php?action=criar_adequacao" class="btn btn-success">+ Criar Adequação</a>
        <a href="index.php?action=listar_alunos" class="btn btn-primary">Listar Alunos</a>
        <a href="index.php?action=gerar_lista_adequacao" class="btn btn-warning">📋 Gerar Lista de Adequação</a>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>