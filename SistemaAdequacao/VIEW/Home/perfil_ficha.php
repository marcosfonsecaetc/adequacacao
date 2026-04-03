<?php
function fp($val) { return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'); }
function fm($p, $k) { return !empty($p[$k]) ? '(X)' : '( )'; }
function fs($p, $k) { return ($p[$k] ?? 0) == 1 ? '(X) Sim ( ) Não' : '( ) Sim (X) Não'; }
function fi($p, $k) {
    $v = $p[$k] ?? 'nao_soube';
    return ['completo'=>'(X) Informou Completamente ( ) Informou Parcialmente ( ) Não Soube Informar',
            'parcial' =>'( ) Informou Completamente (X) Informou Parcialmente ( ) Não Soube Informar',
            'nao_soube'=>'( ) Informou Completamente ( ) Informou Parcialmente (X) Não Soube Informar'][$v] ?? '';
}
$niveis = ['nao_alfabetizado'=>'Não Alfabetizado','alfabetizado'=>'Alfabetizado','ef1_regular'=>'Ensino Fundamental I Regular','ef2_supletivo'=>'Ensino Fundamental II Supletivo','ef2_regular'=>'Ensino Fundamental II Regular','ef3_supletivo'=>'Ensino Fundamental III Supletivo','em_regular'=>'Ensino Médio Regular','em_supletivo'=>'Ensino Médio Supletivo','eja'=>'Educação de Jovens e Adultos','profissionalizante'=>'Ensino Profissionalizante'];
$nivelLabel = $niveis[$p['escolaridade_nivel'] ?? ''] ?? '—';
$dr = !empty($p['data_registro']) ? date('d/m/Y', strtotime($p['data_registro'])) : date('d/m/Y');
?>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:Arial,sans-serif;font-size:11px;color:#000;background:#fff;}
.wrap{max-width:720px;margin:0 auto;padding:10px;}
.cab{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
.cab img{height:55px;}
.cab-texto{flex:1;text-align:center;font-size:10.5px;line-height:1.7;padding:0 10px;}
.titulo-box{border:1px solid #000;padding:6px 14px;text-align:center;margin-bottom:14px;font-size:13px;font-weight:bold;}
.sec-h{background:#e0e0e0;font-weight:bold;padding:3px 6px;border:1px solid #000;font-size:11px;margin-top:6px;}
.tbl{width:100%;border-collapse:collapse;}
.tbl td,.tbl th{border:1px solid #000;padding:3px 6px;font-size:11px;vertical-align:top;}
.tbl th{background:#f0f0f0;font-weight:bold;width:35%;}
.tbl .full{width:100%;}
.row-q{border:1px solid #000;border-top:none;padding:3px 6px;}
.row-q table{width:100%;border-collapse:collapse;}
.row-q table td{border:1px solid #ccc;padding:2px 5px;font-size:10.5px;vertical-align:top;}
.row-q table th{border:1px solid #000;padding:2px 5px;font-size:10.5px;background:#e8e8e8;text-align:center;}
.label{font-weight:bold;}
.rodape{margin-top:10px;font-size:8px;color:#444;text-align:center;border-top:1px solid #ccc;padding-top:4px;}
@media print{.no-print{display:none!important;}.wrap{padding:0;}@page{size:A4 portrait;margin:10mm;}}
</style>

<div class="wrap">

    <!-- CABEÇALHO -->
    <div class="cab">
        <img src="ASSETS/imagens/gdf.png" alt="ETC">
        <div class="cab-texto">
            SECRETARIA DE ESTADO DE EDUCAÇÃO DO DISTRITO FEDERAL<br>
            COORDENAÇÃO REGIONAL DE ENSINO DE CEILÂNDIA<br>
            <strong>ESCOLA TÉCNICA DE CEILÂNDIA – CEP-ETC</strong><br>
            <u>SALA DE RECURSOS ETC</u>
        </div>
        <img src="ASSETS/imagens/sala_recursos.png" alt="Sala de Recursos">
    </div>

    <div class="titulo-box">FORMULÁRIO ANAMNESE — PERFIL DE ENTRADA</div>

    <!-- 1. IDENTIFICAÇÃO -->
    <div class="sec-h">1 — Identificação</div>
    <table class="tbl">
        <tr><th>Nome</th><td colspan="3"><?php echo fp($aluno['nome_completo']); ?></td></tr>
        <tr><th>Filiação</th><td colspan="3"><?php echo fp($aluno['filiacao_mae']); ?> / <?php echo fp($aluno['filiacao_pai']); ?></td></tr>
        <tr>
            <th>Data de Nascimento</th><td><?php echo !empty($aluno['data_nascimento']) ? date('d/m/Y', strtotime($aluno['data_nascimento'])) : '—'; ?></td>
            <th>Naturalidade</th><td><?php echo fp($p['naturalidade'] ?? ''); ?></td>
        </tr>
        <tr><th>Endereço</th><td colspan="3"><?php echo fp($aluno['endereco']); ?></td></tr>
        <tr>
            <th>Telefone</th><td><?php echo fp($p['telefone_aluno'] ?? ''); ?></td>
            <th>Telefone — Responsável</th><td><?php echo fp($aluno['telefones_responsaveis']); ?></td>
        </tr>
    </table>

    <!-- 2. AVALIAÇÃO -->
    <div class="sec-h" style="margin-top:8px;">2 — Avaliação</div>
    <table class="tbl">
        <tr><th>Tipo de Deficiência</th><td colspan="3"><?php echo fp($aluno['diagnostico_detalhado']); ?> <?php if (!empty($aluno['cid_codigos'])): ?>(CID: <?php echo fp($aluno['cid_codigos']); ?>)<?php endif; ?></td></tr>
        <tr>
            <th>Tem laudo atualizado?</th><td><?php echo fs($p,'laudo_atualizado'); ?></td>
            <th>De quando?</th><td><?php echo fp($p['laudo_data'] ?? ''); ?></td>
        </tr>
        <tr><th>Acompanhamento médico / terapia / tratamento</th><td colspan="3"><?php echo fp($p['acompanhamento_medico'] ?? ''); ?></td></tr>
        <tr>
            <th>Usa medicamento?</th><td><?php echo fs($p,'usa_medicamento'); ?></td>
            <th>Qual? Dosagem?</th><td><?php echo fp($p['medicamento_detalhes'] ?? ''); ?></td>
        </tr>
        <tr><th>Independência nas AVDs?</th><td><?php echo fs($p,'independencia_avd'); ?><th>Independência na locomoção?</th><td><?php echo fs($p,'independencia_locomocao'); ?></td></tr>
        <tr><th>Recebe auxílio do governo?</th><td colspan="3"><?php echo fs($p,'auxilio_governo'); ?></td></tr>
        <tr>
            <th>Documentação</th>
            <td colspan="3">
                <?php echo fm($p,'doc_rg'); ?> RG &nbsp;&nbsp;
                <?php echo fm($p,'doc_cpf'); ?> CPF &nbsp;&nbsp;
                <?php echo fm($p,'doc_ctps'); ?> CTPS &nbsp;&nbsp;
                <?php echo fm($p,'doc_titulo'); ?> Título Eleitor &nbsp;&nbsp;
                <?php echo fm($p,'doc_reservista'); ?> Reservista &nbsp;&nbsp;
                <?php echo fm($p,'doc_sus'); ?> Cartão SUS
            </td>
        </tr>
    </table>

    <!-- 3. ESCOLARIDADE -->
    <div class="sec-h" style="margin-top:8px;">3 — Escolaridade</div>
    <table class="tbl">
        <tr><th>Última escola</th><td colspan="3"><?php echo fp($p['ultima_escola'] ?? ''); ?></td></tr>
        <tr>
            <th>Estudou em escola especial?</th><td><?php echo fp($p['escola_especial'] ?? ''); ?></td>
            <th>Por quanto tempo?</th><td><?php echo fp($p['escola_especial_tempo'] ?? ''); ?></td>
        </tr>
        <tr><th>Nível de Escolaridade</th><td colspan="3"><?php echo $nivelLabel; ?></td></tr>
    </table>

    <!-- 4. QUESTÕES SOCIOBIOGRÁFICAS -->
    <div class="sec-h" style="margin-top:8px;">4 — Questões Sociobiográficas</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:45%">Pergunta / Resposta</th>
                <th style="width:18%;text-align:center;">Informou Completamente</th>
                <th style="width:18%;text-align:center;">Informou Parcialmente</th>
                <th style="width:19%;text-align:center;">Não Soube Informar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $qs = [
            ['rel_pai',            'Você se relaciona bem com seu pai? E com sua mãe?'],
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
        foreach ($qs as [$key, $pergunta]):
            $inf = $p[$key.'_inf'] ?? 'nao_soube';
        ?>
        <tr>
            <td><strong><?php echo $pergunta; ?></strong><br><small><?php echo fp($p[$key] ?? ''); ?></small></td>
            <td style="text-align:center;"><?php echo $inf === 'completo'  ? '✓' : ''; ?></td>
            <td style="text-align:center;"><?php echo $inf === 'parcial'   ? '✓' : ''; ?></td>
            <td style="text-align:center;"><?php echo $inf === 'nao_soube' ? '✓' : ''; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- DETECTAR -->
    <div class="sec-h" style="margin-top:8px;">Detectar</div>
    <table class="tbl">
        <tr>
            <td><?php echo fm($p,'detectar_fluidez'); ?> Fluidez verbal</td>
            <td><?php echo fm($p,'detectar_encadeamento'); ?> Encadeamento de ideias</td>
            <td><?php echo fm($p,'detectar_independencia'); ?> Independência de ideias</td>
        </tr>
        <tr>
            <td><?php echo fm($p,'detectar_introversao'); ?> Introversão</td>
            <td><?php echo fm($p,'detectar_extroversao'); ?> Extroversão</td>
            <td><?php echo fm($p,'detectar_projeto_vida'); ?> Se tem ou não um projeto de vida</td>
        </tr>
    </table>

    <div style="text-align:right;padding:6px 0;font-size:11px;">Data: <?php echo $dr; ?></div>

    <div class="rodape">
        Comunicamos a proibição de uso deste formulário para divulgação de qualquer informação constante no presente documento, em observância ao disposto na Lei Federal nº 13.709/2018 — LGPD.<br>
        ESCOLA TÉCNICA DE CEILÂNDIA &nbsp;|&nbsp; EQNN 14 Área Especial S/Nº - Ceilândia Sul - DF - CEP: 72220-140 &nbsp;|&nbsp; Tel: (61) 3901-7545 / 3901-6927
    </div>

</div>
