<?php
class UsuarioDAO {
    private PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function cadastrar(UsuarioDTO $usuario): bool {
        try {
            $this->db->beginTransaction();

            $sqlUser = "INSERT INTO usuario (email, senha_hash) VALUES (:email, :senha)";
            $stmt = $this->db->prepare($sqlUser);
            $senhaHash = password_hash($usuario->senha, PASSWORD_BCRYPT);
            
            $stmt->execute([
                ':email' => $usuario->email,
                ':senha' => $senhaHash
            ]);

            $usuarioId = $this->db->lastInsertId();

            $sqlPapel = "INSERT INTO usuario_papel (usuario_id, papel_id, data_inicio) 
                         VALUES (:uid, :pid, NOW())";
            $stmtPapel = $this->db->prepare($sqlPapel);
            $stmtPapel->execute([
                ':uid' => $usuarioId,
                ':pid' => $usuario->papel_id
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function buscarPorEmail(string $email) {
        $sql = "SELECT u.*, up.papel_id FROM usuario u 
                LEFT JOIN usuario_papel up ON u.id = up.usuario_id 
                WHERE u.email = :email AND u.ativo = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
}