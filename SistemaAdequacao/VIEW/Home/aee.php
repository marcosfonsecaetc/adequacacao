<?php
$title = 'Dashboard AEE - ETC';
$brand = 'ETC - Sala de Recursos';
$navbarClass = 'navbar-dark bg-primary';
include __DIR__ . '/../Layout/topo.php';
?>

    <h3>Minhas Adequações Curriculares</h3>
    <p>Preencha as informações biopsicossociais e de escolarização dos alunos.</p>
    <div class="alert alert-info">Nenhuma adequação pendente para este semestre.</div>
    
    <!-- Buttons Bootstrap para ações -->
    <div class="mt-4">
        <a href="index.php?action=incluir_informacoes_aluno" class="btn btn-success">+ Incluir Informações do Aluno</a>
        <a href="index.php?action=listar_alunos" class="btn btn-primary">Listar Alunos</a>
        <a href="index.php?action=gerar_lista_adequacao" class="btn btn-warning">📋 Gerar Lista de Adequação</a>
    </div>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>