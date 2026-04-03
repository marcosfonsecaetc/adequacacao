# Verificação do Banco de Dados vs Sistema

**Data:** 3 de Abril de 2026  
**Status:** ⚠️ **CORRIGIDO COM 1 PROBLEMA IDENTIFICADO**

---

## 📋 Resultado da Verificação

### ✅ Tabelas Encontradas (Sincronizadas)
1. **usuario** - Gerenciamento de usuários (email, senha_hash, dados RBAC)
2. **papel** - Papéis do sistema (AEE, Professor, Gestor, etc.)
3. **permissao** - Permissões e recursos
4. **usuario_papel** - Relacionamento usuário-papel (RBAC)
5. **papel_permissao** - Relacionamento papel-permissão
6. **aluno** - Dados básicos do estudante
7. **saude_escolarizacao** - Histórico de saúde e escolarização
8. **habilidade_biopsicossocial** - Avaliação de habilidades (AEE)
9. **adequacao_organizativa** - Adequações gerais da escola (AEE)
10. **plano_pedagogico** - Plano pedagógico por disciplina (Professor)
11. **encaminhamento_final** - Encaminhamentos finais
12. **paee** - ✅ **ADICIONADA** (Plano de Atendimento Educacional Especializado)

---

## 🔴 Problemas Identificados

### Problema 1: Tabela PAEE Faltando (**CORRIGIDO**)
**Severidade:** CRÍTICA  
**Descrição:** O sistema usa a tabela `paee` para salvar e consultar dados do PAEE, mas ela não estava definida.

**Onde era usado:**
- `index.php` linha 147-175: `case 'processar_paee'` - Inserção/atualização via formulário
- `index.php` linha 180-185: `case 'paee_pdf'` - Leitura para gerar PDF

**Solução Aplicada:** Adicionada tabela `paee` com 27 campos conforme uso no código.

---

## ⚠️ Inconsistências Menores

### Campo `nome_completo` em UsuarioDTO
**Descrição:** O DTO de usuário possui o campo `nome_completo`, mas a tabela `usuario` não tem coluna equivalente para armazenar nomes.

**Impacto:** Baixo - O campo é aceito no formulário mas não é persistido no banco.

**Recomendação:** 
```sql
-- Se necessário armazenar nome de usuário, adicione a coluna:
ALTER TABLE usuario ADD COLUMN nome_completo VARCHAR(150);
```

---

## 📊 Estrutura Confirmada

### Fluxo de Dados Validado
```
Estudante
  ├─ aluno (dados básicos)
  ├─ saude_escolarizacao (histórico)
  ├─ habilidade_biopsicossocial (avaliação AEE)
  ├─ adequacao_organizativa (adequações gerais)
  ├─ plano_pedagogico (por disciplina - professor)
  ├─ paee (plano especializado - AEE) ✅
  └─ encaminhamento_final (observações finais)

Usuários
  ├─ usuario (autenticação)
  ├─ papel (AEE, Professor, Gestor, etc.)
  ├─ usuario_papel (atribuição de papéis)
  ├─ permissao (controle de acesso)
  └─ papel_permissao (relacionamento)
```

---

## ✨ Próximos Passos

1. ✅ **Executar o banco.txt atualizado** no MySQL/MariaDB
2. ⚠️ **(Opcional)** Adicionar coluna `nome_completo` à tabela `usuario` se necessário
3. 🧪 Testar funcionalidade PAEE no sistema:
   - Acessar formulário PAEE
   - Salvar dados
   - Gerar PDF
   - Verificar preenchimento

---

## 🔧 Como Aplicar a Correção

Execute este comando no seu banco MySQL:

```sql
DROP DATABASE IF EXISTS sistema_adequacao_etc;
-- Execute o arquivo banco.txt completo
SOURCE c:\xampp\htdocs\SistemaAdequacao\banco.txt;
```

Ou importe o arquivo `banco.txt` via phpMyAdmin.

---

**Status Final:** ✅ Banco atualizado e validado contra o codebase
