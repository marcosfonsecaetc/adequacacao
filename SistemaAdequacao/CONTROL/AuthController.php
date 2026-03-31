<?php
class AuthController {
    private UsuarioDAO $usuarioDAO;

    public function __construct($pdo) {
        $this->usuarioDAO = new UsuarioDAO($pdo);
    }

    public function processarCadastro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeCompleto = trim($_POST['nome_completo'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $senha = $_POST['senha'] ?? '';
            $confirmSenha = $_POST['confirm_senha'] ?? '';
            $papel_id = filter_input(INPUT_POST, 'papel_id', FILTER_SANITIZE_NUMBER_INT);

            if (!$nomeCompleto || !$email || !$senha || !$confirmSenha || !$papel_id) {
                header('Location: index.php?action=cadastrar&error=campos_vazios');
                exit;
            }

            if ($senha !== $confirmSenha) {
                header('Location: index.php?action=cadastrar&error=senha_diferente');
                exit;
            }

            $dto = new UsuarioDTO($email, $senha, (int)$papel_id, $nomeCompleto);
            
            if ($this->usuarioDAO->cadastrar($dto)) {
                $successParam = ($dto->getPapelId() === 4) ? 'professor' : 'usuario';
                header("Location: index.php?action=cadastrar&success={$successParam}");
            } else {
                header("Location: index.php?action=cadastrar&error=falha");
            }
            exit;
        }
    }

    public function logar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $senha = $_POST['senha'] ?? '';

            $user = $this->usuarioDAO->buscarPorEmail($email);

            if ($user && password_verify($senha, $user['senha_hash'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_email'] = $user['email'];
                $_SESSION['usuario_papel'] = $user['papel_id'];
                
                header("Location: index.php?action=dashboard");
            } else {
                header("Location: index.php?action=login&error=invalid");
            }
            exit;
        }
    }
}