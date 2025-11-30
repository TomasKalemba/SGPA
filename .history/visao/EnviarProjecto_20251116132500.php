<?php
session_start();

require_once '../modelo/crud.php';

// Verifica se o usuário está logado e autorizado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Estudante') {
    header("Location: login.php");
    exit;
}

// Buscar projetos atribuídos ao estudante
$projetos = [];
$estudanteId = $_SESSION['id'];

$crud = new crud();
$conn = $crud->getConexao();

$sql = "
    SELECT p.Id, p.titulo, p.descricao, p.prazo
    FROM projectos p
    INNER JOIN grupo g ON g.projeto_id = p.Id
    INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
    WHERE ge.estudante_id = :estudante_id
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':estudante_id', $estudanteId);
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Valores antigos do formulário (em caso de erro)
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Enviar Projeto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        .container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin-top: 50px;
        }

        /* Spinner overlay */
        #overlayProcessando {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.75);
            z-index: 1050;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            user-select: none;
        }

        #overlayProcessando .spinner-border {
            width: 5rem;
            height: 5rem;
            border-width: 0.5rem;
            margin-bottom: 20px;
            border-top-color: #28a745;
            border-right-color: transparent;
            border-bottom-color: #28a745;
            border-left-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center m-4" role="alert">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="container">
    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data" id="formEnviarProjeto">
        
        <!-- Projeto Atribuído -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-project-diagram"></i> Projeto Atribuído</label>
            <select class="form-select" name="projeto_id" id="projetoSelect" required>
                <option value="">Selecione</option>
                <?php foreach ($projetos as $proj): ?>
                    <option 
                        value="<?= htmlspecialchars($proj['Id']) ?>"
                        data-descricao="<?= htmlspecialchars($proj['descricao']) ?>"
                        data-prazo="<?= htmlspecialchars($proj['prazo']) ?>"
                        <?= (isset($old['projeto_id']) && $old['projeto_id'] == $proj['Id']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($proj['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-pencil-alt"></i> Descrição</label>
            <textarea class="form-control" id="descricaoProjeto" name="descricao" rows="3" readonly required><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
        </div>

        <!-- Prazo -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-calendar-alt"></i> Prazo do Projeto</label>
            <input type="date" class="form-control" id="prazoProjeto" name="data_submissao" 
                value="<?= htmlspecialchars($old['data_submissao'] ?? '') ?>" readonly required>
        </div>

        <!-- Arquivo -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-file-upload"></i> Arquivo</label>
            <input type="file" class="form-control" name="arquivo" required>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-tasks"></i> Status</label>
            <select class="form-select" name="estatus" required>
                <option value="emAndamento" <?= ($old['estatus'] ?? '') == 'emAndamento' ? 'selected' : '' ?>>Em andamento</option>
                <option value="concluido" <?= ($old['estatus'] ?? '') == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                <option value="atrasado" <?= ($old['estatus'] ?? '') == 'atrasado' ? 'selected' : '' ?>>Atrasado</option>
            </select>
        </div>

        <!-- Feedback -->
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-comments"></i> Comentários</label>
            <textarea class="form-control" name="feedback" rows="3"><?= htmlspecialchars($old['feedback'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane"></i> Enviar Projeto
        </button>
    </form>
</div>

<!-- Overlay processando -->
<div id="overlayProcessando">
  <div class="spinner-border" role="status"></div>
  <div>Processando envio do projeto, por favor aguarde...</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Atualiza descrição e prazo ao escolher projeto
    document.getElementById('projetoSelect').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('descricaoProjeto').value = selectedOption.dataset.descricao || '';
        document.getElementById('prazoProjeto').value = selectedOption.dataset.prazo || '';
    });

    // Mostra overlay ao enviar o formulário
    document.getElementById('formEnviarProjeto').addEventListener('submit', function () {
        document.getElementById('overlayProcessando').style.display = 'flex';
    });

    // Esconde overlay ao carregar a página
    window.addEventListener('load', () => {
        document.getElementById('overlayProcessando').style.display = 'none';
    });
</script>

</body>
</html>
