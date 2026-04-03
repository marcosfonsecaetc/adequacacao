<?php
$title = 'Plano de AEE (PAEE) - ETC';
$brand = 'ETC - Sala de Recursos';
$navbarClass = 'navbar-dark bg-primary';
include __DIR__ . '/../Layout/topo.php';

global $pdo;

$alunoId = (int)($_GET['aluno_id'] ?? 0);
if (!$alunoId) {
    // Tela de seleção de aluno
    $alunoDAO = new AlunoDAO($pdo);
    $alunos   = $alunoDAO->listarTodos();
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Plano de AEE — PAEE</h4>
            <small class="text-muted">Selecione o aluno para preencher ou editar o PAEE</small>
        </div>
    </div>

    <?php if (empty($alunos)): ?>
        <div class="alert alert-warning">Nenhum aluno cadastrado.</div>
    <?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Selecionar Aluno</h5></div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="action" value="paee">
                <div class="col-md-8">
                    <label class="form-label">Aluno</label>
                    <select name="aluno_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php foreach ($alunos as $a): ?>
                            <option value="<?php echo $a['id']; ?>">
                                <?php echo htmlspecialchars($a['nome_completo']); ?> — <?php echo htmlspecialchars($a['matricula'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Continuar →</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

<?php
    include __DIR__ . '/../Layout/rodape.php';
    exit;
}

// Carrega dados do aluno e PAEE existente
$alunoDAO = new AlunoDAO($pdo);
$aluno    = $alunoDAO->buscarPorId($alunoId);
if (!$aluno) { header('Location: index.php?action=paee'); exit; }

$stmtP = $pdo->prepare("SELECT * FROM paee WHERE aluno_id = :id");
$stmtP->execute([':id' => $alunoId]);
$paee = $stmtP->fetch(PDO::FETCH_ASSOC) ?: [];

function pv($paee, $key) {
    return htmlspecialchars($paee[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Plano de AEE — PAEE</h4>
            <small class="text-muted"><?php echo htmlspecialchars($aluno['nome_completo']); ?></small>
        </div>
        <?php if (!empty($paee)): ?>
            <a href="index.php?action=paee_pdf&aluno_id=<?php echo $alunoId; ?>" target="_blank"
               class="btn btn-outline-danger btn-sm">📥 Gerar PDF</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=processar_paee" class="row g-0">
        <input type="hidden" name="aluno_id" value="<?php echo $alunoId; ?>">

        <!-- 1. IDENTIFICAÇÃO -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">1 — Identificação do Estudante</h6></div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Modalidade do PAEE</label>
                        <input type="text" name="modalidade" class="form-control" value="<?php echo pv($paee,'modalidade') ?: 'Generalista'; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Professoras do AEE</label>
                        <input type="text" name="professoras_aee" class="form-control" placeholder="Ex: Claudia Brito (Humanas) e Lucélia Sales (Exatas)" value="<?php echo pv($paee,'professoras_aee'); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Professores Regentes (por sala/disciplina)</label>
                        <textarea name="professores_regentes" class="form-control" rows="4"
                            placeholder="Ex: Sala Linguagem de Programação — Nathanael (LP I), ...&#10;Sala Infraestrutura — João Gomes (Eletrônica), ..."><?php echo pv($paee,'professores_regentes'); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PERFIL -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white"><h6 class="mb-0">2 — Síntese do Contexto Educacional / Perfil do Estudante</h6></div>
                <div class="card-body">
                    <textarea name="perfil_estudante" class="form-control" rows="5"
                        placeholder="Descreva o perfil educacional e contexto do estudante..."><?php echo pv($paee,'perfil_estudante'); ?></textarea>
                </div>
            </div>
        </div>

        <!-- 3. ÁREAS DO DESENVOLVIMENTO -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white"><h6 class="mb-0">3 — Áreas do Desenvolvimento</h6></div>
                <div class="card-body">
                    <?php
                    $areas = [
                        ['key' => 'ling',         'label' => '3.1 Linguagem'],
                        ['key' => 'psicomotor',   'label' => '3.2 Desenvolvimento Psicomotor'],
                        ['key' => 'cognitivo',    'label' => '3.3 Desenvolvimento Cognitivo'],
                        ['key' => 'social',       'label' => '3.4 Aspectos Sociais'],
                        ['key' => 'familiar',     'label' => '3.5 Contexto Familiar'],
                    ];
                    foreach ($areas as $area): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo $area['label']; ?></label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label text-success small">Habilidades</label>
                                <textarea name="<?php echo $area['key']; ?>_habilidades" class="form-control form-control-sm" rows="2"><?php echo pv($paee, $area['key'].'_habilidades'); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-danger small">Dificuldades</label>
                                <textarea name="<?php echo $area['key']; ?>_dificuldades" class="form-control form-control-sm" rows="2"><?php echo pv($paee, $area['key'].'_dificuldades'); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 4. ACESSIBILIDADE -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark"><h6 class="mb-0">4 — Acessibilidade / Necessidades Específicas</h6></div>
                <div class="card-body">
                    <textarea name="acessibilidade" class="form-control" rows="4"
                        placeholder="Descreva recursos, materiais, tecnologia assistiva e adequações necessárias..."><?php echo pv($paee,'acessibilidade'); ?></textarea>
                </div>
            </div>
        </div>

        <!-- 5. OBJETIVOS -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white"><h6 class="mb-0">5 — Objetivos do AEE</h6></div>
                <div class="card-body">
                    <textarea name="objetivos" class="form-control" rows="4"
                        placeholder="Liste os objetivos do AEE para este estudante..."><?php echo pv($paee,'objetivos'); ?></textarea>
                </div>
            </div>
        </div>

        <!-- 6. ORGANIZAÇÃO -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white"><h6 class="mb-0">6 — Organização do AEE</h6></div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Frequência</label>
                        <input type="text" name="frequencia" class="form-control" placeholder="Ex: Quando necessário, conforme demanda" value="<?php echo pv($paee,'frequencia'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tempo de Atendimento</label>
                        <input type="text" name="tempo_atendimento" class="form-control" placeholder="Ex: 3 horas" value="<?php echo pv($paee,'tempo_atendimento'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Composição</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="composicao_individual" id="ci" value="1" <?php echo ($paee['composicao_individual'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="ci">Individual</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="composicao_grupo" id="cg" value="1" <?php echo !empty($paee['composicao_grupo']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="cg">Em grupo</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Outros / Observações sobre organização</label>
                        <textarea name="composicao_outros" class="form-control" rows="2"><?php echo pv($paee,'composicao_outros'); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. ATIVIDADES -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">7 — Atividades Pedagógicas a serem Desenvolvidas no AEE</h6></div>
                <div class="card-body">
                    <textarea name="atividades_pedagogicas" class="form-control" rows="5"
                        placeholder="Descreva as atividades por área: Cognitiva, Motora, Social..."><?php echo pv($paee,'atividades_pedagogicas'); ?></textarea>
                </div>
            </div>
        </div>

        <!-- 8. PROFISSIONAIS -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white"><h6 class="mb-0">8 — Profissionais, Familiares e/ou Instituições Envolvidos</h6></div>
                <div class="card-body">
                    <textarea name="profissionais_envolvidos" class="form-control" rows="3"
                        placeholder="Ex: Equipe gestora (X), Professor regente (X), Família (X)..."><?php echo pv($paee,'profissionais_envolvidos'); ?></textarea>
                </div>
            </div>
        </div>

        <!-- 9. AVALIAÇÃO -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white"><h6 class="mb-0">9 — Avaliação dos Resultados</h6></div>
                <div class="card-body">
                    <textarea name="avaliacao_resultados" class="form-control" rows="3"
                        placeholder="Descreva como será feita a avaliação dos resultados..."><?php echo pv($paee,'avaliacao_resultados'); ?></textarea>
                </div>
            </div>
        </div>

        <!-- 10. ENCAMINHAMENTOS -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white"><h6 class="mb-0">10 — Encaminhamentos</h6></div>
                <div class="card-body row g-3">
                    <div class="col-md-8">
                        <textarea name="encaminhamentos" class="form-control" rows="3"
                            placeholder="Descreva os encaminhamentos necessários..."><?php echo pv($paee,'encaminhamentos'); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data do Registro</label>
                        <input type="date" name="data_registro" class="form-control"
                               value="<?php echo pv($paee,'data_registro') ?: date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-between mb-4">
            <a href="index.php?action=dashboard" class="btn btn-secondary">← Voltar</a>
            <div class="d-flex gap-2">
                <?php if (!empty($paee)): ?>
                    <a href="index.php?action=paee_pdf&aluno_id=<?php echo $alunoId; ?>" target="_blank"
                       class="btn btn-outline-danger">📥 Gerar PDF</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">💾 Salvar PAEE</button>
            </div>
        </div>
    </form>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
