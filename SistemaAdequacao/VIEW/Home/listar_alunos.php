<?php
$title = 'Listar Alunos - ETC';

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

// Garante que a conexão PDO seja acessível mesmo quando o include usa safe_include()
global $pdo;

// Carrega todos os alunos usando AlunoDAO
$alunoDAO = new AlunoDAO($pdo);
$alunos = $alunoDAO->listarTodos();
?>

    <h3>Lista de Alunos</h3>

    <?php if (empty($alunos)): ?>
        <div class="alert alert-info">Nenhum aluno cadastrado ainda.</div>
    <?php else: ?>

    <!-- Dados dos alunos em JSON para uso no JS -->
    <script>
        const alunos = <?php echo json_encode(array_values($alunos), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>

    <!-- Formulário de busca e combo -->
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
                            <option value="<?php echo $aluno['id']; ?>"
                                data-nome="<?php echo htmlspecialchars($aluno['nome_completo']); ?>">
                                <?php echo htmlspecialchars($aluno['nome_completo']); ?>
                                <?php if (!empty($aluno['matricula'])): ?>
                                    — <?php echo htmlspecialchars($aluno['matricula']); ?>
                                <?php endif; ?>
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

    <!-- Card do aluno selecionado -->
    <div id="fichaAluno" class="card mb-4" style="display:none;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
            <strong id="ficha-titulo"></strong>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" id="btnAEE" class="btn btn-sm btn-light" title="Informações AEE">+</button>
                <a id="btnEditar" href="#" class="btn btn-sm btn-warning" title="Editar Aluno">✏️ Editar</a>
                <button type="button" class="btn-close btn-close-white" id="btnFecharFicha"></button>
            </div>
        </div>

        <!-- Dados básicos do aluno -->
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-2 d-flex align-items-center justify-content-center p-3 border-end">
                    <img id="ficha-foto" src="" alt="Foto" class="img-thumbnail" style="max-width:90px;max-height:90px;display:none;">
                    <span id="ficha-sem-foto" class="text-muted small">Sem foto</span>
                </div>
                <div class="col-md-10">
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                            <tr><th class="table-light" style="width:30%">Nome</th>        <td id="ficha-nome_completo"></td></tr>
                            <tr><th class="table-light">Matrícula</th>                     <td id="ficha-matricula"></td></tr>
                            <tr><th class="table-light">Data de Nascimento</th>            <td id="ficha-data_nascimento"></td></tr>
                            <tr><th class="table-light">Idade</th>                         <td id="ficha-idade"></td></tr>
                            <tr><th class="table-light">Modalidade / Turma / Turno</th>   <td id="ficha-modalidade_ano_turma_turno"></td></tr>
                            <tr><th class="table-light">Endereço</th>                      <td id="ficha-endereco"></td></tr>
                            <tr><th class="table-light">Telefones dos Responsáveis</th>   <td id="ficha-telefones_responsaveis"></td></tr>
                            <tr><th class="table-light">Filiação — Mãe</th>               <td id="ficha-filiacao_mae"></td></tr>
                            <tr><th class="table-light">Filiação — Pai</th>               <td id="ficha-filiacao_pai"></td></tr>
                            <tr><th class="table-light">Período de Vigência</th>          <td id="ficha-periodo_vigencia_adequacao"></td></tr>
                            <tr><th class="table-light">Diagnóstico Detalhado</th>        <td id="ficha-diagnostico_detalhado"></td></tr>
                            <tr><th class="table-light">Códigos CID</th>                  <td id="ficha-cid_codigos"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Painel AEE (oculto por padrão) -->
        <div id="painelAEE" style="display:none;">
            <div class="card-header bg-success text-white py-2"><strong>Informações registradas pelo AEE</strong></div>
            <div class="card-body p-0">

                <div class="px-3 pt-3 pb-1"><strong class="text-secondary">Escolarização e Saúde</strong></div>
                <table class="table table-sm table-bordered mb-0">
                    <tbody>
                        <tr><th class="table-light" style="width:30%">Percurso de Escolarização</th><td id="aee-percurso_escolarizacao"></td></tr>
                        <tr><th class="table-light">Atendimentos Passados</th>                      <td id="aee-atendimentos_passados"></td></tr>
                        <tr><th class="table-light">Atendimentos Atuais</th>                        <td id="aee-atendimentos_atuais"></td></tr>
                        <tr><th class="table-light">Faz uso de Medicação</th>                     <td id="aee-faz_uso_medicacao"></td></tr>
                        <tr><th class="table-light">Especificação da Medicação</th>              <td id="aee-medicacao_especificacao"></td></tr>
                    </tbody>
                </table>

                <div class="px-3 pt-3 pb-1"><strong class="text-secondary">Habilidades Biopsicossociais</strong></div>
                <table class="table table-sm table-bordered mb-0">
                    <tbody>
                        <tr><th class="table-light" style="width:30%">Comunicação</th>    <td id="aee-comunicacao"></td></tr>
                        <tr><th class="table-light">Aspectos Motores</th>                <td id="aee-aspectos_motores"></td></tr>
                        <tr><th class="table-light">Vida Autônoma</th>                  <td id="aee-vida_autonoma"></td></tr>
                        <tr><th class="table-light">Aspectos Sociais</th>               <td id="aee-aspectos_sociais"></td></tr>
                    </tbody>
                </table>

                <div class="px-3 pt-3 pb-1"><strong class="text-secondary">Adequação Organizativa</strong></div>
                <table class="table table-sm table-bordered mb-0">
                    <tbody>
                        <tr><th class="table-light" style="width:30%">Espaço em Sala</th>              <td id="aee-espaco_sala"></td></tr>
                        <tr><th class="table-light">Recursos Gerais</th>                               <td id="aee-recursos_gerais"></td></tr>
                        <tr><th class="table-light">Flexibilização de Tempo</th>                     <td id="aee-flexibilizacao_tempo"></td></tr>
                        <tr><th class="table-light">Avaliação Diagnóstica</th>                      <td id="aee-avaliacao_diagnostica"></td></tr>
                        <tr><th class="table-light">Adequação de Temporalidade Legal</th>            <td id="aee-adequacao_temporalidade_legal"></td></tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <?php endif; ?>

    <a href="index.php?action=dashboard" class="btn btn-secondary">Voltar ao Dashboard</a>

    <script>
        const campos = ['nome_completo','matricula','data_nascimento','idade',
                        'modalidade_ano_turma_turno','endereco','telefones_responsaveis',
                        'filiacao_mae','filiacao_pai','periodo_vigencia_adequacao',
                        'diagnostico_detalhado','cid_codigos'];

        const combo     = document.getElementById('comboAluno');
        const busca     = document.getElementById('campoBusca');
        const ficha     = document.getElementById('fichaAluno');
        const painelAEE = document.getElementById('painelAEE');
        const btnAEE    = document.getElementById('btnAEE');
        const btnEditar = document.getElementById('btnEditar');
        const btnFechar = document.getElementById('btnFecharFicha');
        const btnLimpar = document.getElementById('btnLimpar');
        let alunoAtualId = null;

        function exibirAluno(aluno) {
            if (!aluno) { ficha.style.display = 'none'; return; }
            alunoAtualId = aluno.id;
            painelAEE.style.display = 'none';
            btnAEE.textContent = '+';

            document.getElementById('ficha-titulo').textContent = aluno.nome_completo;

            campos.forEach(function(c) {
                var el = document.getElementById('ficha-' + c);
                if (el) el.textContent = aluno[c] || '-';
            });

            var img = document.getElementById('ficha-foto');
            var semFoto = document.getElementById('ficha-sem-foto');
            if (aluno.foto) {
                var fotoPath = aluno.foto.replace(/^\/+/, '');
                var fotoUrl = fotoPath.match(/^https?:\/\//i)
                    ? aluno.foto
                    : window.location.origin + '/SistemaAdequacao/' + fotoPath;

                img.src = fotoUrl;
                img.style.display = 'block';
                semFoto.style.display = 'none';

                img.onerror = function() {
                    img.style.display = 'none';
                    semFoto.style.display = 'inline';
                    console.warn('Falha ao carregar imagem do aluno:', fotoUrl);
                };
            } else {
                img.style.display = 'none';
                semFoto.style.display = 'inline';
            }

            ficha.style.display = 'block';
            btnEditar.href = 'index.php?action=editar_aluno&aluno_id=' + aluno.id;
            ficha.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function limpar() {
            busca.value = '';
            combo.value = '';
            Array.from(combo.options).forEach(o => o.style.display = '');
            ficha.style.display = 'none';
            painelAEE.style.display = 'none';
            alunoAtualId = null;
        }

        // Botão "+" — carrega dados AEE via AJAX e expande/colapsa
        btnAEE.addEventListener('click', function() {
            if (painelAEE.style.display !== 'none') {
                painelAEE.style.display = 'none';
                btnAEE.textContent = '+';
                return;
            }
            fetch('index.php?action=ajax_dados_aee&aluno_id=' + alunoAtualId)
                .then(r => r.json())
                .then(function(dados) {
                    var s = dados.saude      || {};
                    var h = dados.habilidade || {};
                    var a = dados.adequacao  || {};

                    var mapa = {
                        'aee-percurso_escolarizacao':       s.percurso_escolarizacao,
                        'aee-atendimentos_passados':        s.atendimentos_passados,
                        'aee-atendimentos_atuais':          s.atendimentos_atuais,
                        'aee-faz_uso_medicacao':            s.faz_uso_medicacao == 1 ? 'Sim' : (s.faz_uso_medicacao == 0 ? 'Não' : '-'),
                        'aee-medicacao_especificacao':      s.medicacao_especificacao,
                        'aee-comunicacao':                  h.comunicacao,
                        'aee-aspectos_motores':             h.aspectos_motores,
                        'aee-vida_autonoma':                h.vida_autonoma,
                        'aee-aspectos_sociais':             h.aspectos_sociais,
                        'aee-espaco_sala':                  a.espaco_sala,
                        'aee-recursos_gerais':              a.recursos_gerais,
                        'aee-flexibilizacao_tempo':         a.flexibilizacao_tempo,
                        'aee-avaliacao_diagnostica':        a.avaliacao_diagnostica,
                        'aee-adequacao_temporalidade_legal':a.adequacao_temporalidade_legal,
                    };

                    Object.keys(mapa).forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el) el.textContent = mapa[id] || '-';
                    });

                    painelAEE.style.display = 'block';
                    btnAEE.textContent = '−';
                    painelAEE.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
        });

        busca.addEventListener('input', function() {
            var termo = this.value.toLowerCase().trim();
            combo.value = '';
            ficha.style.display = 'none';
            Array.from(combo.options).forEach(function(opt) {
                if (!opt.value) { opt.style.display = ''; return; }
                var aluno = alunos.find(a => a.id == opt.value);
                var texto = (aluno.nome_completo + ' ' + (aluno.matricula || '')).toLowerCase();
                opt.style.display = texto.includes(termo) ? '' : 'none';
            });
        });

        combo.addEventListener('change', function() {
            var aluno = alunos.find(a => a.id == this.value);
            exibirAluno(aluno || null);
        });

        btnFechar.addEventListener('click', limpar);
        btnLimpar.addEventListener('click', limpar);
    </script>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>