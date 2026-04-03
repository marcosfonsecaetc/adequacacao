<?php
function fcp($val) { return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'); }
$dn = !empty($aluno['data_nascimento']) ? date('d/m/Y', strtotime($aluno['data_nascimento'])) : '—';
$dr = !empty($paee['data_registro'])    ? date('d/m/Y', strtotime($paee['data_registro']))    : date('d/m/Y');
$ci = !empty($paee['composicao_individual']) ? 'X' : ' ';
$cg = !empty($paee['composicao_grupo'])      ? 'X' : ' ';

// Busca colaboradores por papel para assinaturas
global $pdo;
$stmtAEE = $pdo->prepare("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 3 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
$stmtAEE->execute(); $colAEE = $stmtAEE->fetchAll(PDO::FETCH_ASSOC);

$stmtGestor = $pdo->prepare("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 2 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
$stmtGestor->execute(); $colGestor = $stmtGestor->fetchAll(PDO::FETCH_ASSOC);

$stmtApoio = $pdo->prepare("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id IN (5,6,7) AND u.ativo = 1 ORDER BY u.nome_completo ASC");
$stmtApoio->execute(); $colApoio = $stmtApoio->fetchAll(PDO::FETCH_ASSOC);

$stmtSec = $pdo->prepare("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 8 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
$stmtSec->execute(); $colSec = $stmtSec->fetchAll(PDO::FETCH_ASSOC);

$stmtCoo = $pdo->prepare("SELECT u.nome_completo, u.matricula_servidor FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 9 AND u.ativo = 1 ORDER BY u.nome_completo ASC");
$stmtCoo->execute(); $colCoo = $stmtCoo->fetchAll(PDO::FETCH_ASSOC);

function assinLinha(array $lista, int $idx, string $cargo): string {
    $col  = $lista[$idx] ?? null;
    $nome = ($col && !empty($col['nome_completo'])) ? htmlspecialchars($col['nome_completo'], ENT_QUOTES, 'UTF-8') : '_______________________________';
    $mat  = ($col && !empty($col['matricula_servidor'])) ? ' &mdash; Matrícula: ' . htmlspecialchars($col['matricula_servidor'], ENT_QUOTES, 'UTF-8') : '';
    return '<div class="assin-line">' . $nome . $mat . '<br><small>' . htmlspecialchars($cargo) . '</small></div>';
}
?>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:Arial,sans-serif;font-size:11px;color:#000;background:#fff;}
.wrap{max-width:720px;margin:0 auto;padding:10px;}

/* CABEÇALHO */
.cab{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
.cab-logo img{height:60px;}
.cab-texto{flex:1;text-align:center;font-size:10.5px;line-height:1.7;padding:0 10px;}
.cab-texto strong{font-size:11px;}
.cab-texto u{text-decoration:underline;}

/* TÍTULO PRINCIPAL */
.titulo-box{border:1px solid #000;padding:7px 14px;text-align:center;margin-bottom:18px;}
.titulo-box span{font-size:13px;font-weight:bold;}

/* SEÇÃO I - DADOS */
.sec-i-label{display:flex;align-items:center;gap:5px;font-weight:bold;font-size:11.5px;margin-bottom:4px;}
.sec-i-label .icone{font-size:13px;}

/* TABELA PRINCIPAL */
.tbl{width:100%;border-collapse:collapse;margin-bottom:0;}
.tbl td,.tbl th{border:1px solid #000;padding:4px 6px;vertical-align:top;font-size:11px;}
.tbl .sub-header{background:#e0e0e0;text-align:center;font-weight:bold;font-size:11px;padding:4px 6px;}
.tbl .row-header{background:#f0f0f0;font-weight:bold;font-size:11px;padding:3px 6px;}
.tbl .col-header{background:#d0d0d0;font-weight:bold;text-align:center;width:50%;padding:3px 6px;}
.tbl .txt{text-align:justify;padding:4px 6px;font-size:11px;line-height:1.5;}
.tbl .txt-pre{text-align:justify;padding:4px 6px;font-size:11px;line-height:1.6;white-space:pre-wrap;}

/* ASSINATURAS */
.assin{margin-top:30px;}
.assin-line{border-top:1px solid #000;margin-top:32px;padding-top:3px;font-size:10px;text-align:center;}

/* RODAPÉ */
.rodape{margin-top:10px;font-size:8px;color:#444;text-align:center;border-top:1px solid #ccc;padding-top:4px;}

@media print{
    .no-print{display:none!important;}
    .wrap{padding:0;}
    @page{size:A4 portrait;margin:10mm;}
}
</style>

<div class="wrap">

    <!-- CABEÇALHO -->
    <div class="cab">
        <div class="cab-logo"><img src="ASSETS/imagens/gdf.png" alt="ETC"></div>
        <div class="cab-texto">
            SECRETARIA DE ESTADO DE EDUCAÇÃO DO DISTRITO FEDERAL<br>
            COORDENAÇÃO REGIONAL DE ENSINO DE CEILÂNDIA<br>
            <strong>ESCOLA TÉCNICA DE CEILÂNDIA – CEP-ETC</strong><br>
            <u>SALA DE RECURSOS ETC</u>
        </div>
        <div class="cab-logo"><img src="ASSETS/imagens/sala_recursos.png" alt="Sala de Recursos ETC"></div>
    </div>

    <!-- TÍTULO -->
    <div class="titulo-box">
        <span>Formulário de Registro Anual do Plano de AEE</span>
    </div>

    <!-- I - DADOS REFERENTES AO ESTUDANTE -->
    <div class="sec-i-label">
        <span class="icone">⊞</span>
        <span>I - Dados referentes ao estudante</span>
    </div>

    <table class="tbl">
        <!-- Sub-cabeçalho AEE -->
        <tr>
            <td colspan="2" class="sub-header">
                Atendimento Educacional Especializado – AEE / Salas de Recursos<br>
                PLANO DE AEE – Modalidade &nbsp;<u><?php echo fcp($paee['modalidade'] ?? 'Generalista'); ?></u>
            </td>
        </tr>

        <!-- 1. Identificação -->
        <tr><td colspan="2" class="row-header">1 - Identificação do Estudante</td></tr>
        <tr>
            <td colspan="2" class="txt">
                Nome do estudante: <strong><?php echo fcp($aluno['nome_completo']); ?></strong>,
                nascido <?php echo $dn; ?>,
                filho de <?php echo fcp($aluno['filiacao_mae']); ?>/<?php echo fcp($aluno['filiacao_pai']); ?>,
                Residente à <?php echo fcp($aluno['endereco']); ?>,
                atualmente com <?php echo fcp($aluno['idade']); ?> anos,
                está regularmente matriculado na Escola Técnica de Ceilândia no curso
                <?php echo fcp($aluno['modalidade_ano_turma_turno']); ?>
                matriculado sob o número: <?php echo fcp($aluno['matricula']); ?>.
                Professoras do AEE <?php echo fcp($paee['professoras_aee'] ?? ''); ?>.<br><br>
                Professores regentes:<br>
                <span style="white-space:pre-wrap;"><?php echo fcp($paee['professores_regentes'] ?? ''); ?></span>
            </td>
        </tr>

        <!-- 2. Perfil -->
        <tr><td colspan="2" class="row-header">2 – Síntese do contexto educacional do estudante/Perfil do estudante</td></tr>
        <tr>
            <td colspan="2" class="txt" style="min-height:50px;">
                <?php echo nl2br(fcp($paee['perfil_estudante'] ?? '')); ?>
            </td>
        </tr>

        <!-- 3. Áreas do Desenvolvimento -->
        <tr><td colspan="2" class="row-header">3 – Áreas do Desenvolvimento</td></tr>

        <?php
        $areas = [
            ['label' => '3.1. Linguagem',                         'key' => 'ling'],
            ['label' => '3.2. Desenvolvimento Psicomotor',        'key' => 'psicomotor'],
            ['label' => '3.3. Desenvolvimento Cognitivo (aprendizagens)', 'key' => 'cognitivo'],
            ['label' => '3.4. Aspectos Sociais',                  'key' => 'social'],
            ['label' => '3.5. Contexto Familiar',                 'key' => 'familiar'],
        ];
        foreach ($areas as $area): ?>
        <tr><td colspan="2" class="row-header" style="background:#f8f8f8;"><?php echo $area['label']; ?></td></tr>
        <tr>
            <td class="col-header">Habilidades</td>
            <td class="col-header">Dificuldades</td>
        </tr>
        <tr>
            <td class="txt" style="min-height:28px;"><?php echo nl2br(fcp($paee[$area['key'].'_habilidades'] ?? '')); ?></td>
            <td class="txt" style="min-height:28px;"><?php echo nl2br(fcp($paee[$area['key'].'_dificuldades'] ?? '')); ?></td>
        </tr>
        <?php endforeach; ?>

        <!-- 4. Acessibilidade -->
        <tr><td colspan="2" class="row-header">4 – Acessibilidade/Necessidades Específicas do Estudante</td></tr>
        <tr>
            <td colspan="2" class="txt" style="min-height:40px;">
                <?php echo nl2br(fcp($paee['acessibilidade'] ?? '')); ?>
            </td>
        </tr>

        <!-- 5. Objetivos -->
        <tr><td colspan="2" class="row-header">5 - Objetivos do AEE para as aprendizagens do estudante</td></tr>
        <tr>
            <td colspan="2" class="txt" style="min-height:40px;">
                <?php echo nl2br(fcp($paee['objetivos'] ?? '')); ?>
            </td>
        </tr>

        <!-- 6. Organização -->
        <tr><td colspan="2" class="row-header">6 - Organização do AEE</td></tr>
        <tr>
            <td colspan="2" class="txt">
                <strong>Frequência:</strong> <?php echo fcp($paee['frequencia'] ?? ''); ?><br>
                <strong>Tempo de atendimento:</strong> <?php echo fcp($paee['tempo_atendimento'] ?? ''); ?><br>
                <strong>Composição do atendimento:</strong>
                ( <?php echo $ci; ?> ) individual &nbsp;&nbsp; ( <?php echo $cg; ?> ) em grupo<br>
                <?php if (!empty($paee['composicao_outros'])): ?>
                <strong>Outros:</strong> <?php echo nl2br(fcp($paee['composicao_outros'])); ?>
                <?php endif; ?>
            </td>
        </tr>

        <!-- 7. Atividades -->
        <tr><td colspan="2" class="row-header">7 - Atividades pedagógicas a serem desenvolvidas no AEE pelo estudante</td></tr>
        <tr>
            <td colspan="2" class="txt" style="min-height:50px;">
                <?php echo nl2br(fcp($paee['atividades_pedagogicas'] ?? '')); ?>
            </td>
        </tr>

        <!-- 8. Profissionais -->
        <tr><td colspan="2" class="row-header">8 - Profissionais, familiar(es) e/ou instituições que deverão ser envolvidos no acompanhamento do desenvolvimento do estudante</td></tr>
        <tr>
            <td colspan="2" class="txt">
                <?php echo nl2br(fcp($paee['profissionais_envolvidos'] ?? '')); ?>
            </td>
        </tr>

        <!-- 9. Avaliação -->
        <tr><td colspan="2" class="row-header">9 - Avaliação dos resultados</td></tr>
        <tr>
            <td colspan="2" class="txt" style="min-height:30px;">
                <?php echo nl2br(fcp($paee['avaliacao_resultados'] ?? '')); ?>
            </td>
        </tr>

        <!-- 10. Encaminhamentos -->
        <tr><td colspan="2" class="row-header">10 - Encaminhamentos</td></tr>
        <tr>
            <td colspan="2" class="txt" style="min-height:30px;">
                <?php echo nl2br(fcp($paee['encaminhamentos'] ?? '')); ?>
            </td>
        </tr>

        <!-- Data -->
        <tr>
            <td colspan="2" class="txt" style="text-align:right;">
                Data: <?php echo $dr; ?>
            </td>
        </tr>
    </table>

    <!-- ASSINATURAS -->
    <div class="assin">
        <?php echo assinLinha($colAEE, 0, 'Professora do AEE — Humanas'); ?>
        <?php echo assinLinha($colAEE, 1, 'Professora do AEE — Exatas'); ?>
        <?php echo assinLinha($colApoio, 0, 'Orientador(a) Educacional'); ?>
        <?php echo assinLinha($colCoo, 0, 'Coordenador(a) Pedagógico(a)'); ?>
        <?php echo assinLinha($colSec, 0, 'Secretário(a) Escolar'); ?>
    </div>

    <!-- RODAPÉ -->
    <div class="rodape">
        Comunicamos a proibição de uso deste formulário para divulgação de qualquer informação constante no presente documento, em observância ao disposto na Lei Federal nº 13.709/2018 — LGPD.<br>
        ESCOLA TÉCNICA DE CEILÂNDIA &nbsp;|&nbsp; EQNN 14 Área Especial S/Nº - Ceilândia Sul - DF - CEP: 72220-140 &nbsp;|&nbsp; Tel: (61) 3901-7545 / 3901-6927
    </div>

</div>
