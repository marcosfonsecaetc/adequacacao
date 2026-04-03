<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário - ETC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="ASSETS/CSS/style.css">
</head>
<body class="bg-light" style="display:flex;align-items:center;justify-content:center;min-height:100vh;">

<div class="login-container">
    <?php if (isset($_GET['success'])):
        $successType = $_GET['success'];
        if ($successType === 'professor') {
            $message = 'Cadastro de professor realizado com sucesso!';
        } else {
            $message = 'Cadastro realizado com sucesso!';
        }
    ?>
        <div id="alerta-sucesso" class="alert alert-success border-0 shadow-sm text-center mb-4">
            <strong>Sucesso!</strong> <br>
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <script>
            // Após 3000ms (3 segundos), redireciona para o login
            setTimeout(function() {
                window.location.href = "index.php?action=login";
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white text-center py-3">
            <h4 class="mb-0">Solicitar Acesso</h4>
            <small>Sistema de Adequação Curricular</small>
        </div>
        <div class="card-body p-4">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'campos_vazios':
                            echo 'Por favor, preencha todos os campos.';
                            break;
                        case 'senha_diferente':
                            echo 'As senhas não coincidem.';
                            break;
                        case 'falha':
                            echo 'Ocorreu um erro interno ao cadastrar. Tente novamente.';
                            break;
                        default:
                            echo 'Erro desconhecido.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <form action="index.php?action=processar_cadastro" method="POST">
                
                <div class="mb-3">
                    <label for="nome_completo" class="form-label">Nome Completo</label>
                    <input type="text" name="nome_completo" id="nome_completo" class="form-control" placeholder="Nome completo" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail Institucional</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@etc.edu.br" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" name="confirm_senha" id="confirm_senha" class="form-control" placeholder="Confirme a senha" required>
                </div>

                <div class="mb-3">
                    <label for="papel_id" class="form-label">Perfil de Acesso</label>
                    <select name="papel_id" id="papel_id" class="form-select" required>
                        <option value="" selected disabled>Selecione seu cargo...</option>
                        <option value="1">Admin</option>
                        <option value="2">Gestão Escolar</option>
                        <option value="3">Sala de Recursos (AEE)</option>
                        <option value="4">Professor Regente</option>
                        <option value="5">Apoio</option>
                    </select>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success py-2">Finalizar Cadastro</button>
                    <a href="index.php?action=login" class="btn btn-outline-secondary py-2">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>