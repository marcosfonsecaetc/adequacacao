<?php

class UsuarioDTO {
    public ?int $id;
    public string $email;
    public string $senha;
    public int $papel_id;
    public ?string $nome_completo;

    public function __construct(string $email, string $senha, int $papel_id, ?string $nome_completo = null, ?int $id = null) {
        $this->email = $email;
        $this->senha = $senha;
        $this->papel_id = $papel_id;
        $this->nome_completo = $nome_completo;
        $this->id = $id;
    }

    // Método getter seguro para acessar papel_id sem expor propriedades diretamente.
    public function getPapelId(): int
    {
        return $this->papel_id;
    }
}