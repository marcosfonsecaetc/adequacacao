<?php
$title = 'Incluir Informações do Aluno - ETC';
$brand = 'ETC - Sala de Recursos';
$navbarClass = 'navbar-dark bg-primary';
include __DIR__ . '/../Layout/topo.php';

// Garante que a conexão PDO seja acessível
global $pdo;

// Carrega todos os alunos usando AlunoDAO
$alunoDAO = new AlunoDAO($pdo);
$alunos = $alunoDAO->listarTodos();
?>

    <h3>Incluir Informações Biopsicossociais do Aluno</h3>
    <p>Selecione um aluno para incluir ou atualizar suas informações biopsicossociais e de escolarização.</p>

    <?php if (empty($alunos)): ?>
        <div class="alert alert-warning">Nenhum aluno cadastrado. Cadastre um aluno antes de incluir informações.</div>
        <a href="index.php?action=cadastrar_aluno" class="btn btn-primary">+ Cadastrar Aluno</a>
    <?php else: ?>
        <!-- Formulário para seleção de aluno com Bootstrap -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Selecionar Aluno</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=processar_incluir_informacoes">
                    <!-- Campo de busca para filtrar alunos -->
                    <div class="mb-3">
                        <label for="busca_aluno" class="form-label">Buscar Aluno (Nome ou Matrícula):</label>
                        <!-- Bootstrap input com ícone de busca -->
                        <input type="text" id="busca_aluno" class="form-control"
                            placeholder="Digite o nome ou matrícula do aluno..."
                            onkeyup="filtrarAlunos()">
                    </div>

                    <!-- Combo dropdown para seleção de alunos -->
                    <div class="mb-3">
                        <label for="select_aluno" class="form-label">Selecionar Aluno:</label>
                        <select id="select_aluno" name="aluno_id" class="form-select" required>
                            <option value="">-- Escolha um aluno --</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?php echo htmlspecialchars($aluno['id']); ?>"
                                        data-nome="<?php echo htmlspecialchars(strtolower($aluno['nome_completo'])); ?>"
                                        data-matricula="<?php echo htmlspecialchars(strtolower($aluno['matricula'] ?? '')); ?>"
                                        data-email="<?php echo htmlspecialchars(strtolower($aluno['email'] ?? '')); ?>">
                                    <?php echo htmlspecialchars($aluno['nome_completo']); ?> -
                                    Matrícula: <?php echo htmlspecialchars($aluno['matricula'] ?? 'N/A'); ?> -
                                    Email: <?php echo htmlspecialchars($aluno['email'] ?? 'N/A'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Lista de alunos em cards Bootstrap (mantida para compatibilidade) -->
                    <div id="lista_alunos" style="display: none;">
                        <?php foreach ($alunos as $aluno): ?>
                            <div class="aluno-card mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="aluno_id_radio"
                                        id="aluno_<?php echo htmlspecialchars($aluno['id']); ?>"
                                        value="<?php echo htmlspecialchars($aluno['id']); ?>">
                                    <label class="form-check-label" for="aluno_<?php echo htmlspecialchars($aluno['id']); ?>">
                                        <strong><?php echo htmlspecialchars($aluno['nome_completo']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            Matrícula: <?php echo htmlspecialchars($aluno['matricula'] ?? 'N/A'); ?> |
                                            Email: <?php echo htmlspecialchars($aluno['email'] ?? 'N/A'); ?>
                                        </small>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Botões de ação -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="index.php?action=dashboard" class="btn btn-secondary">← Voltar</a>
                        <div class="d-flex gap-2">
                            <a href="index.php?action=cadastrar_aluno" class="btn btn-primary">+ Incluir Aluno</a>
                            <button type="submit" class="btn btn-success" id="btn_continuar" disabled>
                                Continuar →
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- JavaScript para filtrar alunos no combo e habilitar botão -->
    <script>
        function filtrarAlunos() {
            const busca = document.getElementById('busca_aluno').value.toLowerCase();
            const select = document.getElementById('select_aluno');
            const options = select.querySelectorAll('option:not([value=""])'); // Exclui a opção vazia
            let encontrou = false;

            // Primeiro, mostra todas as opções
            options.forEach(option => {
                option.style.display = 'block';
            });

            // Se há busca, filtra as opções
            if (busca.length > 0) {
                options.forEach(option => {
                    const nome = option.getAttribute('data-nome') || '';
                    const matricula = option.getAttribute('data-matricula') || '';
                    const email = option.getAttribute('data-email') || '';

                    if (nome.includes(busca) || matricula.includes(busca) || email.includes(busca)) {
                        option.style.display = 'block';
                        encontrou = true;
                    } else {
                        option.style.display = 'none';
                    }
                });

                // Se nenhum resultado encontrado, mostra mensagem
                if (!encontrou) {
                    // Cria uma opção temporária para mostrar que não encontrou
                    let optionExistente = select.querySelector('option[value="no-results"]');
                    if (!optionExistente) {
                        optionExistente = document.createElement('option');
                        optionExistente.value = 'no-results';
                        optionExistente.textContent = 'Nenhum aluno encontrado';
                        optionExistente.disabled = true;
                        select.appendChild(optionExistente);
                    }
                    optionExistente.style.display = 'block';
                } else {
                    // Remove a opção de "não encontrado" se existir
                    const optionNoResults = select.querySelector('option[value="no-results"]');
                    if (optionNoResults) {
                        optionNoResults.remove();
                    }
                }
            } else {
                // Remove a opção de "não encontrado" quando não há busca
                const optionNoResults = select.querySelector('option[value="no-results"]');
                if (optionNoResults) {
                    optionNoResults.remove();
                }
            }

            // Se a opção selecionada foi ocultada, desmarca
            const selectedOption = select.querySelector('option[value="' + select.value + '"]');
            if (selectedOption && selectedOption.style.display === 'none' && select.value !== '') {
                select.value = '';
                document.getElementById('btn_continuar').disabled = true;
            }
        }

        // Habilita/desabilita botão continuar baseado na seleção do combo
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('select_aluno');
            const btnContinuar = document.getElementById('btn_continuar');

            select.addEventListener('change', function() {
                btnContinuar.disabled = (this.value === '');
            });

            // Também escuta mudanças no campo de busca para atualizar o botão
            document.getElementById('busca_aluno').addEventListener('input', function() {
                // Se a busca mudou e a opção selecionada foi filtrada, desabilita o botão
                const selectedOption = select.querySelector('option[value="' + select.value + '"]');
                if (selectedOption && selectedOption.style.display === 'none') {
                    select.value = '';
                    btnContinuar.disabled = true;
                }
            });
        });
    </script>

<?php include __DIR__ . '/../Layout/rodape.php'; ?>