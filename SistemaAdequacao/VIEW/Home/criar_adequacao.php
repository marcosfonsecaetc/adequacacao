<?php
$title = 'Criar Adequação - ETC';

// Define brand e cor da navbar baseado no papel do usuário logado
$papelId = $_SESSION['usuario_papel'] ?? null;
if ($papelId == 4) {
    $brand = 'ETC - Professor Regente';
    $navbarClass = 'navbar-dark bg-dark'; // Professor
} elseif ($papelId == 3) {
    $brand = 'ETC - Sala de Recursos';
    $navbarClass = 'navbar-dark bg-primary'; // AEE / Sala de Recursos
} elseif ($papelId == 2) {
    $brand = 'ETC - Gestão Escolar';
    $navbarClass = 'navbar-dark bg-success'; // Gestor
} else {
    $brand = 'ETC - Sistema';
    $navbarClass = 'navbar-dark bg-secondary'; // Outro
}

include __DIR__ . '/../Layout/topo.php';

// Garante que a conexão PDO seja acessível
global $pdo;

// Carrega todos os alunos usando AlunoDAO
$alunoDAO = new AlunoDAO($pdo);
$alunos = $alunoDAO->listarTodos();
?>

    <h3>Criar Nova Adequação Curricular</h3>
    <p>Selecione um aluno para iniciar o preenchimento da ficha de adequação.</p>

    <?php if (empty($alunos)): ?>
        <div class="alert alert-warning">Nenhum aluno cadastrado. Cadastre um aluno antes de criar uma adequação.</div>
        <a href="index.php?action=cadastrar_aluno" class="btn btn-primary">+ Cadastrar Aluno</a>
    <?php else: ?>
        <!-- Formulário para seleção de aluno com Bootstrap -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dados do Aluno</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=processar_criar_adequacao">
                    <!-- Campo de busca para filtrar alunos -->
                    <div class="mb-3">
                        <label for="busca_aluno" class="form-label">Buscar Aluno (Nome ou Matrícula):</label>
                        <!-- Bootstrap input com ícone de busca -->
                        <input type="text" id="busca_aluno" class="form-control" 
                            placeholder="Digite o nome ou matrícula do aluno..." 
                            onkeyup="filtrarAlunos()">
                    </div>

                    <div class="mb-3">
                        <label for="aluno_id" class="form-label">Selecione o Aluno:</label>
                        <!-- Bootstrap select -->
                        <select id="aluno_id" name="aluno_id" class="form-select" required onchange="carregarDadosAluno()">
                            <option value="">-- Escolha um aluno --</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?php echo htmlspecialchars($aluno['id']); ?>" 
                                    data-nome="<?php echo htmlspecialchars($aluno['nome_completo']); ?>"
                                    data-data_nascimento="<?php echo htmlspecialchars($aluno['data_nascimento'] ?? ''); ?>"
                                    data-idade="<?php echo htmlspecialchars($aluno['idade'] ?? ''); ?>"
                                    data-modalidade="<?php echo htmlspecialchars($aluno['modalidade_ano_turma_turno'] ?? ''); ?>"
                                    data-endereco="<?php echo htmlspecialchars($aluno['endereco'] ?? ''); ?>"
                                    data-telefones="<?php echo htmlspecialchars($aluno['telefones_responsaveis'] ?? ''); ?>"
                                    data-mae="<?php echo htmlspecialchars($aluno['filiacao_mae'] ?? ''); ?>"
                                    data-pai="<?php echo htmlspecialchars($aluno['filiacao_pai'] ?? ''); ?>"
                                    data-diagnostico="<?php echo htmlspecialchars($aluno['diagnostico_detalhado'] ?? ''); ?>"
                                    data-cid="<?php echo htmlspecialchars($aluno['cid_codigos'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($aluno['nome_completo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Área para exibição dos dados do aluno selecionado -->
                    <div id="dados_aluno" style="display:none;">
                        <hr>
                        <h5>Resumo do Aluno</h5>
                        <!-- Bootstrap dl (description list) para exibir dados -->
                        <dl class="row">
                            <dt class="col-sm-4">Nome:</dt>
                            <dd class="col-sm-8" id="info-nome">-</dd>
                            
                            <dt class="col-sm-4">Data de Nascimento:</dt>
                            <dd class="col-sm-8" id="info-data">-</dd>
                            
                            <dt class="col-sm-4">Idade:</dt>
                            <dd class="col-sm-8" id="info-idade">-</dd>
                            
                            <dt class="col-sm-4">Modalidade/Turma:</dt>
                            <dd class="col-sm-8" id="info-modalidade">-</dd>
                            
                            <dt class="col-sm-4">Endereço:</dt>
                            <dd class="col-sm-8" id="info-endereco">-</dd>
                            
                            <dt class="col-sm-4">Telefones:</dt>
                            <dd class="col-sm-8" id="info-telefones">-</dd>
                            
                            <dt class="col-sm-4">Filiação Mãe:</dt>
                            <dd class="col-sm-8" id="info-mae">-</dd>
                            
                            <dt class="col-sm-4">Filiação Pai:</dt>
                            <dd class="col-sm-8" id="info-pai">-</dd>
                            
                            <dt class="col-sm-4">Diagnóstico:</dt>
                            <dd class="col-sm-8" id="info-diagnostico">-</dd>
                            
                            <dt class="col-sm-4">CID:</dt>
                            <dd class="col-sm-8" id="info-cid">-</dd>
                        </dl>
                    </div>

                    <!-- Botão Bootstrap para continuar -->
                    <div class="mt-4" id="btn_continuar" style="display:none;">
                        <button type="submit" class="btn btn-success btn-lg">
                            Continuar com Adequação
                        </button>
                        <a href="index.php?action=dashboard" class="btn btn-secondary btn-lg">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- JavaScript para carregar dados do aluno dinamicamente -->
    <script>
        // Armazena a lista original de opções para restaurar depois da busca
        let opcoesAlunos = [];

        // Inicializa a lista de alunos ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('aluno_id');
            opcoesAlunos = Array.from(select.options).map(opt => ({
                value: opt.value,
                text: opt.text,
                nome: opt.dataset.nome,
                data_nascimento: opt.dataset.data_nascimento,
                idade: opt.dataset.idade,
                modalidade: opt.dataset.modalidade,
                endereco: opt.dataset.endereco,
                telefones: opt.dataset.telefones,
                mae: opt.dataset.mae,
                pai: opt.dataset.pai,
                diagnostico: opt.dataset.diagnostico,
                cid: opt.dataset.cid
            }));
        });

        // Função para filtrar alunos conforme digita
        function filtrarAlunos() {
            const busca = document.getElementById('busca_aluno').value.toLowerCase();
            const select = document.getElementById('aluno_id');
            
            // Limpa o select mantendo apenas a primeira opção vazia
            select.innerHTML = '<option value="">-- Escolha um aluno --</option>';
            
            if (busca === '') {
                // Se está vazio, mostra todos
                opcoesAlunos.forEach(opcao => {
                    if (opcao.value !== '') {
                        const option = document.createElement('option');
                        option.value = opcao.value;
                        option.text = opcao.text;
                        option.dataset.nome = opcao.nome;
                        option.dataset.data_nascimento = opcao.data_nascimento;
                        option.dataset.idade = opcao.idade;
                        option.dataset.modalidade = opcao.modalidade;
                        option.dataset.endereco = opcao.endereco;
                        option.dataset.telefones = opcao.telefones;
                        option.dataset.mae = opcao.mae;
                        option.dataset.pai = opcao.pai;
                        option.dataset.diagnostico = opcao.diagnostico;
                        option.dataset.cid = opcao.cid;
                        select.appendChild(option);
                    }
                });
            } else {
                // Filtra por nome ou matrícula
                const resultado = opcoesAlunos.filter(opcao => {
                    const nome = (opcao.nome || '').toLowerCase();
                    return opcao.value !== '' && (nome.includes(busca));
                });
                
                resultado.forEach(opcao => {
                    const option = document.createElement('option');
                    option.value = opcao.value;
                    option.text = opcao.text;
                    option.dataset.nome = opcao.nome;
                    option.dataset.data_nascimento = opcao.data_nascimento;
                    option.dataset.idade = opcao.idade;
                    option.dataset.modalidade = opcao.modalidade;
                    option.dataset.endereco = opcao.endereco;
                    option.dataset.telefones = opcao.telefones;
                    option.dataset.mae = opcao.mae;
                    option.dataset.pai = opcao.pai;
                    option.dataset.diagnostico = opcao.diagnostico;
                    option.dataset.cid = opcao.cid;
                    select.appendChild(option);
                });
                
                // Se houver apenas um resultado, seleciona automaticamente
                if (resultado.length === 1) {
                    select.value = resultado[0].value;
                    carregarDadosAluno();
                }
            }
        }

        function carregarDadosAluno() {
            var select = document.getElementById('aluno_id');
            var option = select.options[select.selectedIndex];
            
            if (option.value === '') {
                document.getElementById('dados_aluno').style.display = 'none';
                document.getElementById('btn_continuar').style.display = 'none';
                return;
            }
            
            // Extrai os dados dos atributos data-*
            var dados = {
                nome: option.dataset.nome,
                data_nascimento: option.dataset.data_nascimento,
                idade: option.dataset.idade,
                modalidade: option.dataset.modalidade,
                endereco: option.dataset.endereco,
                telefones: option.dataset.telefones,
                mae: option.dataset.mae,
                pai: option.dataset.pai,
                diagnostico: option.dataset.diagnostico,
                cid: option.dataset.cid
            };
            
            // Atualiza os campos de exibição
            document.getElementById('info-nome').textContent = dados.nome || '-';
            document.getElementById('info-data').textContent = dados.data_nascimento || '-';
            document.getElementById('info-idade').textContent = dados.idade || '-';
            document.getElementById('info-modalidade').textContent = dados.modalidade || '-';
            document.getElementById('info-endereco').textContent = dados.endereco || '-';
            document.getElementById('info-telefones').textContent = dados.telefones || '-';
            document.getElementById('info-mae').textContent = dados.mae || '-';
            document.getElementById('info-pai').textContent = dados.pai || '-';
            document.getElementById('info-diagnostico').textContent = dados.diagnostico || '-';
            document.getElementById('info-cid').textContent = dados.cid || '-';
            
            // Mostra os dados e o botão
            document.getElementById('dados_aluno').style.display = 'block';
            document.getElementById('btn_continuar').style.display = 'block';
        }
    </script>

    <a href="index.php?action=dashboard" class="btn btn-secondary mt-4">Voltar ao Dashboard</a>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>
