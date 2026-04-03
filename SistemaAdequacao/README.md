# Sistema Adequação (ETC)

Projeto PHP para gestão de inclusão escolar com plano AEE (acompanhamento de alunos com necessidades educacionais especiais).

## 🛠️ Stack

- PHP (puro / MVC bem leve)
- MySQL / MariaDB
- PDO
- Bootstrap / CSS
- Apache (XAMPP)

## 📁 Estrutura

- `index.php` - front controller de ações via `?action=`.
- `MODEL/DAO` - acesso a dados (AlunoDAO, UsuarioDAO)
- `MODEL/DTO` - modelos (AlunoDTO, UsuarioDTO)
- `VIEW/` - templates e páginas
- `CONTROL/` - controllers (AuthController)
- `ASSETS/` - imagens, css, uploads
- `banco.txt` - schema do banco
- `banco insert.txt` - dados iniciais

## 🚀 Instalação local

1. Colar pasta em `C:\xampp\htdocs\SistemaAdequacao`
2. Inicializar Apache + MySQL no XAMPP
3. Criar DB e importação:
   - `c:\xampp\mysql\bin\mysql -u root -e "CREATE DATABASE IF NOT EXISTS sistema_adequacao_etc;"`
   - `c:\xampp\mysql\bin\mysql -u root sistema_adequacao_etc < c:\xampp\htdocs\SistemaAdequacao\banco.txt`
   - `c:\xampp\mysql\bin\mysql -u root sistema_adequacao_etc < c:\xampp\htdocs\SistemaAdequacao\banco insert.txt`
4. Acessar: `http://localhost/SistemaAdequacao/index.php`

## 🔐 Acesso inicial

- admin@etc.edu.br (hash de senha já em banco)
- gestor@gmail.com
- aee@gmail.com
- professor@gmail.com
- apoio@etc.edu.br

## 📸 Fotos de alunos

- Caminho relativo salvo no DB: `ASSETS/uploads/fotos/<arquivo>.jpg`
- URL exata usada pelo app: `http://localhost/SistemaAdequacao/ASSETS/uploads/fotos/<arquivo>.jpg`

## 🐞 Correção de bug de foto

No `VIEW/Home/listar_alunos.php` e `VIEW/Home/perfil_ficha_perfil.php`, foi adicionada normalização de URL para garantir que o caminho da foto do aluno inclua `/SistemaAdequacao/`.

## 🧩 Rotas principais

- `index.php?action=listar_alunos`
- `index.php?action=editar_aluno&aluno_id=...`
- `index.php?action=preencher_informacoes_aluno&aluno_id=...`
- `index.php?action=perfil_pdf_turma&turma=...`

## 📌 Observações

- Ao subir no GitHub, mantenha os dados sensíveis (senhas reais) fora do repositório.
- Use `.gitignore` para `ASSETS/uploads/fotos` se não quiser subir imagens.
 - Exemplos:
```
ASSETS/uploads/fotos/*
!ASSETS/uploads/fotos/.gitkeep
```

## 🌟 Contribuição

1. Fork
2. Branch `feature/nome`
3. Commit + PR
4. Revisão

---

Feito para ETC e projeto de Adequação Escalar.