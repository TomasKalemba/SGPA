<?php
// Simulação de opções de projetos — substitua por um loop dinâmico depois
$projetos = [
    ['id' => 25, 'titulo' => 'quarta', 'descricao' => 'teste', 'prazo' => '2025-07-12', 'docente' => 'Emanuel'],
    ['id' => 26, 'titulo' => 'Teste2', 'descricao' => 'testando inserir o nome dos estudantes na tabele de grupo', 'prazo' => '2025-07-12', 'docente' => 'Emanuel'],
    ['id' => 30, 'titulo' => 'Informatica', 'descricao' => 'Fala sobre a sua importancia', 'prazo' => '2025-07-11', 'docente' => 'Emanuel'],
    // ... adicione os demais
];
?>

<div class="container p-4 my-4 bg-white shadow rounded" style="max-width: 720px;">
    <h4 class="text-center mb-4"><i class="fas fa-upload text-primary"></i> Enviar Projeto</h4>

    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data">

        <!-- Projeto Atribuído -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-project-diagram"></i> Projeto Atribuído</label>
            <select class="form-select" name="projeto_id" id="projetoSelect" required>
                <option value="">Selecione</option>
                <?php foreach ($projetos as $proj): ?>
                    <option 
                        value="<?= $proj['id'] ?>"
                        data-descricao="<?= htmlspecialchars($proj['descricao']) ?>"
                        data-prazo="<?= $proj['prazo'] ?>"
                        data-docente="<?= htmlspecialchars($proj['docente']) ?>"
                    >
                        <?= $proj['titulo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nome do Docente -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-user"></i> Nome do Docente</label>
            <input type="text" class="form-control bg-light" name="docente_nome" id="docenteNome" readonly>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-align-left"></i> Descrição do Projeto</label>
            <textarea class="form-control bg-light" id="descricaoProjeto" name="descricao" rows="3" readonly></textarea>
        </div>

        <!-- Prazo -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-calendar-alt"></i> Prazo</label>
            <input type="date" class="form-control bg-light" id="prazoProjeto" name="data_submissao" readonly>
        </div>

        <!-- Arquivo -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-file-upload"></i> Arquivo do Projeto</label>
            <input type="file" class="form-control" name="arquivo" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-tasks"></i> Status do Projeto</label>
            <select class="form-select" name="estatus" required>
                <option value="emAndamento">Em andamento</option>
                <option value="concluido">Concluído</option>
                <option value="atrasado">Atrasado</option>
            </select>
        </div>

        <!-- Feedback -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-comments"></i> Comentários ou Observações</label>
            <textarea class="form-control" name="feedback" rows="3" placeholder="Opcional"></textarea>
        </div>

        <!-- Botão -->
        <div class="text-center">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-paper-plane"></i> Enviar Projeto
            </button>
        </div>

    </form>
</div>

<!-- Script para preenchimento automático -->
<script>
document.getElementById('projetoSelect').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    document.getElementById('descricaoProjeto').value = selected.getAttribute('data-descricao') || '';
    document.getElementById('prazoProjeto').value = selected.getAttribute('data-prazo') || '';
    document.getElementById('docenteNome').value = selected.getAttribute('data-docente') || '';
});
</script>
</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

