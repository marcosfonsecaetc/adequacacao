<?php
class UsuarioDAO {
    private PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function cadastrar(UsuarioDTO $usuario): bool {
        try {
            $this->db->beginTransaction();

            $sqlUser = "INSERT INTO usuario (email, nome_completo, matricula_servidor, senha_hash) VALUES (:email, :nome, :mat, :senha)";
            $stmt = $this->db->prepare($sqlUser);
            $senhaHash = password_hash($usuario->senha, PASSWORD_BCRYPT);
            
            $stmt->execute([
                ':email' => $usuario->email,
                ':nome'  => $usuario->nome_completo ?? '',
                ':mat'   => $usuario->matricula_servidor ?? '',
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

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT u.*, up.papel_id FROM usuario u
             LEFT JOIN usuario_papel up ON u.id = up.usuario_id
             WHERE u.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function atualizar(int $id, string $email, int $papelId, ?string $novaSenha, string $nomeCompleto = '', string $matricula = ''): bool {
        try {
            $this->db->beginTransaction();
            if ($novaSenha) {
                $stmt = $this->db->prepare("UPDATE usuario SET email = :email, nome_completo = :nome, matricula_servidor = :mat, senha_hash = :senha WHERE id = :id");
                $stmt->execute([':email' => $email, ':nome' => $nomeCompleto, ':mat' => $matricula, ':senha' => password_hash($novaSenha, PASSWORD_BCRYPT), ':id' => $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE usuario SET email = :email, nome_completo = :nome, matricula_servidor = :mat WHERE id = :id");
                $stmt->execute([':email' => $email, ':nome' => $nomeCompleto, ':mat' => $matricula, ':id' => $id]);
            }
            $stmt2 = $this->db->prepare("UPDATE usuario_papel SET papel_id = :pid WHERE usuario_id = :uid");
            $stmt2->execute([':pid' => $papelId, ':uid' => $id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function toggleAtivo(int $id): bool {
        $stmt = $this->db->prepare("UPDATE usuario SET ativo = NOT ativo WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}