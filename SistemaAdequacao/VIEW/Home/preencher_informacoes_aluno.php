<?php
$title = 'Preencher Informações do Aluno - ETC';
$brand = 'ETC - Sala de Recursos';
$navbarClass = 'navbar-dark bg-primary';
include __DIR__ . '/../Layout/topo.php';

// Verifica se aluno_id foi passado
$alunoId = $_GET['aluno_id'] ?? null;
if (!$alunoId) {
    $_SESSION['flash_error'] = 'Erro: Aluno não especificado.';
    header('Location: index.php?action=incluir_informacoes_aluno');
    exit;
}

// Garante que a conexão PDO seja acessível
global $pdo;

// Carrega dados do aluno
$alunoDAO = new AlunoDAO($pdo);
$aluno = $alunoDAO->buscarPorId($alunoId);
if (!$aluno) {
    $_SESSION['flash_error'] = 'Erro: Aluno não encontrado.';
    header('Location: index.php?action=incluir_informacoes_aluno');
    exit;
}
?>

    <h3>Informações Biopsicossociais do Aluno</h3>
    <p>Preencha as informações detalhadas do aluno: <strong><?php echo htmlspecialchars($aluno['nome_completo']); ?></strong></p>

    <!-- Formulário com múltiplas seções -->
    <form method="POST" action="index.php?action=processar_informacoes_aluno">
        <!-- Campo oculto com aluno_id -->
        <input type="hidden" name="aluno_id" value="<?php echo htmlspecialchars($alunoId); ?>">

        <!-- Seção 1: Dados Básicos (já preenchidos) -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Seção 1: Dados Básicos do Aluno</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Foto do Aluno</label>
                        <div>
                            <?php if (!empty($aluno['foto']) && file_exists(__DIR__ . '/../../' . $aluno['foto'])): ?>
                                <img src="/SistemaAdequacao/<?php echo htmlspecialchars($aluno['foto']); ?>" alt="Foto do aluno" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                            <?php else: ?>
                                <div class="text-muted">Nenhuma foto cadastrada</div>
                            <?php endif; ?>
                            <div class="mt-2">
                                <a href="index.php?action=editar_aluno&aluno_id=<?php echo (int)$alunoId; ?>" class="btn btn-sm btn-outline-secondary">✏️ Alterar foto</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['nome_completo']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Matrícula</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($aluno['matricula'] ?? 'N/A'); ?>" readonly>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($aluno['email'] ?? 'N/A'); ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção 2: Escolarização e Saúde -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Seção 2: Escolarização e Saúde</h5>
            </div>
            <div class="card-body">
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
            </div>
        </div>

        <!-- Seção 3: Habilidades Biopsicossociais -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Seção 3: Habilidades Biopsicossociais</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="comunicacao" class="form-label">3.1 Comunicação (Oral, Tátil, Braille)</label>
                    <textarea id="comunicacao" name="comunicacao" class="form-control" rows="3"
                        placeholder="Descreva a forma de comunicação do aluno..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="aspectos_motores" class="form-label">3.2 Aspectos Motores (Marcha, Coordenação Fina)</label>
                    <textarea id="aspectos_motores" name="aspectos_motores" class="form-control" rows="3"
                        placeholder="Descreva os aspectos motores do aluno..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="vida_autonoma" class="form-label">3.3 Vida Autônoma (Independência, AVDs)</label>
                    <textarea id="vida_autonoma" name="vida_autonoma" class="form-control" rows="3"
                        placeholder="Descreva a autonomia do aluno..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="aspectos_sociais" class="form-label">3.4 Aspectos Sociais (Relacionamento, Frustração)</label>
                    <textarea id="aspectos_sociais" name="aspectos_sociais" class="form-control" rows="3"
                        placeholder="Descreva as habilidades sociais do aluno..."></textarea>
                </div>
            </div>
        </div>

        <!-- Seção 4: Adequação Organizativa -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Seção 4: Adequação Organizativa</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="espaco_sala" class="form-label">4.1 Espaço em Sala (Proximidade sonora, organização de móveis)</label>
                    <textarea id="espaco_sala" name="espaco_sala" class="form-control" rows="3"
                        placeholder="Descreva as adequações de espaço necessárias..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="recursos_gerais" class="form-label">4.2 Recursos Gerais (Tecnologia Assistiva: Braille, NVDA)</label>
                    <textarea id="recursos_gerais" name="recursos_gerais" class="form-control" rows="3"
                        placeholder="Descreva os recursos necessários..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="flexibilizacao_tempo" class="form-label">4.3 Flexibilização de Tempo (Acréscimo de 50% a 100%)</label>
                    <textarea id="flexibilizacao_tempo" name="flexibilizacao_tempo" class="form-control" rows="3"
                        placeholder="Descreva a flexibilização de tempo necessária..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="avaliacao_diagnostica" class="form-label">4.4 Avaliação Diagnóstica (Engajamento, Escuta Ativa)</label>
                    <textarea id="avaliacao_diagnostica" name="avaliacao_diagnostica" class="form-control" rows="3"
                        placeholder="Descreva a avaliação diagnóstica..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="adequacao_temporalidade_legal" class="form-label">4.5 Adequação de Temporalidade Legal (Regimento Escolar Art. 201)</label>
                    <textarea id="adequacao_temporalidade_legal" name="adequacao_temporalidade_legal" class="form-control" rows="3"
                        placeholder="Descreva a adequação de temporalidade legal..."></textarea>
                </div>
            </div>
        </div>

        <!-- Botões de navegação -->
        <div class="mt-4 d-flex justify-content-between">
            <a href="index.php?action=incluir_informacoes_aluno" class="btn btn-secondary">
                ← Voltar
            </a>
            <button type="submit" class="btn btn-success">
                Salvar Informações
            </button>
        </div>
    </form>

    <!-- JavaScript para mostrar/esconder campo de medicação -->
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

<?php include __DIR__ . '/../Layout/rodape.php'; ?>