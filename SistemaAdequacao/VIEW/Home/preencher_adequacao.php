<?php
$title = 'Preencher Adequação - Seção 2: Escolarização e Saúde';

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

// Garante que a conexão PDO seja acessível
global $pdo;

// Verifica se recebeu o aluno_id via GET
$alunoId = $_GET['aluno_id'] ?? null;
if (!$alunoId) {
    $_SESSION['flash_error'] = 'Erro: Nenhum aluno selecionado.';
    header('Location: index.php?action=criar_adequacao');
    exit;
}

// Carrega dados do aluno
$alunoDAO = new AlunoDAO($pdo);
$aluno = $alunoDAO->buscarPorId($alunoId);
if (!$aluno) {
    $_SESSION['flash_error'] = 'Erro: Aluno não encontrado.';
    header('Location: index.php?action=criar_adequacao');
    exit;
}
?>

    <h3>Preencher Adequação Curricular</h3>
    
    <?php if ($papelId == 3): // AEE - Sala de Recursos ?>
        <p>Seção 2: Escolarização e Saúde</p>
    <?php elseif ($papelId == 4): // Professor ?>
        <p>Seção 8: Plano Pedagógico</p>
    <?php else: ?>
        <p>Acesso não autorizado para este papel.</p>
    <?php endif; ?>

    <!-- Card com dados do aluno selecionado -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Dados do Aluno: <?php echo htmlspecialchars($aluno['nome_completo']); ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nome:</strong> <?php echo htmlspecialchars($aluno['nome_completo']); ?><br>
                    <strong>Data Nasc.:</strong> <?php echo htmlspecialchars($aluno['data_nascimento'] ?? '-'); ?><br>
                    <strong>Idade:</strong> <?php echo htmlspecialchars($aluno['idade'] ?? '-'); ?>
                </div>
                <div class="col-md-6">
                    <strong>Modalidade/Turma:</strong> <?php echo htmlspecialchars($aluno['modalidade_ano_turma_turno'] ?? '-'); ?><br>
                    <strong>Diagnóstico:</strong> <?php echo htmlspecialchars($aluno['diagnostico_detalhado'] ?? '-'); ?><br>
                    <strong>CID:</strong> <?php echo htmlspecialchars($aluno['cid_codigos'] ?? '-'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário dinâmico baseado no papel do usuário -->
    <div class="card">
        <?php if ($papelId == 3): // AEE - Seção 2: Escolarização e Saúde ?>
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Seção 2: Escolarização e Saúde</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=processar_secao_2">
                    <!-- Campo oculto com aluno_id -->
                    <input type="hidden" name="aluno_id" value="<?php echo htmlspecialchars($alunoId); ?>">

                    <div class="mb-3">
                        <label for="percurso_escolarizacao" class="form-label">
                            2.1 Percurso de Escolarização <span class="text-danger">*</span>
                        </label>
                        <textarea id="percurso_escolarizacao" name="percurso_escolarizacao" class="form-control" rows="3"
                            placeholder="Descreva o histórico escolar do aluno..." required></textarea>
                        <div class="form-text">Ex: concluiu 8º ano no ensino fundamental, etc.</div>
                    </div>

                    <div class="mb-3">
                        <label for="atendimentos_passados" class="form-label">
                            2.2 Atendimentos Passados
                        </label>
                        <textarea id="atendimentos_passados" name="atendimentos_passados" class="form-control" rows="3"
                            placeholder="Descreva atendimentos anteriores (fonoaudiologia, terapia ocupacional, etc.)"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="atendimentos_atuais" class="form-label">
                            2.3 Atendimentos Atuais
                        </label>
                        <textarea id="atendimentos_atuais" name="atendimentos_atuais" class="form-control" rows="3"
                            placeholder="Descreva atendimentos em andamento"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">2.4 Faz uso de medicação?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="faz_uso_medicacao" id="medicacao_nao" value="0" checked>
                            <label class="form-check-label" for="medicacao_nao">
                                Não
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="faz_uso_medicacao" id="medicacao_sim" value="1">
                            <label class="form-check-label" for="medicacao_sim">
                                Sim
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="medicacao_especificacao_div" style="display:none;">
                        <label for="medicacao_especificacao" class="form-label">
                            2.5 Especificação da Medicação
                        </label>
                        <textarea id="medicacao_especificacao" name="medicacao_especificacao" class="form-control" rows="2"
                            placeholder="Descreva a medicação utilizada"></textarea>
                    </div>

                    <!-- Botões de navegação -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="index.php?action=criar_adequacao" class="btn btn-secondary">
                            ← Voltar
                        </a>
                        <button type="submit" class="btn btn-success">
                            Próxima Seção →
                        </button>
                    </div>
                </form>
            </div>

        <?php elseif ($papelId == 4): // Professor - Plano Pedagógico por Disciplina ?>
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Plano Pedagógico por Disciplina</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=processar_plano_pedagogico">
                    <input type="hidden" name="aluno_id" value="<?php echo htmlspecialchars($alunoId); ?>">
                    <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($_SESSION['usuario_id']); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Disciplina <span class="text-danger">*</span></label>
                            <input type="text" name="disciplina_nome" class="form-control"
                                placeholder="Ex: Redes de Computadores, Banco de Dados..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Período de Vigência <span class="text-danger">*</span></label>
                            <input type="text" name="periodo_vigencia_semestral" class="form-control"
                                placeholder="Ex: 1º Semestre (12/02 a 10/07/2026)" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Etapa de Ensino</label>
                        <input type="text" name="etapa_ensino" class="form-control"
                            value="Ensino Profissionalizante" readonly>
                    </div>

                    <!-- Seção 8: Plano Pedagógico -->
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">Seção 8 — Plano Pedagógico</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Objetivos de Aprendizagem <span class="text-danger">*</span></label>
                                <textarea name="objetivos_aprendizagem" class="form-control" rows="3"
                                    placeholder="Descreva os objetivos específicos para este aluno nesta disciplina..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Conteúdos Didáticos <span class="text-danger">*</span></label>
                                <textarea name="conteudos_didaticos" class="form-control" rows="3"
                                    placeholder="Liste os conteúdos a serem trabalhados..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estratégias Pedagógicas <span class="text-danger">*</span></label>
                                <textarea name="estrategias_pedagogicas" class="form-control" rows="3"
                                    placeholder="Descreva as estratégias e recursos didáticos..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estratégias de Avaliação <span class="text-danger">*</span></label>
                                <textarea name="estrategias_avaliacao" class="form-control" rows="3"
                                    placeholder="Descreva como será feita a avaliação (menção APTO/NÃO APTO)..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Adequações específicas da disciplina -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">Adequações Específicas desta Disciplina</div>
                        <div class="card-body">
                            <p class="text-muted small">Complemente as adequações gerais do AEE com as necessidades específicas da sua disciplina.</p>
                            <div class="mb-3">
                                <label class="form-label">Adequação de Espaço / Organização</label>
                                <textarea name="adequacao_espaco" class="form-control" rows="2"
                                    placeholder="Ex: Aluno deve sentar próximo ao projetor para visualizar melhor os diagramas..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Recursos Específicos da Disciplina</label>
                                <textarea name="adequacao_recursos" class="form-control" rows="2"
                                    placeholder="Ex: Material de redes em Braille, simuladores acessíveis, audiodescrição de topologias..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Flexibilização de Tempo na Disciplina</label>
                                <textarea name="adequacao_tempo" class="form-control" rows="2"
                                    placeholder="Ex: 50% de tempo extra nas avaliações práticas de configuração..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Forma de Avaliação Adaptada</label>
                                <textarea name="adequacao_avaliativa" class="form-control" rows="2"
                                    placeholder="Ex: Avaliação oral em substituição à escrita, portfólio de atividades práticas..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="index.php?action=dashboard" class="btn btn-secondary">← Voltar ao Dashboard</a>
                        <button type="submit" class="btn btn-success">Salvar Plano Pedagógico</button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5>Acesso Negado</h5>
                    <p>Você não tem permissão para acessar esta funcionalidade.</p>
                    <a href="index.php?action=dashboard" class="btn btn-primary">Voltar ao Dashboard</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript para mostrar/esconder campo de medicação (apenas para AEE) -->
    <?php if ($papelId == 3): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const medicacaoRadios = document.querySelectorAll('input[name="faz_uso_medicacao"]');
            const medicacaoDiv = document.getElementById('medicacao_especificacao_div');

            medicacaoRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === '1') {
                        medicacaoDiv.style.display = 'block';
                    } else {
                        medicacaoDiv.style.display = 'none';
                        document.getElementById('medicacao_especificacao').value = '';
                    }
                });
            });
        });
    </script>
    <?php endif; ?>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
