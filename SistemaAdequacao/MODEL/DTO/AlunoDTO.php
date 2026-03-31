<?php

class AlunoDTO {
    public ?int $id;
    public ?string $matricula;
    public ?string $email;
    public string $nome_completo;
    public ?string $foto;
    public ?string $data_nascimento;
    public ?int $idade;
    public ?string $modalidade_ano_turma_turno;
    public ?string $endereco;
    public ?string $telefones_responsaveis;
    public ?string $filiacao_mae;
    public ?string $filiacao_pai;
    public ?string $periodo_vigencia_adequacao;
    public ?string $diagnostico_detalhado;
    public ?string $cid_codigos;

    public function __construct(
        string $nome_completo,
        ?string $matricula = null,
        ?string $email = null,
        ?string $foto = null,
        ?string $data_nascimento = null,
        ?int $idade = null,
        ?string $modalidade_ano_turma_turno = null,
        ?string $endereco = null,
        ?string $telefones_responsaveis = null,
        ?string $filiacao_mae = null,
        ?string $filiacao_pai = null,
        ?string $periodo_vigencia_adequacao = null,
        ?string $diagnostico_detalhado = null,
        ?string $cid_codigos = null,
        ?int $id = null
    ) {
        $this->matricula = $matricula;
        $this->email = $email;
        $this->nome_completo = $nome_completo;
        $this->foto = $foto;
        $this->data_nascimento = $data_nascimento;
        $this->idade = $idade;
        $this->modalidade_ano_turma_turno = $modalidade_ano_turma_turno;
        $this->endereco = $endereco;
        $this->telefones_responsaveis = $telefones_responsaveis;
        $this->filiacao_mae = $filiacao_mae;
        $this->filiacao_pai = $filiacao_pai;
        $this->periodo_vigencia_adequacao = $periodo_vigencia_adequacao;
        $this->diagnostico_detalhado = $diagnostico_detalhado;
        $this->cid_codigos = $cid_codigos;
        $this->id = $id;
    }
}