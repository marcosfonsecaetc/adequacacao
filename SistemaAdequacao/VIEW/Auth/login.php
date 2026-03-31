<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Adequação ETC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="ASSETS/CSS/style.css">
</head>
<body class="login-page">

<div class="login-container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center py-4">
            <h4 class="mb-0">Acesso ao Sistema</h4>
            <small>Escola Técnica de Ceilândia</small>
        </div>
        <div class="card-body p-4">
            <form action="index.php?action=logar" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail Institucional</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="usuario@etc.edu.br">
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" required placeholder="Digite sua senha">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Entrar</button>
            </form>
            
            <hr>
            
            <div class="text-center">
                <p class="mb-0 text-muted">Ainda não tem acesso?</p>
                <a href="index.php?action=cadastrar" class="text-decoration-none fw-bold">Solicitar Cadastro</a>
            </div>
        </div>
    </div>
    <div class="text-center mt-4 text-muted" style="font-size: 0.8rem;">
        &copy; 2026 ETC - Gestão de Adequações Curriculares
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>