<?php
// Inicia a sessão para controle de usuário em todo o app.
session_start();

// Caminho base do projeto (exemplo: c:\xampp\htdocs\SistemaAdequacao\)
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);

// Configurações de acesso ao banco de dados (ajuste conforme seu ambiente)
$dbConfig = [
    'host' => 'localhost',
    'name' => 'sistema_adequacao_etc',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
];

// Cria conexão PDO com tratamento de exceção para erros de conexão.
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']}",
        $dbConfig['user'],
        $dbConfig['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

// Inclui arquivos com segurança: verifica existência antes.
function safe_include(string $path): void
{
    if (!file_exists($path)) {
        die('Erro crítico: arquivo não encontrado: ' . $path);
    }

    require_once $path;
}

// Importa classes do sistema (modelo, DAO, controlador).
$pathsToLoad = [
    'MODEL/DTO/UsuarioDTO.php',
    'MODEL/DTO/AlunoDTO.php',
    'MODEL/DAO/UsuarioDAO.php',
    'MODEL/DAO/AlunoDAO.php',
    'CONTROL/AuthController.php',
];

foreach ($pathsToLoad as $relativePath) {
    safe_include(BASE_PATH . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
}

// Inicia controller de autenticação.
$authController = new AuthController($pdo);

// Roteamento simples via query string ?action=xxx
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        safe_include(BASE_PATH . 'VIEW/Auth/login.php');
        break;

    case 'cadastrar':
        safe_include(BASE_PATH . 'VIEW/Auth/cadastro_usuario.php');
        break;

    case 'processar_cadastro':
        $authController->processarCadastro();
        break;

    case 'logar':
        $authController->logar();
        break;

    case 'dashboard':
        // Exibe a página inicial correta baseado no papel do usuário
        if (empty($_SESSION['usuario_papel'])) {
            header('Location: index.php?action=login');
            exit;
        }

        switch ((int)$_SESSION['usuario_papel']) {
            case 4:
                safe_include(BASE_PATH . 'VIEW/Home/professor.php');
                break;

            case 3:
                safe_include(BASE_PATH . 'VIEW/Home/aee.php');
                break;

            case 2:
                safe_include(BASE_PATH . 'VIEW/Home/gestor.php');
                break;

            default:
                // Caso não tenha página específica, volta para login ou mostra mensagem
                echo '<div class="alert alert-warning">Acesso não autorizado. Faça login novamente.</div>';
                session_destroy();
                echo '<a href="index.php?action=login" class="btn btn-primary">Ir para login</a>';
                break;
        }

        break;

    case 'editar_aluno':
        $id = (int)($_GET['aluno_id'] ?? 0);
        if (!$id) { header('Location: index.php?action=listar_alunos'); exit; }
        $alunoDAO = new AlunoDAO($pdo);
        $aluno = $alunoDAO->buscarPorId($id);
        if (!$aluno) { header('Location: index.php?action=listar_alunos'); exit; }
        require BASE_PATH . 'VIEW/Home/editar_aluno.php';
        break;

    case 'processar_editar_aluno':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['aluno_id'] ?? 0);
            if (!$id) { header('Location: index.php?action=listar_alunos'); exit; }

            $fotoPath = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/ASSETS/uploads/fotos/';
                $check = getimagesize($_FILES['foto']['tmp_name']);
                if ($check && $_FILES['foto']['size'] <= 2 * 1024 * 1024 &&
                    in_array($_FILES['foto']['type'], ['image/jpeg','image/png','image/gif'])) {
                    $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fileName)) {
                        $fotoPath = 'ASSETS/uploads/fotos/' . $fileName;
                    }
                }
            }

            $alunoDAO = new AlunoDAO($pdo);
            $alunoDAO->atualizar($id, array_map('trim', $_POST), $fotoPath);
            $_SESSION['flash_success'] = 'Aluno atualizado com sucesso!';
            header('Location: index.php?action=listar_alunos');
            exit;
        }
        header('Location: index.php?action=listar_alunos');
        exit;

    case 'cadastrar_aluno':
        safe_include(BASE_PATH . 'VIEW/Home/cadastrar_aluno.php');
        break;

    case 'listar_alunos':
        safe_include(BASE_PATH . 'VIEW/Home/listar_alunos.php');
        break;

    case 'processar_cadastro_aluno':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeCompleto = trim($_POST['nome'] ?? '');
            $matricula = trim($_POST['matricula'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (!$nomeCompleto || !$matricula || !$email) {
                $_SESSION['flash_error'] = 'Os campos Nome, Matrícula e E-mail são obrigatórios.';
                header('Location: index.php?action=cadastrar_aluno');
                exit;
            }

            // Processar upload da foto
            $fotoPath = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/ASSETS/uploads/fotos/';
                $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
                $targetFile = $uploadDir . $fileName;

                // Verificar se é uma imagem real
                $check = getimagesize($_FILES['foto']['tmp_name']);
                if ($check === false) {
                    $_SESSION['flash_error'] = 'O arquivo enviado não é uma imagem válida.';
                    header('Location: index.php?action=cadastrar_aluno');
                    exit;
                }

                // Verificar tamanho (2MB máximo)
                if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                    $_SESSION['flash_error'] = 'A imagem deve ter no máximo 2MB.';
                    header('Location: index.php?action=cadastrar_aluno');
                    exit;
                }

                // Verificar tipo de arquivo
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
                    $_SESSION['flash_error'] = 'Apenas arquivos JPG, PNG e GIF são permitidos.';
                    header('Location: index.php?action=cadastrar_aluno');
                    exit;
                }

                // Mover arquivo para o diretório de uploads
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                    $fotoPath = 'ASSETS/uploads/fotos/' . $fileName;
                } else {
                    $_SESSION['flash_error'] = 'Erro ao fazer upload da imagem.';
                    header('Location: index.php?action=cadastrar_aluno');
                    exit;
                }
            }

            $alunoDTO = new AlunoDTO(
                $nomeCompleto,
                $matricula,
                $email,
                $fotoPath,
                trim($_POST['data_nascimento'] ?? '') ?: null,
                is_numeric($_POST['idade']) ? (int)$_POST['idade'] : null,
                trim($_POST['modalidade_ano_turma_turno'] ?? '') ?: null,
                trim($_POST['endereco'] ?? '') ?: null,
                trim($_POST['telefones_responsaveis'] ?? '') ?: null,
                trim($_POST['filiacao_mae'] ?? '') ?: null,
                trim($_POST['filiacao_pai'] ?? '') ?: null,
                trim($_POST['periodo_vigencia_adequacao'] ?? '') ?: null,
                trim($_POST['diagnostico_detalhado'] ?? '') ?: null,
                trim($_POST['cid_codigos'] ?? '') ?: null
            );

            $alunoDAO = new AlunoDAO($pdo);

            if ($alunoDAO->cadastrar($alunoDTO)) {
                $_SESSION['flash_success'] = 'Aluno cadastrado com sucesso!';
                header('Location: index.php?action=dashboard');
                exit;
            }

            $_SESSION['flash_error'] = 'Erro ao cadastrar aluno. Tente novamente.';
            header('Location: index.php?action=cadastrar_aluno');
            exit;
        }

        header('Location: index.php?action=cadastrar_aluno');
        exit;

    case 'logout':
        // Encerra a sessão e volta para o login
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit;

    case 'criar_adequacao':
        // Tela para criar nova adequação curricular - seleciona aluno
        safe_include(BASE_PATH . 'VIEW/Home/criar_adequacao.php');
        break;

    case 'incluir_informacoes_aluno':
        // Tela para AEE incluir informações biopsicossociais do aluno
        safe_include(BASE_PATH . 'VIEW/Home/incluir_informacoes_aluno.php');
        break;

    case 'processar_incluir_informacoes':
        // Processa seleção do aluno e redireciona para preenchimento das informações
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alunoId = $_POST['aluno_id'] ?? null;
            if (!$alunoId) {
                $_SESSION['flash_error'] = 'Erro: Nenhum aluno selecionado.';
                header('Location: index.php?action=incluir_informacoes_aluno');
                exit;
            }
            // Redireciona para preenchimento passando aluno_id via GET
            header('Location: index.php?action=preencher_informacoes_aluno&aluno_id=' . urlencode($alunoId));
            exit;
        }
        header('Location: index.php?action=incluir_informacoes_aluno');
        exit;

    case 'gerar_lista_adequacao':
        safe_include(BASE_PATH . 'VIEW/Home/gerar_lista_adequacao.php');
        break;

    case 'gerar_pdf_ficha':
        $id = (int)($_GET['aluno_id'] ?? 0);
        if (!$id) { echo '<p>ID inválido.</p>'; exit; }
        $alunoDAO = new AlunoDAO($pdo);
        $aluno = $alunoDAO->buscarPorId($id);
        if (!$aluno) { echo '<p>Aluno não encontrado.</p>'; exit; }
        $aee    = $alunoDAO->buscarDadosAEE($id);
        $s      = $aee['saude']      ?? [];
        $h      = $aee['habilidade'] ?? [];
        $ad     = $aee['adequacao']  ?? [];
        $stmtPP = $pdo->prepare("SELECT pp.*, u.email as professor_email FROM plano_pedagogico pp LEFT JOIN usuario u ON u.id = pp.professor_id WHERE pp.aluno_id = :id");
        $stmtPP->execute([':id' => $id]);
        $planos = $stmtPP->fetchAll(PDO::FETCH_ASSOC);
        $stmtEnc = $pdo->prepare("SELECT * FROM encaminhamento_final WHERE aluno_id = :id");
        $stmtEnc->execute([':id' => $id]);
        $enc = $stmtEnc->fetch(PDO::FETCH_ASSOC) ?: [];
        $stmtAEE = $pdo->query("SELECT u.email FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 3 AND u.ativo = 1");
        $usuariosAEE = $stmtAEE->fetchAll(PDO::FETCH_COLUMN);
        $stmtGestor = $pdo->query("SELECT u.email FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 2 AND u.ativo = 1");
        $usuariosGestor = $stmtGestor->fetchAll(PDO::FETCH_COLUMN);
        $nomeAluno = htmlspecialchars($aluno['nome_completo'] ?? 'ficha', ENT_QUOTES, 'UTF-8');
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8">';
        echo '<title>Ficha - ' . $nomeAluno . '</title>';
        echo '<style>@page{size:A4 landscape;margin:10mm}body{margin:0}@media print{.no-print{display:none!important}}</style>';
        echo '</head><body>';
        echo '<div class="no-print" style="padding:8px;background:#f8f9fa;border-bottom:1px solid #ddd;font-family:Arial,sans-serif">';
        echo '<button onclick="window.print()" style="padding:6px 16px;background:#198754;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px">🖨️ Imprimir / Salvar PDF</button>';
        echo '<span style="margin-left:12px;font-size:12px;color:#555">Use &ldquo;Salvar como PDF&rdquo; na impressora para gerar o arquivo.</span>';
        echo '</div>';
        require BASE_PATH . 'VIEW/Home/ficha_completa_parcial.php';
        echo '<script>window.onload=function(){window.print();}</script>';
        echo '</body></html>';
        exit;

    case 'ajax_ficha_completa':
        header('Content-Type: text/html; charset=utf-8');
        $id = (int)($_GET['aluno_id'] ?? 0);
        if (!$id) { echo '<p class="text-danger">ID inválido.</p>'; exit; }
        $alunoDAO = new AlunoDAO($pdo);
        $aluno = $alunoDAO->buscarPorId($id);
        if (!$aluno) { echo '<p class="text-danger">Aluno não encontrado.</p>'; exit; }
        $aee    = $alunoDAO->buscarDadosAEE($id);
        $s      = $aee['saude']      ?? [];
        $h      = $aee['habilidade'] ?? [];
        $ad     = $aee['adequacao']  ?? [];
        $stmtPP = $pdo->prepare("SELECT pp.*, u.email as professor_email FROM plano_pedagogico pp LEFT JOIN usuario u ON u.id = pp.professor_id WHERE pp.aluno_id = :id");
        $stmtPP->execute([':id' => $id]);
        $planos = $stmtPP->fetchAll(PDO::FETCH_ASSOC);
        $stmtEnc = $pdo->prepare("SELECT * FROM encaminhamento_final WHERE aluno_id = :id");
        $stmtEnc->execute([':id' => $id]);
        $enc = $stmtEnc->fetch(PDO::FETCH_ASSOC) ?: [];
        // Busca usuários AEE (papel 3)
        $stmtAEE = $pdo->query("SELECT u.email FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 3 AND u.ativo = 1");
        $usuariosAEE = $stmtAEE->fetchAll(PDO::FETCH_COLUMN);
        // Busca gestores (papel 2)
        $stmtGestor = $pdo->query("SELECT u.email FROM usuario u INNER JOIN usuario_papel up ON up.usuario_id = u.id WHERE up.papel_id = 2 AND u.ativo = 1");
        $usuariosGestor = $stmtGestor->fetchAll(PDO::FETCH_COLUMN);
        require BASE_PATH . 'VIEW/Home/ficha_completa_parcial.php';
        exit;

    case 'ajax_dados_aee':
        header('Content-Type: application/json');
        $id = (int)($_GET['aluno_id'] ?? 0);
        if (!$id) { echo json_encode([]); exit; }
        $alunoDAO = new AlunoDAO($pdo);
        echo json_encode($alunoDAO->buscarDadosAEE($id));
        exit;

    case 'preencher_informacoes_aluno':
        // Tela para AEE preencher informações biopsicossociais do aluno
        safe_include(BASE_PATH . 'VIEW/Home/preencher_informacoes_aluno.php');
        break;

    case 'processar_informacoes_aluno':
        // Processa dados das informações biopsicossociais do aluno
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alunoId = $_POST['aluno_id'] ?? null;
            if (!$alunoId) {
                $_SESSION['flash_error'] = 'Erro: Dados inválidos.';
                header('Location: index.php?action=incluir_informacoes_aluno');
                exit;
            }

            try {
                // Salva saude_escolarizacao (cria ou atualiza)
                $stmt1 = $pdo->prepare("INSERT INTO saude_escolarizacao
                    (aluno_id, percurso_escolarizacao, atendimentos_passados, atendimentos_atuais, faz_uso_medicacao, medicacao_especificacao)
                    VALUES (:aluno_id, :percurso_escolarizacao, :atendimentos_passados, :atendimentos_atuais, :faz_uso_medicacao, :medicacao_especificacao)
                    ON DUPLICATE KEY UPDATE
                    percurso_escolarizacao = VALUES(percurso_escolarizacao),
                    atendimentos_passados  = VALUES(atendimentos_passados),
                    atendimentos_atuais    = VALUES(atendimentos_atuais),
                    faz_uso_medicacao      = VALUES(faz_uso_medicacao),
                    medicacao_especificacao = VALUES(medicacao_especificacao)");
                $stmt1->execute([
                    ':aluno_id'               => $alunoId,
                    ':percurso_escolarizacao' => trim($_POST['percurso_escolarizacao'] ?? ''),
                    ':atendimentos_passados'  => trim($_POST['atendimentos_passados'] ?? ''),
                    ':atendimentos_atuais'    => trim($_POST['atendimentos_atuais'] ?? ''),
                    ':faz_uso_medicacao'      => (int)($_POST['faz_uso_medicacao'] ?? 0),
                    ':medicacao_especificacao'=> trim($_POST['medicacao_especificacao'] ?? ''),
                ]);

                // Salva habilidade_biopsicossocial (cria ou atualiza)
                $stmt2 = $pdo->prepare("INSERT INTO habilidade_biopsicossocial
                    (aluno_id, comunicacao, aspectos_motores, vida_autonoma, aspectos_sociais)
                    VALUES (:aluno_id, :comunicacao, :aspectos_motores, :vida_autonoma, :aspectos_sociais)
                    ON DUPLICATE KEY UPDATE
                    comunicacao      = VALUES(comunicacao),
                    aspectos_motores = VALUES(aspectos_motores),
                    vida_autonoma    = VALUES(vida_autonoma),
                    aspectos_sociais = VALUES(aspectos_sociais)");
                $stmt2->execute([
                    ':aluno_id'        => $alunoId,
                    ':comunicacao'     => trim($_POST['comunicacao'] ?? ''),
                    ':aspectos_motores'=> trim($_POST['aspectos_motores'] ?? ''),
                    ':vida_autonoma'   => trim($_POST['vida_autonoma'] ?? ''),
                    ':aspectos_sociais'=> trim($_POST['aspectos_sociais'] ?? ''),
                ]);

                // Salva adequacao_organizativa (cria ou atualiza)
                $stmt3 = $pdo->prepare("INSERT INTO adequacao_organizativa
                    (aluno_id, espaco_sala, recursos_gerais, flexibilizacao_tempo, avaliacao_diagnostica, adequacao_temporalidade_legal)
                    VALUES (:aluno_id, :espaco_sala, :recursos_gerais, :flexibilizacao_tempo, :avaliacao_diagnostica, :adequacao_temporalidade_legal)
                    ON DUPLICATE KEY UPDATE
                    espaco_sala                   = VALUES(espaco_sala),
                    recursos_gerais               = VALUES(recursos_gerais),
                    flexibilizacao_tempo          = VALUES(flexibilizacao_tempo),
                    avaliacao_diagnostica         = VALUES(avaliacao_diagnostica),
                    adequacao_temporalidade_legal = VALUES(adequacao_temporalidade_legal)");
                $stmt3->execute([
                    ':aluno_id'                    => $alunoId,
                    ':espaco_sala'                 => trim($_POST['espaco_sala'] ?? ''),
                    ':recursos_gerais'             => trim($_POST['recursos_gerais'] ?? ''),
                    ':flexibilizacao_tempo'        => trim($_POST['flexibilizacao_tempo'] ?? ''),
                    ':avaliacao_diagnostica'       => trim($_POST['avaliacao_diagnostica'] ?? ''),
                    ':adequacao_temporalidade_legal' => trim($_POST['adequacao_temporalidade_legal'] ?? ''),
                ]);

                $_SESSION['flash_success'] = 'Informações biopsicossociais do aluno salvas com sucesso!';
                header('Location: index.php?action=dashboard');
                exit;

            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Erro ao salvar informações: ' . $e->getMessage();
                header('Location: index.php?action=preencher_informacoes_aluno&aluno_id=' . urlencode($alunoId));
                exit;
            }
        }
        header('Location: index.php?action=incluir_informacoes_aluno');
        exit;

    case 'processar_criar_adequacao':
        // Processa seleção do aluno e redireciona para preenchimento
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alunoId = $_POST['aluno_id'] ?? null;
            if (!$alunoId) {
                $_SESSION['flash_error'] = 'Erro: Nenhum aluno selecionado.';
                header('Location: index.php?action=criar_adequacao');
                exit;
            }
            // Redireciona para preenchimento passando aluno_id via GET
            header('Location: index.php?action=preencher_adequacao&aluno_id=' . urlencode($alunoId));
            exit;
        }
        header('Location: index.php?action=criar_adequacao');
        exit;

    case 'preencher_adequacao':
        // Tela para preencher a adequação curricular
        safe_include(BASE_PATH . 'VIEW/Home/preencher_adequacao.php');
        break;

    case 'processar_secao_2':
        // Processa dados da Seção 2: Escolarização e Saúde
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alunoId = $_POST['aluno_id'] ?? null;
            if (!$alunoId) {
                $_SESSION['flash_error'] = 'Erro: Dados inválidos.';
                header('Location: index.php?action=criar_adequacao');
                exit;
            }

            // Validação básica
            $percursoEscolarizacao = trim($_POST['percurso_escolarizacao'] ?? '');
            if (!$percursoEscolarizacao) {
                $_SESSION['flash_error'] = 'Campo "Percurso de Escolarização" é obrigatório.';
                header('Location: index.php?action=preencher_adequacao&aluno_id=' . urlencode($alunoId));
                exit;
            }

            try {
                // Insere dados na tabela saude_escolarizacao
                $sql = "INSERT INTO saude_escolarizacao (
                            aluno_id, percurso_escolarizacao, atendimentos_passados, 
                            atendimentos_atuais, faz_uso_medicacao, medicacao_especificacao
                        ) VALUES (
                            :aluno_id, :percurso_escolarizacao, :atendimentos_passados,
                            :atendimentos_atuais, :faz_uso_medicacao, :medicacao_especificacao
                        )";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':aluno_id' => $alunoId,
                    ':percurso_escolarizacao' => $percursoEscolarizacao,
                    ':atendimentos_passados' => trim($_POST['atendimentos_passados'] ?? ''),
                    ':atendimentos_atuais' => trim($_POST['atendimentos_atuais'] ?? ''),
                    ':faz_uso_medicacao' => (int)($_POST['faz_uso_medicacao'] ?? 0),
                    ':medicacao_especificacao' => trim($_POST['medicacao_especificacao'] ?? ''),
                ]);

                $_SESSION['flash_success'] = 'Seção 2 preenchida com sucesso!';
                // Por enquanto redireciona de volta para dashboard (próximas seções podem ser implementadas)
                header('Location: index.php?action=dashboard');
                exit;

            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Erro ao salvar dados: ' . $e->getMessage();
                header('Location: index.php?action=preencher_adequacao&aluno_id=' . urlencode($alunoId));
                exit;
            }
        }
        header('Location: index.php?action=criar_adequacao');
        exit;

    case 'processar_plano_pedagogico':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alunoId    = $_POST['aluno_id']    ?? null;
            $professorId = $_POST['professor_id'] ?? null;

            if (!$alunoId || !$professorId) {
                $_SESSION['flash_error'] = 'Erro: Dados inválidos.';
                header('Location: index.php?action=dashboard');
                exit;
            }

            $disciplinaNome         = trim($_POST['disciplina_nome']          ?? '');
            $periodoVigencia        = trim($_POST['periodo_vigencia_semestral'] ?? '');
            $objetivosAprendizagem  = trim($_POST['objetivos_aprendizagem']    ?? '');
            $conteudosDidaticos     = trim($_POST['conteudos_didaticos']       ?? '');
            $estrategiasPedagogicas = trim($_POST['estrategias_pedagogicas']   ?? '');
            $estrategiasAvaliacao   = trim($_POST['estrategias_avaliacao']     ?? '');

            if (!$disciplinaNome || !$periodoVigencia || !$objetivosAprendizagem ||
                !$conteudosDidaticos || !$estrategiasPedagogicas || !$estrategiasAvaliacao) {
                $_SESSION['flash_error'] = 'Todos os campos obrigatórios devem ser preenchidos.';
                header('Location: index.php?action=preencher_adequacao&aluno_id=' . urlencode($alunoId));
                exit;
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO plano_pedagogico (
                    aluno_id, professor_id, disciplina_nome, etapa_ensino,
                    periodo_vigencia_semestral, objetivos_aprendizagem,
                    conteudos_didaticos, estrategias_pedagogicas, estrategias_avaliacao,
                    adequacao_espaco, adequacao_recursos, adequacao_tempo, adequacao_avaliativa
                ) VALUES (
                    :aluno_id, :professor_id, :disciplina_nome, :etapa_ensino,
                    :periodo_vigencia_semestral, :objetivos_aprendizagem,
                    :conteudos_didaticos, :estrategias_pedagogicas, :estrategias_avaliacao,
                    :adequacao_espaco, :adequacao_recursos, :adequacao_tempo, :adequacao_avaliativa
                )");

                $stmt->execute([
                    ':aluno_id'                  => $alunoId,
                    ':professor_id'              => $professorId,
                    ':disciplina_nome'           => $disciplinaNome,
                    ':etapa_ensino'              => trim($_POST['etapa_ensino'] ?? 'Ensino Profissionalizante'),
                    ':periodo_vigencia_semestral' => $periodoVigencia,
                    ':objetivos_aprendizagem'    => $objetivosAprendizagem,
                    ':conteudos_didaticos'       => $conteudosDidaticos,
                    ':estrategias_pedagogicas'   => $estrategiasPedagogicas,
                    ':estrategias_avaliacao'     => $estrategiasAvaliacao,
                    ':adequacao_espaco'          => trim($_POST['adequacao_espaco']     ?? ''),
                    ':adequacao_recursos'        => trim($_POST['adequacao_recursos']   ?? ''),
                    ':adequacao_tempo'           => trim($_POST['adequacao_tempo']      ?? ''),
                    ':adequacao_avaliativa'      => trim($_POST['adequacao_avaliativa'] ?? ''),
                ]);

                $_SESSION['flash_success'] = 'Plano Pedagógico salvo com sucesso!';
                header('Location: index.php?action=dashboard');
                exit;

            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Erro ao salvar plano pedagógico: ' . $e->getMessage();
                header('Location: index.php?action=preencher_adequacao&aluno_id=' . urlencode($alunoId));
                exit;
            }
        }
        header('Location: index.php?action=dashboard');
        exit;

    default:
        safe_include(BASE_PATH . 'VIEW/Auth/login.php');
        break;
}
