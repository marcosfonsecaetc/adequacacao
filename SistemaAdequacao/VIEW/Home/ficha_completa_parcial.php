<?php
function fc($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
function fcMed($s) {
    if (($s['faz_uso_medicacao'] ?? 0) == 1) {
        return 'Sim — ' . htmlspecialchars($s['medicacao_especificacao'] ?? '', ENT_QUOTES, 'UTF-8');
    }
    return 'NÃO FAZ USO DE MEDICAÇÃO';
}
$professores = array_unique(array_filter(array_column($planos ?? [], 'professor_email')));
?>
<style>
    .f { font-family: Arial, sans-serif; font-size: 11px; color: #000; max-width: 900px; margin: 0 auto; }

    /* Cabeçalho */
    .f-head { display:flex; align-items:center; border:1px solid #000; padding:5px 8px; margin-bottom:3px; }
    .f-head-logo { width:65px; text-align:center; }
    .f-head-logo img { max-width:60px; max-height:60px; }
    .f-head-text { flex:1; text-align:center; font-size:10.5px; line-height:1.55; }
    .f-head-text strong { font-size:11px; }

    /* Título */
    .f-titulo { border:1px solid #000; border-top:none; padding:5px 8px; margin-bottom:3px; }
    .f-titulo b { font-size:11px; }
    .f-titulo p { margin:2px 0 0; font-size:10px; text-align:justify; }

    /* Seção */
    .f-sec { border:1px solid #000; border-top:none; }
    .f-sec-h { background:#d9d9d9; font-weight:bold; padding:3px 8px; border-bottom:1px solid #000; font-size:11px; }
    .f-sec-desc { padding:2px 8px 3px; font-size:9.5px; color:#333; font-style:italic; border-bottom:1px solid #ccc; text-align:justify; }

    /* Linhas */
    .f-row { display:flex; border-bottom:1px solid #ccc; min-height:18px; }
    .f-row:last-child { border-bottom:none; }
    .f-cell { padding:2px 6px; flex:1; text-align:justify; word-break:break-word; }
    .f-cell+.f-cell { border-left:1px solid #ccc; }
    .f-cell-fixed { padding:2px 6px; text-align:justify; word-break:break-word; }

    /* Bloco de texto longo */
    .f-full { padding:3px 8px; text-align:justify; border-bottom:1px solid #ccc; word-break:break-word; white-space:pre-wrap; }
    .f-full:last-child { border-bottom:none; }

    /* Tabela do plano pedagógico */
    .f-pp-table { width:100%; border-collapse:collapse; }
    .f-pp-table th, .f-pp-table td { border:1px solid #000; padding:3px 6px; font-size:10.5px; vertical-align:top; text-align:justify; word-break:break-word; }
    .f-pp-table th { background:#d9d9d9; font-weight:bold; text-align:center; width:25%; }

    /* Assinaturas */
    .f-assinaturas { display:flex; gap:20px; margin-top:30px; }
    .f-assinatura { flex:1; text-align:center; border-top:1px solid #000; padding-top:4px; font-size:10px; }

    /* Rodapé */
    .f-rodape { border:1px solid #000; border-top:none; padding:3px 8px; font-size:8px; color:#444; text-align:center; }

    .f-label { font-weight:bold; }
    .f-obs { border:1px solid #000; border-top:none; padding:4px 8px; font-size:10.5px; }

    @media print {
        .f { max-width:100%; }
        .modal-footer, .modal-header { display:none !important; }
    }
</style>

<div class="f">

    <!-- CABEÇALHO -->
    <div class="f-head">
        <div class="f-head-logo"><img src="ASSETS/imagens/gdf.png" alt="GDF"></div>
        <div class="f-head-text">
            GOVERNO DO DISTRITO FEDERAL<br>
            SECRETARIA DE ESTADO DE EDUCAÇÃO<br>
            Subsecretaria de Educação Inclusiva e Integral<br>
            Diretoria de Educação Inclusiva e Atendimentos Educacionais Especializados<br>
            Coordenação Regional de Ensino de Ceilândia<br>
            <strong>Escola Técnica de Ceilândia</strong>
        </div>
        <div class="f-head-logo"><img src="ASSETS/imagens/sala_recursos.png" alt="Sala de Recursos ETC"></div>
    </div>

    <!-- TÍTULO -->
    <div class="f-titulo">
        <b>FORMULÁRIO DE REGISTRO DAS ADEQUAÇÕES CURRICULARES - ETAPAS E MODALIDADES DA EDUCAÇÃO BÁSICA</b>
        <p>Este formulário deverá ser preenchido pelo professor regente em articulação com o professor do Atendimento Educacional Especializado (SERVIÇOS, RECURSOS) que atuam junto ao estudante público da Educação Especial de acordo com a Etapa ou modalidade/Ciclo/Bloco/Ano de matrícula.</p>
    </div>

    <!-- 1. IDENTIFICAÇÃO -->
    <div class="f-sec">
        <div class="f-sec-h">1. IDENTIFICAÇÃO DO ESTUDANTE:</div>
        <div class="f-row">
            <div class="f-cell"><span class="f-label">Nome completo do (da) estudante:</span> <?php echo fc($aluno['nome_completo']); ?></div>
            <div class="f-cell" style="max-width:230px"><span class="f-label">Data de nascimento:</span> <?php echo fc($aluno['data_nascimento']); ?></div>
        </div>
        <div class="f-row">
            <div class="f-cell"><span class="f-label">Modalidade/Ano/Turma/Turno:</span> <?php echo fc($aluno['modalidade_ano_turma_turno']); ?></div>
            <div class="f-cell" style="max-width:230px"><span class="f-label">Idade:</span> <?php echo fc($aluno['idade']); ?> anos</div>
        </div>
        <div class="f-row">
            <div class="f-cell"><span class="f-label">Endereço:</span> <?php echo fc($aluno['endereco']); ?></div>
            <div class="f-cell" style="max-width:230px"><span class="f-label">Telefones dos responsáveis:</span> <?php echo fc($aluno['telefones_responsaveis']); ?></div>
        </div>
        <div class="f-row">
            <div class="f-cell"><span class="f-label">Filiação:</span> <?php echo fc($aluno['filiacao_mae']); ?> / <?php echo fc($aluno['filiacao_pai']); ?></div>
            <div class="f-cell" style="max-width:230px"><span class="f-label">Período de vigência das adequações:</span> <?php echo fc($aluno['periodo_vigencia_adequacao']); ?></div>
        </div>
        <div class="f-full">
            <span class="f-label">Diagnóstico do (da) estudante:</span> <?php echo fc($aluno['diagnostico_detalhado']); ?>
            <?php if (!empty($aluno['cid_codigos'])): ?> &nbsp;<span class="f-label">CID:</span> <?php echo fc($aluno['cid_codigos']); ?><?php endif; ?>
        </div>
        <div class="f-full">
            <span class="f-label">Professor(es) regentes:</span> <?php echo !empty($professores) ? fc(implode(', ', $professores)) : '—'; ?>
        </div>
    </div>

    <!-- 2. ESCOLARIZAÇÃO -->
    <div class="f-sec">
        <div class="f-sec-h">2. DESCRIÇÃO SUCINTA SOBRE A ESCOLARIZAÇÃO DO (DA) ESTUDANTE:</div>
        <div class="f-sec-desc">(Descrever o percurso de escolarização, considerando os atendimentos por etapas e modalidades do AEE. Ex: Educação Precoce, Classe Especial, Integração Inversa, Turmas Inclusivas, Classe Bilíngue, etc. É importante mencionar, quando possível, o nome das unidades escolares que o/a estudante frequentou, bem como a cidade de origem de cada UE, considerando aquelas que se situam, inclusive, fora do Distrito Federal.)</div>
        <div class="f-full" style="min-height:50px"><?php echo fc($s['percurso_escolarizacao'] ?? ''); ?></div>
    </div>

    <!-- 3. ATENDIMENTOS -->
    <div class="f-sec">
        <div class="f-sec-h">3. ATENDIMENTOS E/OU TRATAMENTOS TERAPÊUTICOS E CLÍNICOS (Ex.: fonoaudiologia, psicoterapia, terapia ocupacional, neurologia, psiquiatria, equoterapia, natação e demais atividades esportivas, atendimento psicopedagógico, modalidades do AEE).</div>
        <div class="f-row">
            <div class="f-cell">
                <span class="f-label">3.1. Quais atendimentos terapêuticos e/ou clínicos o estudante recebeu?</span><br>
                <?php echo fc($s['atendimentos_passados'] ?? ''); ?>
            </div>
            <div class="f-cell">
                <span class="f-label">3.2. Quais os atendimentos terapêuticos e ou clínicos O aluno recebe atualmente?</span><br>
                <?php echo fc($s['atendimentos_atuais'] ?? ''); ?>
            </div>
        </div>
        <div class="f-full">
            <span class="f-label">3.3. Faz uso de medicação? ( )S (X)N – Qual(is)?</span> <?php echo fcMed($s); ?>
        </div>
    </div>

    <!-- 4. HABILIDADES BIOPSICOSSOCIAIS -->
    <div class="f-sec">
        <div class="f-sec-h">4. HABILIDADES BIOPSICOSSOCIAIS:</div>
        <div class="f-full">
            <span class="f-label">4.1. Comunicação</span>(Descrever de que forma o/a estudante estabelece a comunicação -expressão e interpretação- entre estudante-estudante, estudante-professor e estudante-demais servidores, a depender da particularidade de cada um, podendo ser por meio da LIBRAS, linguagem corporal, oral, gestual, digital, etc. assim como, por meio da linguagem escrita, seja por meio do Sistema Braille, Português escrito, desenhos, entre outros.):<br>
            <?php echo fc($h['comunicacao'] ?? ''); ?>
        </div>
        <div class="f-full">
            <span class="f-label">4.2. Aspectos Motores e de mobilidade</span> (Descrever as principais características da mobilidade do estudante - realiza a marcha, faz uso da cadeira de rodas, etc. -, assim como os aspectos que envolvem os pequenos músculos como realiza o movimento da pinça, agarra objetos, etc. e grandes músculos como tensionamento em demasia, corre, sobe/desce degraus, anda sobre uma linha, etc.; sobretudo relacionados ao esquema corporal, à lateralidade, à noção espaço-temporal, equilíbrio, dentre outros.):<br>
            <?php echo fc($h['aspectos_motores'] ?? ''); ?>
        </div>
        <div class="f-full">
            <span class="f-label">4.3. Atividades de vida autônoma</span>(Descrever as principais evidências e autonomia do/da estudante em seu cotidiano, tais como: se vestir e se locomover de forma independente, calçar o próprio sapato, uso do banheiro, iniciativa para solicitar algo, segurar o talher para se alimentar sozinho/sozinha, etc.):<br>
            <?php echo fc($h['vida_autonoma'] ?? ''); ?>
        </div>
        <div class="f-full">
            <span class="f-label">4.4. Aspectos sociais</span>(Descrever, em linhas gerais, como ocorre o processo de socialização do/da estudante: prefere ficar sozinho/sozinha, faz amigos com facilidade, de que forma lida com a frustração, demonstra insegurança, etc.):<br>
            <?php echo fc($h['aspectos_sociais'] ?? ''); ?>
        </div>
    </div>

    <!-- 5. ADEQUAÇÕES ORGANIZATIVAS -->
    <div class="f-sec">
        <div class="f-sec-h">5. ADEQUAÇÕES ORGANIZATIVAS:</div>
        <div class="f-full">
            <span class="f-label">5.1. Espaço</span> (Descrever a forma que o espaço da sala de aula/contexto escolar precisa estar adequado de modo a potencializar o desenvolvimento do/da estudante: disposição da sala, onde anexar algum trabalho de forma acessível, favorecimento da mobilidade, etc.):<br>
            <?php echo fc($ad['espaco_sala'] ?? ''); ?>
        </div>
        <div class="f-full">
            <span class="f-label">5.2. Recursos</span> (Descrever os recursos gerais utilizados para viabilizar o processo de ensino aprendizagem do/da estudante):<br>
            <?php echo fc($ad['recursos_gerais'] ?? ''); ?>
        </div>
        <div class="f-full">
            <span class="f-label">5.3. Tempo</span> (Descrever a intensidade, duração, transitoriedade, constância de uma atividade. Ressalta-se a flexibilização temporal organizacional):<br>
            <?php echo fc($ad['flexibilizacao_tempo'] ?? ''); ?>
        </div>
    </div>

    <!-- 6. AVALIAÇÃO DIAGNÓSTICA -->
    <div class="f-sec">
        <div class="f-sec-h">6. AVALIAÇÃO DIAGNÓSTICA (de acordo com a área do conhecimento):</div>
        <div class="f-full" style="min-height:40px"><?php echo fc($ad['avaliacao_diagnostica'] ?? ''); ?></div>
    </div>

    <!-- 7. TEMPORALIDADE -->
    <div class="f-sec">
        <div class="f-sec-h">7. ADEQUAÇÃO DE TEMPORALIDADE (Regimento Escolar da Rede Pública de Ensino do Distrito Federal, art. 201, §1o e 2o)</div>
        <div class="f-full" style="min-height:30px"><?php echo fc($ad['adequacao_temporalidade_legal'] ?? ''); ?></div>
    </div>

    <!-- 8. ADEQUAÇÕES CURRICULARES POR DISCIPLINA -->
    <?php if (!empty($planos)): ?>
        <?php foreach ($planos as $pp): ?>
        <div class="f-sec">
            <div class="f-sec-h">8. ADEQUAÇÕES CURRICULARES (Este campo deverá ser preenchido a cada semestre)</div>
            <div class="f-full">
                <span class="f-label">ETAPA:</span>
                ( ) Educação Infantil &nbsp;( ) Ensino Fundamental - Anos Iniciais &nbsp;( ) Ensino Fundamental - Anos Finais &nbsp;( ) Ensino Médio &nbsp;(X) <?php echo fc($pp['etapa_ensino']); ?>
            </div>
            <div class="f-full">
                <span class="f-label">Período de vigência da Adequação Curricular (semestral):</span> <?php echo fc($pp['periodo_vigencia_semestral']); ?>
            </div>
            <div class="f-full">
                <span class="f-label">9. Áreas do conhecimento/Componentes Curriculares (Linguagens, Matemática, Ciências Naturais, Ciências Humanas ou outras):</span><br>
                <span class="f-label">Professor regente:</span> <?php echo fc($pp['professor_email']); ?>
                &nbsp;&nbsp;<span class="f-label">Disciplina:</span> <?php echo fc($pp['disciplina_nome']); ?>
            </div>
            <table class="f-pp-table">
                <thead>
                    <tr>
                        <th>Objetivos para as aprendizagens<br><small style="font-weight:normal">(Descrever o foco principal do processo de ensino-aprendizagem)</small></th>
                        <th>Conteúdos/Unidades Didáticas<br><small style="font-weight:normal">(Mencionar os conteúdos a serem trabalhados)</small></th>
                        <th>Estratégias Pedagógicas/<br>Recursos Didáticos</th>
                        <th>Estratégias de Avaliação para a aprendizagem<br><small style="font-weight:normal">(portfólios, observações e anotações das potencialidades)</small></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo fc($pp['objetivos_aprendizagem']); ?></td>
                        <td><?php echo fc($pp['conteudos_didaticos']); ?></td>
                        <td><?php echo fc($pp['estrategias_pedagogicas']); ?></td>
                        <td><?php echo fc($pp['estrategias_avaliacao']); ?></td>
                    </tr>
                </tbody>
            </table>
            <?php if (!empty($pp['adequacao_espaco']) || !empty($pp['adequacao_recursos']) || !empty($pp['adequacao_tempo']) || !empty($pp['adequacao_avaliativa'])): ?>
            <div class="f-full" style="background:#f5f5f5"><span class="f-label">Adequações Específicas desta Disciplina:</span></div>
            <?php if (!empty($pp['adequacao_espaco'])): ?>
            <div class="f-full"><span class="f-label">Espaço/Organização:</span> <?php echo fc($pp['adequacao_espaco']); ?></div>
            <?php endif; ?>
            <?php if (!empty($pp['adequacao_recursos'])): ?>
            <div class="f-full"><span class="f-label">Recursos Específicos:</span> <?php echo fc($pp['adequacao_recursos']); ?></div>
            <?php endif; ?>
            <?php if (!empty($pp['adequacao_tempo'])): ?>
            <div class="f-full"><span class="f-label">Flexibilização de Tempo:</span> <?php echo fc($pp['adequacao_tempo']); ?></div>
            <?php endif; ?>
            <?php if (!empty($pp['adequacao_avaliativa'])): ?>
            <div class="f-full"><span class="f-label">Avaliação Adaptada:</span> <?php echo fc($pp['adequacao_avaliativa']); ?></div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="f-sec">
        <div class="f-sec-h">8. ADEQUAÇÕES CURRICULARES</div>
        <div class="f-full" style="color:#888;font-style:italic">Nenhum plano pedagógico registrado.</div>
    </div>
    <?php endif; ?>

    <!-- 10. ENCAMINHAMENTOS -->
    <div class="f-sec">
        <div class="f-sec-h">10. DESCRIÇÃO DOS ENCAMINHAMENTOS (Considerando os diferentes contextos)</div>
        <div class="f-full"><span class="f-label">Escolar:</span> <?php echo fc($enc['contexto_escolar'] ?? ''); ?></div>
        <div class="f-full"><span class="f-label">Familiar:</span> <?php echo fc($enc['contexto_familiar'] ?? ''); ?></div>
        <div class="f-full"><span class="f-label">Outros:</span> <?php echo fc($enc['outros_encaminhamentos'] ?? ''); ?></div>
        <div class="f-full"><span class="f-label">Observações:</span><br>A Adequação Curricular deverá estar em consonância com a Resolução no 02/2001, do Conselho Nacional de Educação, no item III do art. 8o.</div>
    </div>

    <!-- DATA -->
    <div style="text-align:right; padding:8px 8px 0; font-size:11px; border:1px solid #000; border-top:none;">
        Data <?php echo date('d/m/Y'); ?>.
    </div>

    <!-- ASSINATURAS -->
    <div style="border:1px solid #000; border-top:none; padding:30px 8px 10px;">
        <?php
        // Professores regentes vinculados ao aluno
        $stmtVinc = $pdo->prepare("SELECT u.nome_completo, u.matricula_servidor, ap.disciplina FROM aluno_professor ap INNER JOIN usuario u ON u.id = ap.professor_id WHERE ap.aluno_id = :id ORDER BY u.nome_completo ASC");
        $stmtVinc->execute([':id' => $aluno['id']]);
        $profsVinculados = $stmtVinc->fetchAll(PDO::FETCH_ASSOC);
        $nomeRegente = !empty($profsVinculados)
            ? implode(' / ', array_map(fn($p) => ($p['nome_completo'] ?: '(sem nome)') . (!empty($p['matricula_servidor']) ? ' — Mat. '.$p['matricula_servidor'] : ''), $profsVinculados))
            : '(Professor Regente não identificado)';

        // AEE (papel 3)
        $stmtAEEa = $pdo->query("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 3 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
        $aeeList  = $stmtAEEa->fetchAll(PDO::FETCH_ASSOC);
        $aee1 = !empty($aeeList[0]) ? ($aeeList[0]['nome_completo'] ?: $usuariosAEE[0] ?? '(A preencher)') . (!empty($aeeList[0]['matricula_servidor']) ? ' — Mat. '.$aeeList[0]['matricula_servidor'] : '') : ($usuariosAEE[0] ?? '(A preencher)');
        $aee2 = !empty($aeeList[1]) ? ($aeeList[1]['nome_completo'] ?: $usuariosAEE[1] ?? '(A preencher)') . (!empty($aeeList[1]['matricula_servidor']) ? ' — Mat. '.$aeeList[1]['matricula_servidor'] : '') : ($usuariosAEE[1] ?? '(A preencher)');

        // Gestor (papel 2)
        $stmtGa  = $pdo->query("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 2 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
        $gestList = $stmtGa->fetchAll(PDO::FETCH_ASSOC);
        $gestor  = !empty($gestList[0]) ? ($gestList[0]['nome_completo'] ?: $usuariosGestor[0] ?? '(A preencher)') . (!empty($gestList[0]['matricula_servidor']) ? ' — Mat. '.$gestList[0]['matricula_servidor'] : '') : ($usuariosGestor[0] ?? '(A preencher)');

        // Apoio (papel 8=secretário, 9=coordenador)
        $stmtAp  = $pdo->query("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 8 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
        $apoList  = $stmtAp->fetchAll(PDO::FETCH_ASSOC);
        $secretario   = !empty($apoList[0]) ? ($apoList[0]['nome_completo'] ?: '(A preencher)') . (!empty($apoList[0]['matricula_servidor']) ? ' — Mat. '.$apoList[0]['matricula_servidor'] : '') : '(A preencher)';
        $stmtCo  = $pdo->query("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 9 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
        $coList   = $stmtCo->fetchAll(PDO::FETCH_ASSOC);
        $coordenador  = !empty($coList[0]) ? ($coList[0]['nome_completo'] ?: '(A preencher)') . (!empty($coList[0]['matricula_servidor']) ? ' — Mat. '.$coList[0]['matricula_servidor'] : '') : '(A preencher)';
        ?>
        <div class="f-assinaturas">
            <div class="f-assinatura"><?php echo fc($nomeRegente); ?><br>Professor(es) Regente(s)</div>
            <div class="f-assinatura"><?php echo fc($aee1); ?><br>Professor(a) do AEE<br><span style="color:#0070c0">Sala de Recursos</span></div>
            <div class="f-assinatura"><?php echo fc($aee2); ?><br>Professor(a) do AEE<br><span style="color:#0070c0">Sala de Recursos</span></div>
        </div>
        <div class="f-assinaturas" style="margin-top:30px">
            <div class="f-assinatura"><?php echo fc($gestor); ?><br>Membro da Equipe Gestora</div>
            <div class="f-assinatura"><?php echo fc($secretario); ?><br>Secretário(a) Escolar</div>
            <div class="f-assinatura"><?php echo fc($coordenador); ?><br>Coordenador(a) Pedagógico(a)</div>
        </div>
    </div>

    <!-- RODAPÉ -->
    <div class="f-rodape">
        Comunicamos a proibição de uso deste formulário para divulgação de qualquer informação constante no presente documento, em observância ao disposto na Lei Federal nº 13.709/2018, Lei Geral de Proteção de Dados - LGPD.<br>
        ESCOLA TÉCNICA DE CEILÂNDIA &nbsp;|&nbsp; EQNN 14 Área Especial S/Nº - Ceilândia Sul - DF - CEP: 72220-140 &nbsp;|&nbsp; Telefone: (61) 3901 7545 / (61) 3901 6927 &nbsp;|&nbsp; E-mail: etc.df.gov.br
    </div>

</div>
