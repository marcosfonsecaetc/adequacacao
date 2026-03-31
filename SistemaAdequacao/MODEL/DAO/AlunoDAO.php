<?php

class AlunoDAO {
    private PDO $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function cadastrar(AlunoDTO $aluno): bool {
        try {
            $sql = "INSERT INTO aluno (
                        matricula,
                        email,
                        nome_completo,
                        foto,
                        data_nascimento,
                        idade,
                        modalidade_ano_turma_turno,
                        endereco,
                        telefones_responsaveis,
                        filiacao_mae,
                        filiacao_pai,
                        periodo_vigencia_adequacao,
                        diagnostico_detalhado,
                        cid_codigos
                    ) VALUES (
                        :matricula,
                        :email,
                        :nome_completo,
                        :foto,
                        :data_nascimento,
                        :idade,
                        :modalidade_ano_turma_turno,
                        :endereco,
                        :telefones_responsaveis,
                        :filiacao_mae,
                        :filiacao_pai,
                        :periodo_vigencia_adequacao,
                        :diagnostico_detalhado,
                        :cid_codigos
                    )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':matricula' => $aluno->matricula,
                ':email' => $aluno->email,
                ':nome_completo' => $aluno->nome_completo,
                ':foto' => $aluno->foto,
                ':data_nascimento' => $aluno->data_nascimento,
                ':idade' => $aluno->idade,
                ':modalidade_ano_turma_turno' => $aluno->modalidade_ano_turma_turno,
                ':endereco' => $aluno->endereco,
                ':telefones_responsaveis' => $aluno->telefones_responsaveis,
                ':filiacao_mae' => $aluno->filiacao_mae,
                ':filiacao_pai' => $aluno->filiacao_pai,
                ':periodo_vigencia_adequacao' => $aluno->periodo_vigencia_adequacao,
                ':diagnostico_detalhado' => $aluno->diagnostico_detalhado,
                ':cid_codigos' => $aluno->cid_codigos,
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function atualizar(int $id, array $dados, ?string $fotoPath): bool {
        $fotoSql = $fotoPath !== null ? ', foto = :foto' : '';
        $stmt = $this->db->prepare("UPDATE aluno SET
            nome_completo = :nome_completo, matricula = :matricula, email = :email,
            data_nascimento = :data_nascimento, idade = :idade,
            modalidade_ano_turma_turno = :modalidade_ano_turma_turno,
            endereco = :endereco, telefones_responsaveis = :telefones_responsaveis,
            filiacao_mae = :filiacao_mae, filiacao_pai = :filiacao_pai,
            periodo_vigencia_adequacao = :periodo_vigencia_adequacao,
            diagnostico_detalhado = :diagnostico_detalhado, cid_codigos = :cid_codigos
            $fotoSql
            WHERE id = :id");
        $params = [
            ':id'                          => $id,
            ':nome_completo'               => $dados['nome'],
            ':matricula'                   => $dados['matricula'],
            ':email'                       => $dados['email'],
            ':data_nascimento'             => $dados['data_nascimento'] ?: null,
            ':idade'                       => is_numeric($dados['idade']) ? (int)$dados['idade'] : null,
            ':modalidade_ano_turma_turno'  => $dados['modalidade_ano_turma_turno'],
            ':endereco'                    => $dados['endereco'],
            ':telefones_responsaveis'      => $dados['telefones_responsaveis'],
            ':filiacao_mae'                => $dados['filiacao_mae'],
            ':filiacao_pai'                => $dados['filiacao_pai'],
            ':periodo_vigencia_adequacao'  => $dados['periodo_vigencia_adequacao'],
            ':diagnostico_detalhado'       => $dados['diagnostico_detalhado'],
            ':cid_codigos'                 => $dados['cid_codigos'],
        ];
        if ($fotoPath !== null) $params[':foto'] = $fotoPath;
        return $stmt->execute($params);
    }

    public function listarTodos(): array {
        $sql = "SELECT * FROM aluno ORDER BY nome_completo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM aluno WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarDadosAEE(int $id): array {
        $saude = $this->db->prepare("SELECT * FROM saude_escolarizacao WHERE aluno_id = :id");
        $saude->execute([':id' => $id]);

        $habilidade = $this->db->prepare("SELECT * FROM habilidade_biopsicossocial WHERE aluno_id = :id");
        $habilidade->execute([':id' => $id]);

        $adequacao = $this->db->prepare("SELECT * FROM adequacao_organizativa WHERE aluno_id = :id");
        $adequacao->execute([':id' => $id]);

        return [
            'saude'      => $saude->fetch(PDO::FETCH_ASSOC) ?: [],
            'habilidade' => $habilidade->fetch(PDO::FETCH_ASSOC) ?: [],
            'adequacao'  => $adequacao->fetch(PDO::FETCH_ASSOC) ?: [],
        ];
    }
}