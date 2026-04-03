<?php
$title = 'Perfil do Estudante - ETC';
$brand = 'ETC - Sala de Recursos';
$navbarClass = 'navbar-dark bg-primary';
include __DIR__ . '/../Layout/topo.php';

global $pdo;

$alunoId = (int)($_GET['aluno_id'] ?? 0);
if (!$alunoId) {
    $alunoDAO = new AlunoDAO($pdo);
    $alunos   = $alunoDAO->listarTodos();

    // Turmas distintas
    $stmtTurmas = $pdo->query("SELECT DISTINCT modalidade_ano_turma_turno FROM aluno WHERE modalidade_ano_turma_turno IS NOT NULL AND modalidade_ano_turma_turno != '' ORDER BY modalidade_ano_turma_turno ASC");
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_COLUMN);

    // Filtro de turma para o relatório
    $turmaSel = trim($_GET['turma'] ?? '');
    $alunosFiltrados = [];
    if ($turmaSel) {
        $stmtF = $pdo->prepare("SELECT a.*, pe.vida_pessoal, pe.acompanhamento_medico, pe.id AS pe_id FROM aluno a LEFT JOIN perfil_estudante pe ON pe.aluno_id = a.id WHERE a.modalidade_ano_turma_turno = :t ORDER BY a.nome_completo ASC");
        $stmtF->execute([':t' => $turmaSel]);
        $alunosFiltrados = $stmtF->fetchAll(PDO::FETCH_ASSOC);
    }
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="mb-0">Perfil do Estudante</h4><small class="text-muted">Selecione o aluno para preencher a anamnese</small></div>
    </div>

    <!-- Seleção individual -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><h6 class="mb-0">Preencher / Editar Perfil Individual</h6></div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-2">
                <input type="hidden" name="action" value="perfil_estudante">
                <div class="col-md-9">
                    <select name="aluno_id" class="form-select" required>
                        <option value="">-- Selecione --</option>
                        <?php foreach ($alunos as $a): ?>
                            <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['nome_completo']); ?> — <?php echo htmlspecialchars($a['matricula'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Continuar →</button></div>
            </form>
        </div>
    </div>

    <!-- Relatório Geral por Turma -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white"><h6 class="mb-0">📊 Relatório Geral de Perfis por Turma</h6></div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-2 mb-3">
                <input type="hidden" name="action" value="perfil_estudante">
                <div class="col-md-8">
                    <label class="form-label">Filtrar por Curso / Turma</label>
                    <select name="turma" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Selecione uma turma --</option>
                        <?php foreach ($turmas as $t): ?>
                            <option value="<?php echo htmlspecialchars($t); ?>" <?php echo $turmaSel === $t ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <?php if ($turmaSel && !empty($alunosFiltrados)): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small"><?php echo count($alunosFiltrados); ?> aluno(s) encontrado(s)</span>
                    <a href="index.php?action=perfil_pdf_turma&turma=<?php echo urlencode($turmaSel); ?>" target="_blank"
                       class="btn btn-danger btn-sm">🖨️ Gerar PDF da Turma</a>
                </div>
                <div class="row g-3">
                    <?php foreach ($alunosFiltrados as $af): ?>
                        <div class="col-md-6">
                            <div class="card border shadow-sm h-100">
                                <div class="card-body d-flex gap-3 align-items-center">
                                    <?php if (!empty($af['foto'])): ?>
                                        <img src="SistemaAdequacao/<?php echo htmlspecialchars($af['foto']); ?>" alt="Foto"
                                             style="width:56px;height:56px;object-fit:cover;border-radius:50%;border:2px solid #1a6fa8;">
                                    <?php else: ?>
                                        <div style="width:56px;height:56px;border-radius:50%;background:#e0e8f0;display:flex;align-items:center;justify-content:center;font-size:22px;">👤</div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small"><?php echo htmlspecialchars($af['nome_completo']); ?></div>
                                        <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($af['cid_codigos'] ?? '—'); ?></div>
                                        <div class="text-muted" style="font-size:11px;"><?php echo !empty($af['pe_id']) ? '<span class="badge bg-success">Perfil preenchido</span>' : '<span class="badge bg-warning text-dark">Sem perfil</span>'; ?></div>
                                    </div>
                                    <a href="index.php?action=perfil_estudante&aluno_id=<?php echo $af['id']; ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                                    <a href="index.php?action=perfil_pdf_perfil&aluno_id=<?php echo $af['id']; ?>" target="_blank" class="btn btn-sm btn-outline-danger">🖨️</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($turmaSel): ?>
                <div class="alert alert-info mb-0">Nenhum aluno encontrado para esta turma.</div>
            <?php endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/../Layout/rodape.php'; exit; }

$alunoDAO = new AlunoDAO($pdo);
$aluno    = $alunoDAO->buscarPorId($alunoId);
if (!$aluno) { header('Location: index.php?action=perfil_estudante'); exit; }

$stmt = $pdo->prepare("SELECT * FROM perfil_estudante WHERE aluno_id = :id");
$stmt->execute([':id' => $alunoId]);
$p = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

function pv($p, $k, $d = '') { return htmlspecialchars($p[$k] ?? $d, ENT_QUOTES, 'UTF-8'); }
function pc($p, $k) { return !empty($p[$k]) ? 'checked' : ''; }
function psel($p, $k, $v) { return ($p[$k] ?? '') === $v ? 'selected' : ''; }

$niveis = ['nao_alfabetizado'=>'Não Alfabetizado','alfabetizado'=>'Alfabetizado','ef1_regular'=>'Ensino Fundamental I Regular','ef2_supletivo'=>'Ensino Fundamental II Supletivo','ef2_regular'=>'Ensino Fundamental II Regular','ef3_supletivo'=>'Ensino Fundamental III Supletivo','em_regular'=>'Ensino Médio Regular','em_supletivo'=>'Ensino Médio Supletivo','eja'=>'Educação de Jovens e Adultos','profissionalizante'=>'Ensino Profissionalizante'];
$infOpts = ['completo'=>'Informou Completamente','parcial'=>'Informou Parcialmente','nao_soube'=>'Não Soube Informar'];
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="mb-0">Perfil do Estudante — Anamnese</h4><small class="text-muted"><?php echo htmlspecialchars($aluno['nome_completo']); ?></small></div>
        <?php if (!empty($p)): ?>
            <div class="d-flex gap-2">
                <a href="index.php?action=perfil_pdf&aluno_id=<?php echo $alunoId; ?>" target="_blank" class="btn btn-outline-primary btn-sm">🖨️ FORMULÁRIO ANAMNESE</a>
                <a href="index.php?action=perfil_pdf_perfil&aluno_id=<?php echo $alunoId; ?>" target="_blank" class="btn btn-outline-danger btn-sm">🖨️ PDF Perfil do Estudante</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=processar_perfil_estudante">
        <input type="hidden" name="aluno_id" value="<?php echo $alunoId; ?>">

        <!-- 1. IDENTIFICAÇÃO -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white"><h6 class="mb-0">1 — Identificação</h6></div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Naturalidade</label>
                    <input type="text" name="naturalidade" class="form-control" value="<?php echo pv($p,'naturalidade'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telefone do Aluno</label>
                    <input type="text" name="telefone_aluno" class="form-control" value="<?php echo pv($p,'telefone_aluno'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data do Registro</label>
                    <input type="date" name="data_registro" class="form-control" value="<?php echo pv($p,'data_registro') ?: date('Y-m-d'); ?>">
                </div>
            </div>
        </div>

        <!-- 2. AVALIAÇÃO -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-secondary text-white"><h6 class="mb-0">2 — Avaliação</h6></div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tem laudo atualizado?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="laudo_atualizado" value="1" <?php echo ($p['laudo_atualizado'] ?? '') == 1 ? 'checked' : ''; ?>><label class="form-check-label">Sim</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="laudo_atualizado" value="0" <?php echo ($p['laudo_atualizado'] ?? 0) == 0 ? 'checked' : ''; ?>><label class="form-check-label">Não</label></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">De quando?</label>
                    <input type="text" name="laudo_data" class="form-control" value="<?php echo pv($p,'laudo_data'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Faz acompanhamento médico, terapia ou tratamento? Onde?</label>
                    <textarea name="acompanhamento_medico" class="form-control" rows="2"><?php echo pv($p,'acompanhamento_medico'); ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Usa medicamento?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="usa_medicamento" value="1" <?php echo ($p['usa_medicamento'] ?? '') == 1 ? 'checked' : ''; ?>><label class="form-check-label">Sim</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="usa_medicamento" value="0" <?php echo ($p['usa_medicamento'] ?? 0) == 0 ? 'checked' : ''; ?>><label class="form-check-label">Não</label></div>
                    </div>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Qual? Dosagem?</label>
                    <input type="text" name="medicamento_detalhes" class="form-control" value="<?php echo pv($p,'medicamento_detalhes'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Independência nas AVDs?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="independencia_avd" value="1" <?php echo ($p['independencia_avd'] ?? '') == 1 ? 'checked' : ''; ?>><label class="form-check-label">Sim</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="independencia_avd" value="0" <?php echo ($p['independencia_avd'] ?? 0) == 0 ? 'checked' : ''; ?>><label class="form-check-label">Não</label></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Independência na locomoção?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="independencia_locomocao" value="1" <?php echo ($p['independencia_locomocao'] ?? '') == 1 ? 'checked' : ''; ?>><label class="form-check-label">Sim</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="independencia_locomocao" value="0" <?php echo ($p['independencia_locomocao'] ?? 0) == 0 ? 'checked' : ''; ?>><label class="form-check-label">Não</label></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Recebe auxílio do governo?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="auxilio_governo" value="1" <?php echo ($p['auxilio_governo'] ?? '') == 1 ? 'checked' : ''; ?>><label class="form-check-label">Sim</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="auxilio_governo" value="0" <?php echo ($p['auxilio_governo'] ?? 0) == 0 ? 'checked' : ''; ?>><label class="form-check-label">Não</label></div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Documentação</label>
                    <div class="row g-2">
                        <?php
                        $docs = ['doc_rg'=>'RG – Registro Geral','doc_cpf'=>'CPF – Cadastro de Pessoa Física','doc_ctps'=>'CTPS – Carteira de Trabalho','doc_titulo'=>'Título de Eleitor','doc_reservista'=>'Reservista Militar','doc_sus'=>'Cartão do SUS'];
                        foreach ($docs as $key => $label): ?>
                        <div class="col-md-4">
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo pc($p,$key); ?>><label class="form-check-label"><?php echo $label; ?></label></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ESCOLARIDADE -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-info text-white"><h6 class="mb-0">3 — Escolaridade</h6></div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Última escola que estudou ou ainda estuda</label>
                    <input type="text" name="ultima_escola" class="form-control" value="<?php echo pv($p,'ultima_escola'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estudou em escola especial?</label>
                    <input type="text" name="escola_especial" class="form-control" value="<?php echo pv($p,'escola_especial'); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Por quanto tempo?</label>
                    <input type="text" name="escola_especial_tempo" class="form-control" value="<?php echo pv($p,'escola_especial_tempo'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Nível de Escolaridade</label>
                    <select name="escolaridade_nivel" class="form-select">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($niveis as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo psel($p,'escolaridade_nivel',$val); ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. QUESTÕES SOCIOBIOGRÁFICAS -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-warning text-dark"><h6 class="mb-0">4 — Questões Sociobiográficas</h6></div>
            <div class="card-body">
                <?php
                $perguntas = [
                    ['rel_pai',            'Você se relaciona bem com seu pai?'],
                    ['rel_mae',            'Você se relaciona bem com sua mãe?'],
                    ['irmaos',             'Você tem irmão? Quantos? Quais os nomes deles?'],
                    ['irmaos_rel',         'Você se relaciona bem com eles?'],
                    ['onibus_sozinho',     'Você anda de ônibus sozinho?'],
                    ['sabe_ler_escrever',  'Você sabe ler e escrever?'],
                    ['dia_a_dia',          'Você faz o que no seu dia-a-dia? E nos fins de semana?'],
                    ['escola_anterior',    'Você frequentava outra escola antes de vir para ETC? Qual? O que fazia lá?'],
                    ['vida_pessoal',       'Fale um pouco da sua vida (o que mais gosta de fazer, quais as pessoas que mais gosta)?'],
                    ['opiniao_escola',     'O que você achou da escola?'],
                    ['experiencia_trabalho','Você já trabalhou? Se sim, onde? Fale da sua experiência.'],
                    ['planos_futuro',      'O que você pensa da sua vida no futuro?'],
                    ['curso_interesse',    'Você quer aprender alguma atividade aqui na escola? Ou quer fazer outro curso?'],
                    ['relacionamento',     'Você tem namorada(o)? Se sim, fale sobre ela(ele).'],
                    ['outras_informacoes', 'Que outras informações você gostaria de dar?'],
                ];
                $semInf = ['rel_pai','rel_mae','irmaos_rel','onibus_sozinho','sabe_ler_escrever'];
                foreach ($perguntas as [$key, $pergunta]):
                    $temInf = !in_array($key, $semInf);
                ?>
                <div class="row g-2 mb-3 align-items-start border-bottom pb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold"><?php echo $pergunta; ?></label>
                        <?php if (!$temInf): ?>
                            <select name="<?php echo $key; ?>" class="form-select form-select-sm">
                                <?php foreach ($infOpts as $v => $l): ?>
                                    <option value="<?php echo $v; ?>" <?php echo psel($p, $key, $v); ?>><?php echo $l; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <textarea name="<?php echo $key; ?>" class="form-control form-control-sm" rows="2"><?php echo pv($p,$key); ?></textarea>
                        <?php endif; ?>
                    </div>
                    <?php if ($temInf): ?>
                    <div class="col-md-4">
                        <label class="form-label small">Resposta</label>
                        <select name="<?php echo $key; ?>_inf" class="form-select form-select-sm">
                            <?php foreach ($infOpts as $v => $l): ?>
                                <option value="<?php echo $v; ?>" <?php echo psel($p, $key.'_inf', $v); ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- DETECTAR -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-dark text-white"><h6 class="mb-0">Detectar</h6></div>
            <div class="card-body">
                <p class="text-muted small">É importante que o entrevistador observe a capacidade de compreensão do vocabulário utilizado pelo(a) aluno(a).</p>
                <div class="row g-2">
                    <?php
                    $detectar = ['detectar_fluidez'=>'Fluidez verbal','detectar_encadeamento'=>'Encadeamento de ideias','detectar_independencia'=>'Independência de ideias','detectar_introversao'=>'Introversão','detectar_extroversao'=>'Extroversão','detectar_projeto_vida'=>'Se tem ou não um projeto de vida'];
                    foreach ($detectar as $key => $label): ?>
                    <div class="col-md-4">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo pc($p,$key); ?>><label class="form-check-label"><?php echo $label; ?></label></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="index.php?action=dashboard" class="btn btn-secondary">← Voltar</a>
            <div class="d-flex gap-2">
                <?php if (!empty($p)): ?>
                    <a href="index.php?action=perfil_pdf&aluno_id=<?php echo $alunoId; ?>" target="_blank" class="btn btn-outline-primary">🖨️ FORMULÁRIO ANAMNESE</a>
                    <a href="index.php?action=perfil_pdf_perfil&aluno_id=<?php echo $alunoId; ?>" target="_blank" class="btn btn-outline-danger">🖨️ PDF Perfil do Estudante</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">💾 Salvar</button>
            </div>
        </div>
    </form>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
