<?php
function fpp($val) { return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'); }

// Monta o texto do perfil a partir dos dados salvos
$idade = fpp($aluno['idade'] ?? '');
$diagnostico = fpp($aluno['diagnostico_detalhado'] ?? '');
$cid = fpp($aluno['cid_codigos'] ?? '');
$modalidade = fpp($aluno['modalidade_ano_turma_turno'] ?? '');

// Texto principal do perfil: usa vida_pessoal se existir, senão monta do diagnóstico
$textoPerfil = '';
if (!empty($p['vida_pessoal'])) {
    $textoPerfil = fpp($p['vida_pessoal']);
} else {
    $partes = [];
    if ($idade) $partes[] = $idade . ' anos';
    if ($diagnostico) $partes[] = $diagnostico;
    $textoPerfil = implode(' - ', $partes);
}

// Foto do aluno
$fotoUrl = '';
if (!empty($aluno['foto'])) {
    $fotoPath = trim($aluno['foto'], '/\\');
    $appBase = '/SistemaAdequacao/';

    // Garante URL absoluta com host e protocolo
    $protocol = (!empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $fotoUrl = $protocol . '://' . $host . $appBase . $fotoPath;

    // Fallback em caso de ambiente sem REQUEST_SCHEME (gerador de PDF via CLI)
    if (empty($_SERVER['HTTP_HOST']) && file_exists(__DIR__ . '/../../' . $fotoPath)) {
        $fotoUrl = 'file://' . realpath(__DIR__ . '/../../' . $fotoPath);
    }
}

$nomeAluno = strtoupper(fpp($aluno['nome_completo'] ?? ''));
$subTitulo  = strtoupper($modalidade);
if ($cid) $subTitulo .= ' (' . strtoupper($cid) . ')';
?>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #fff; color: #000; }
.wrap { max-width: 900px; margin: 0 auto; padding: 16px; }

/* Topo: logo Sala de Recursos centralizado */
.topo { text-align: center; padding-bottom: 10px; }
.topo img { height: 90px; }

/* Linha dupla azul */
.linha-dupla { border-top: 4px solid #1a6fa8; border-bottom: 2px solid #1a6fa8; padding: 2px 0; margin-bottom: 0; }

/* Corpo principal */
.corpo { display: flex; min-height: 420px; border-bottom: 4px solid #1a6fa8; }

/* Coluna esquerda: logos */
.col-esq {
    width: 90px;
    min-width: 90px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    padding: 16px 8px;
    border-right: 3px solid #1a6fa8;
}
.col-esq img { width: 70px; }

/* Coluna central: nome + texto */
.col-centro {
    flex: 1;
    padding: 24px 28px;
    border-right: 3px solid #1a6fa8;
}
.nome-aluno {
    font-size: 22px;
    font-weight: bold;
    color: #1a6fa8;
    text-align: center;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
}
.sub-titulo {
    font-size: 13px;
    font-weight: bold;
    color: #1a6fa8;
    text-align: center;
    margin-bottom: 20px;
}
.texto-perfil {
    font-size: 14px;
    line-height: 1.9;
    text-align: justify;
    color: #000;
}
.texto-perfil::before {
    content: "➤ ";
    color: #1a6fa8;
    font-size: 16px;
}

/* Coluna direita: foto */
.col-dir {
    width: 220px;
    min-width: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 16px;
}
.foto-box {
    border: 3px solid #1a6fa8;
    width: 180px;
    height: 230px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #f0f4f8;
}
.foto-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.foto-box .sem-foto {
    color: #aaa;
    font-size: 12px;
    text-align: center;
    padding: 10px;
}

@media print {
    .no-print { display: none !important; }
    .wrap { padding: 0; }
    @page { size: A4 landscape; margin: 8mm; }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<div class="wrap">

    <!-- TOPO: Logo Sala de Recursos -->
    <div class="topo">
        <img src="ASSETS/imagens/sala_recursos.png" alt="Sala de Recursos ETC">
    </div>

    <!-- LINHA DUPLA AZUL -->
    <div class="linha-dupla"></div>

    <!-- CORPO -->
    <div class="corpo">

        <!-- COLUNA ESQUERDA: logos -->
        <div class="col-esq">
            <img src="ASSETS/imagens/etc.png" alt="ETC">
            <img src="ASSETS/imagens/cids.png" alt="Inclusão">
            <img src="ASSETS/imagens/inclusao_escolar.png" alt="Inclusão Escolar">
        </div>

        <!-- COLUNA CENTRAL: nome e texto -->
        <div class="col-centro">
            <div class="nome-aluno"><?php echo $nomeAluno; ?></div>
            <?php if ($subTitulo): ?>
                <div class="sub-titulo"><?php echo $subTitulo; ?></div>
            <?php endif; ?>
            <div class="texto-perfil"><?php echo nl2br($textoPerfil); ?></div>
        </div>

        <!-- COLUNA DIREITA: foto -->
        <div class="col-dir">
            <div class="foto-box">
                <?php if ($fotoUrl): ?>
                    <img src="<?php echo $fotoUrl; ?>" alt="Foto do aluno">
                <?php else: ?>
                    <div class="sem-foto">Sem foto cadastrada</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
