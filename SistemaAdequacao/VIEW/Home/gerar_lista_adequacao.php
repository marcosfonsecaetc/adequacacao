<?php
$extraHead = '
<style>
@media print {
    @page { size: A4 landscape; margin: 10mm; }
    body { background: #fff !important; font-size: 10px; }
    nav, .card.mb-3, .btn, a.btn { display: none !important; }
    h3 { font-size: 13px; margin-bottom: 6px; }
    #tabelaAdequacoes { width: 100%; border-collapse: collapse; page-break-inside: auto; }
    #tabelaAdequacoes thead { display: table-header-group; }
    #tabelaAdequacoes tr { page-break-inside: avoid; page-break-after: auto; }
    #tabelaAdequacoes th, #tabelaAdequacoes td { border: 1px solid #333 !important; padding: 3px 5px !important; vertical-align: top; }
    #tabelaAdequacoes th { background: #222 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .detalhe-row, .modal { display: none !important; }
    td.d-flex { display: none !important; }
}
</style>
';
$papelId = $_SESSION['usuario_papel'] ?? null;
if ($papelId == 4) {
    $brand = 'ETC - Professor Regente';
    $navbarClass = 'navbar-dark bg-dark';
} elseif ($papelId == 3) {
    $brand = 'ETC - Sala de Recursos';
    $navbarClass = 'navbar-dark bg-primary';
} else {
    $brand = 'ETC - Sistema';
    $navbarClass = 'navbar-dark bg-secondary';
}
$title = 'Lista de Adequações - ETC';
include __DIR__ . '/../Layout/topo.php';

global $pdo;

// Busca todos os alunos que possuem pelo menos um registro nas tabelas do AEE
$stmt = $pdo->query("
    SELECT DISTINCT a.id, a.nome_completo, a.matricula, a.modalidade_ano_turma_turno,
                    a.diagnostico_detalhado, a.cid_codigos, a.periodo_vigencia_adequacao, a.foto
    FROM aluno a
    WHERE EXISTS (SELECT 1 FROM saude_escolarizacao    WHERE aluno_id = a.id)
       OR EXISTS (SELECT 1 FROM habilidade_biopsicossocial WHERE aluno_id = a.id)
       OR EXISTS (SELECT 1 FROM adequacao_organizativa WHERE aluno_id = a.id)
    ORDER BY a.nome_completo ASC
");
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Lista de Adequações Curriculares</h3>
        <button id="btnImprimir" class="btn btn-outline-secondary btn-sm">🖨️ Imprimir</button>
    </div>

    <?php if (empty($alunos)): ?>
        <div class="alert alert-info">Nenhuma adequação registrada ainda.</div>
    <?php else: ?>

    <!-- Busca e combo -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" id="campoBusca" class="form-control" placeholder="Buscar por nome ou matrícula...">
                </div>
                <div class="col-md-5">
                    <select id="comboAluno" class="form-select">
                        <option value="">-- Selecione um aluno --</option>
                        <?php foreach ($alunos as $aluno): ?>
                            <option value="tr-<?php echo $aluno['id']; ?>">
                                <?php echo htmlspecialchars($aluno['nome_completo']); ?>
                                <?php if (!empty($aluno['matricula'])): ?> — <?php echo htmlspecialchars($aluno['matricula']); ?><?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="btnLimpar" class="btn btn-outline-secondary w-100">Limpar</button>
                </div>
            </div>
        </div>
    </div>

        <table class="table table-bordered table-hover align-middle" id="tabelaAdequacoes">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Modalidade / Turma</th>
                    <th>Diagnóstico</th>
                    <th>CID</th>
                    <th>Vigência</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alunos as $i => $aluno): ?>
                    <?php
                    $alunoDAO = new AlunoDAO($pdo);
                    $aee = $alunoDAO->buscarDadosAEE((int)$aluno['id']);
                    $s = $aee['saude']      ?? [];
                    $h = $aee['habilidade'] ?? [];
                    $ad = $aee['adequacao'] ?? [];
                    $rowId = 'detalhe-' . $aluno['id'];
                    ?>
                    <!-- Linha principal -->
                    <tr id="tr-<?php echo $aluno['id']; ?>"
                        data-nome="<?php echo strtolower(htmlspecialchars($aluno['nome_completo'])); ?>"
                        data-matricula="<?php echo strtolower(htmlspecialchars($aluno['matricula'] ?? '')); ?>">
                        <td><strong><?php echo htmlspecialchars($aluno['nome_completo']); ?></strong></td>
                        <td><?php echo htmlspecialchars($aluno['matricula'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['modalidade_ano_turma_turno'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['diagnostico_detalhado'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cid_codigos'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['periodo_vigencia_adequacao'] ?? '-'); ?></td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary btn-aee"
                                data-id="<?php echo $aluno['id']; ?>" title="Informações AEE">+</button>
                            <button class="btn btn-sm btn-outline-success btn-ficha"
                                data-id="<?php echo $aluno['id']; ?>" title="Gerar Ficha Completa">📄 Ficha</button>
                            <a href="index.php?action=gerar_pdf_ficha&aluno_id=<?php echo $aluno['id']; ?>" target="_blank"
                                class="btn btn-sm btn-outline-danger" title="Gerar PDF">📥 PDF</a>
                        </td>
                    </tr>
                    <!-- Dados AEE em JSON para o modal -->
                    <?php
                    $aeeJson = json_encode([
                        'nome'                          => $aluno['nome_completo'],
                        'foto'                          => !empty($aluno['foto']) ? 'http://' . $_SERVER['HTTP_HOST'] . '/SistemaAdequacao/' . $aluno['foto'] : '',
                        'percurso_escolarizacao'        => $s['percurso_escolarizacao']        ?? '-',
                        'atendimentos_passados'         => $s['atendimentos_passados']         ?? '-',
                        'atendimentos_atuais'           => $s['atendimentos_atuais']           ?? '-',
                        'medicacao'                     => ($s['faz_uso_medicacao'] ?? 0) == 1 ? 'Sim — ' . ($s['medicacao_especificacao'] ?? '') : 'Não',
                        'comunicacao'                   => $h['comunicacao']                   ?? '-',
                        'aspectos_motores'              => $h['aspectos_motores']              ?? '-',
                        'vida_autonoma'                 => $h['vida_autonoma']                 ?? '-',
                        'aspectos_sociais'              => $h['aspectos_sociais']              ?? '-',
                        'espaco_sala'                   => $ad['espaco_sala']                  ?? '-',
                        'recursos_gerais'               => $ad['recursos_gerais']              ?? '-',
                        'flexibilizacao_tempo'          => $ad['flexibilizacao_tempo']         ?? '-',
                        'avaliacao_diagnostica'         => $ad['avaliacao_diagnostica']        ?? '-',
                        'adequacao_temporalidade_legal' => $ad['adequacao_temporalidade_legal'] ?? '-',
                    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
                    ?>
                    <tr style="display:none;" class="detalhe-row" data-pai="tr-<?php echo $aluno['id']; ?>"
                        data-aee='<?php echo $aeeJson; ?>'></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="index.php?action=dashboard" class="btn btn-secondary mt-2">Voltar ao Dashboard</a>
    <iframe id="frameImpressao" style="display:none;"></iframe>

    <!-- Modal Ficha Completa -->
    <div class="modal fade" id="modalFicha" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="fichaModalTitulo">Ficha de Adequação Curricular</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="fichaModalBody">
                    <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="imprimirFicha()">🖨️ Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAEE" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white d-flex align-items-center gap-3">
                    <div id="modalAEEFotoWrap">
                        <img id="modalAEEFoto" src="" alt="Foto" style="width:52px;height:52px;object-fit:cover;border-radius:50%;border:2px solid #fff;display:none;">
                        <span id="modalAEESemFoto" style="display:none;font-size:2rem;">👤</span>
                    </div>
                    <h5 class="modal-title mb-0" id="modalAEETitulo"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- Escolarização e Saúde -->
                    <div class="fw-bold text-primary border-bottom mb-2">Escolarização e Saúde</div>
                    <table class="table table-sm table-bordered mb-4">
                        <tbody>
                            <tr><th class="table-light" style="width:30%">Percurso</th>          <td id="m-percurso"></td></tr>
                            <tr><th class="table-light">Atend. Passados</th>                     <td id="m-passados"></td></tr>
                            <tr><th class="table-light">Atend. Atuais</th>                       <td id="m-atuais"></td></tr>
                            <tr><th class="table-light">Medicação</th>                          <td id="m-medicacao"></td></tr>
                        </tbody>
                    </table>

                    <!-- Habilidades Biopsicossociais -->
                    <div class="fw-bold text-success border-bottom mb-2">Habilidades Biopsicossociais</div>
                    <table class="table table-sm table-bordered mb-4">
                        <tbody>
                            <tr><th class="table-light" style="width:30%">Comunicação</th>      <td id="m-comunicacao"></td></tr>
                            <tr><th class="table-light">Asp. Motores</th>                        <td id="m-motores"></td></tr>
                            <tr><th class="table-light">Vida Autônoma</th>                      <td id="m-autonoma"></td></tr>
                            <tr><th class="table-light">Asp. Sociais</th>                        <td id="m-sociais"></td></tr>
                        </tbody>
                    </table>

                    <!-- Adequação Organizativa -->
                    <div class="fw-bold text-warning border-bottom mb-2">Adequação Organizativa</div>
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                            <tr><th class="table-light" style="width:30%">Espaço em Sala</th>   <td id="m-espaco"></td></tr>
                            <tr><th class="table-light">Recursos Gerais</th>                     <td id="m-recursos"></td></tr>
                            <tr><th class="table-light">Flex. Tempo</th>                         <td id="m-tempo"></td></tr>
                            <tr><th class="table-light">Aval. Diagnóstica</th>                  <td id="m-avaliacao"></td></tr>
                            <tr><th class="table-light">Temporalidade Legal</th>                 <td id="m-temporalidade"></td></tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var busca     = document.getElementById('campoBusca');
            var combo     = document.getElementById('comboAluno');
            var btnLimpar = document.getElementById('btnLimpar');
            var modal     = new bootstrap.Modal(document.getElementById('modalAEE'));

            var modalFicha = new bootstrap.Modal(document.getElementById('modalFicha'));

            document.querySelectorAll('.btn-ficha').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.dataset.id;
                    document.getElementById('fichaModalBody').innerHTML =
                        '<div class="text-center py-4"><div class="spinner-border text-success"></div></div>';
                    modalFicha.show();

                    fetch('index.php?action=ajax_ficha_completa&aluno_id=' + id)
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            document.getElementById('fichaModalBody').innerHTML = html;
                        });
                });
            });

            document.getElementById('btnImprimir').addEventListener('click', function() {
                var rows = '';
                document.querySelectorAll('#tabelaAdequacoes tbody tr[id^="tr-"]').forEach(function(tr) {
                    if (tr.style.display === 'none') return;
                    var cells = tr.querySelectorAll('td');
                    rows += '<tr>';
                    for (var i = 0; i < cells.length - 1; i++) {
                        rows += '<td>' + cells[i].innerHTML + '</td>';
                    }
                    rows += '</tr>';
                });
                var html = '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8">'
                    + '<title>Lista de Adequações</title>'
                    + '<style>'
                    + '@page{size:A4 landscape;margin:10mm}'
                    + 'body{font-family:Arial,sans-serif;font-size:10px;margin:0}'
                    + 'h3{font-size:13px;margin:0 0 6px}'
                    + 'table{width:100%;border-collapse:collapse}'
                    + 'thead{display:table-header-group}'
                    + 'tr{page-break-inside:avoid}'
                    + 'th,td{border:1px solid #333;padding:3px 5px;vertical-align:top;text-align:left}'
                    + 'th{background:#222;color:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}'
                    + '</style></head><body>'
                    + '<h3>Lista de Adequações Curriculares</h3>'
                    + '<table><thead><tr>'
                    + '<th>Nome</th><th>Matrícula</th><th>Modalidade / Turma</th><th>Diagnóstico</th><th>CID</th><th>Vigência</th>'
                    + '</tr></thead><tbody>' + rows + '</tbody></table>'
                    + '</body></html>';
                var frame = document.getElementById('frameImpressao');
                frame.onload = function() { frame.contentWindow.print(); };
                frame.srcdoc = html;
            });

            function imprimirFicha() {
                var conteudo = document.getElementById('fichaModalBody').innerHTML;
                var janela = window.open('', '_blank');
                janela.document.write('<html><head><title>Ficha de Adequação</title>');
                janela.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
                janela.document.write('</head><body class="p-4">' + conteudo + '</body></html>');
                janela.document.close();
                janela.onload = function() { janela.print(); };
            }

            document.querySelectorAll('.btn-aee').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id  = this.dataset.id;
                    var row = document.querySelector('.detalhe-row[data-pai="tr-' + id + '"]');
                    var d   = JSON.parse(row.dataset.aee);

                    document.getElementById('modalAEETitulo').textContent  = 'Informações AEE — ' + d.nome;

                    var foto    = document.getElementById('modalAEEFoto');
                    var semFoto = document.getElementById('modalAEESemFoto');
                    if (d.foto) {
                        foto.src = d.foto;
                        foto.style.display = 'inline-block';
                        semFoto.style.display = 'none';
                    } else {
                        foto.style.display = 'none';
                        semFoto.style.display = 'inline';
                    }
                    document.getElementById('m-percurso').textContent      = d.percurso_escolarizacao;
                    document.getElementById('m-passados').textContent      = d.atendimentos_passados;
                    document.getElementById('m-atuais').textContent        = d.atendimentos_atuais;
                    document.getElementById('m-medicacao').textContent     = d.medicacao;
                    document.getElementById('m-comunicacao').textContent   = d.comunicacao;
                    document.getElementById('m-motores').textContent       = d.aspectos_motores;
                    document.getElementById('m-autonoma').textContent      = d.vida_autonoma;
                    document.getElementById('m-sociais').textContent       = d.aspectos_sociais;
                    document.getElementById('m-espaco').textContent        = d.espaco_sala;
                    document.getElementById('m-recursos').textContent      = d.recursos_gerais;
                    document.getElementById('m-tempo').textContent         = d.flexibilizacao_tempo;
                    document.getElementById('m-avaliacao').textContent     = d.avaliacao_diagnostica;
                    document.getElementById('m-temporalidade').textContent = d.adequacao_temporalidade_legal;

                    modal.show();
                });
            });

            function filtrarTabela(termo) {
                document.querySelectorAll('#tabelaAdequacoes tbody tr[id^="tr-"]').forEach(function(tr) {
                    var nome      = tr.dataset.nome      || '';
                    var matricula = tr.dataset.matricula || '';
                    tr.style.display = (!termo || nome.includes(termo) || matricula.includes(termo)) ? '' : 'none';
                });
            }

            busca.addEventListener('input', function() {
                combo.value = '';
                filtrarTabela(this.value.toLowerCase().trim());
            });

            combo.addEventListener('change', function() {
                busca.value = '';
                filtrarTabela('');
                if (!this.value) return;
                document.querySelectorAll('#tabelaAdequacoes tbody tr[id^="tr-"]').forEach(function(tr) {
                    tr.style.display = 'none';
                });
                var alvo = document.getElementById(combo.value);
                if (alvo) {
                    alvo.style.display = '';
                    alvo.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            btnLimpar.addEventListener('click', function() {
                busca.value = '';
                combo.value = '';
                filtrarTabela('');
            });
        });
    </script>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
