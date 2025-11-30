<?php 
ob_start();
session_start();

require_once '../modelo/crud.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

// Inclui o cabeçalho correto
if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
} elseif ($_SESSION['tipo'] === 'Estudante') {
    include_once('head/Estudante.php');
} else {
    header("Location: login.php");
    exit;
}

// Valores antigos do formulário (se houve erro)
$old = $_SESSION['old'] ?? [];

// Buscar projetos atribuídos ao estudante, incluindo o nome do docente
$projetos = [];
if ($_SESSION['tipo'] === 'Estudante') {
    $estudanteId = $_SESSION['id'];
    $conexao = (new crud())->getConexao();

    $sql = "
        SELECT p.Id, p.titulo, p.descricao, p.prazo, u.nome AS docente_nome
        FROM projectos p
        INNER JOIN grupo g ON g.projeto_id = p.Id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        INNER JOIN usuarios u ON u.id = p.docente_id
        WHERE ge.estudante_id = :estudante_id
    ";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':estudante_id', $estudanteId);
    $stmt->execute();
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div id="layoutSidenav_content">
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <title>Enviar Projeto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        body {
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 40px 50px;      /* Aumentei o padding para dar mais "respiro" */
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 900px;
            width: 100%;
            max-height: 90vh;       /* Limita a altura para caber na tela */
            overflow-y: auto;      /* Scroll vertical se o conteúdo ultrapassar */
        }

        .form-label {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            height: 42px;           /* Um pouco maior que o seu original */
            padding: 8px 12px;
            font-size: 0.95rem;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        textarea.form-control {
            min-height: 100px;      /* Aumentei a altura mínima */
            max-height: 180px;      /* Aumentei o máximo para melhor usabilidade */
            resize: vertical;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .btn-submit {
            font-weight: 600;
            padding: 12px 40px;
            border-radius: 8px;
            font-size: 1.1rem;
        }

        /* Ícones junto ao texto dos labels */
        .form-label i {
            margin-right: 8px;
            color: #198754;
        }

        /* Ajusta o espaço entre as linhas do formulário */
        .row > .col-md-6, .row > .col-md-12 {
            margin-bottom: 20px;
        }

        /* Overlay spinner */
        #overlayProcessando {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1050;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: #fff;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            user-select: none;
        }

        #overlayProcessando .spinner-border {
            width: 4rem;
            height: 4rem;
            border-width: 0.5rem;
            margin-bottom: 20px;
            border-top-color: #198754;
            border-right-color: transparent;
            border-bottom-color: #198754;
            border-left-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .form-container {
                padding: 25px 20px;
                max-width: 100%;
                max-height: 100vh;
            }
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert <?= strpos($_SESSION['mensagem'], 'Erro') !== false ? 'alert-danger' : 'alert-success' ?> text-center mx-auto mb-4 w-75" role="alert" style="max-width: 900px;">
        <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>

<div class="form-container shadow-sm">
    <h2 class="mb-4 text-center text-success fw-bold">Enviar Projeto</h2>
    <form method="post" action="../controlo/EnviarProjecto.php" enctype="multipart/form-data" id="formEnviarProjeto" autocomplete="off">

        <div class="row">
            <!-- Projeto Atribuído -->
            <div class="col-md-6">
                <label class="form-label" for="projetoSelect"><i class="fas fa-project-diagram"></i> Projeto Atribuído</label>
                <select class="form-select" name="projeto_id" id="projetoSelect" required>
                    <option value="">Selecione o projeto...</option>
                    <?php foreach ($projetos as $proj): ?>
                        <option 
                            value="<?= htmlspecialchars($proj['Id']) ?>"
                            data-descricao="<?= htmlspecialchars($proj['descricao']) ?>"
                            data-prazo="<?= htmlspecialchars($proj['prazo']) ?>"
                            data-docente="<?= htmlspecialchars($proj['docente_nome']) ?>"
                            <?= (isset($old['projeto_id']) && $old['projeto_id'] == $proj['Id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($proj['titulo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Nome do Docente -->
            <div class="col-md-6">
                <label class="form-label" for="docenteNome"><i class="fas fa-user"></i> Nome do Docente</label>
                <input type="text" id="docenteNome" class="form-control" name="docente_nome" 
                    value="<?= htmlspecialchars($old['docente_nome'] ?? '') ?>" required readonly>
            </div>

            <!-- Descrição -->
            <div class="col-md-12">
                <label class="form-label" for="descricaoProjeto"><i class="fas fa-pencil-alt"></i> Descrição</label>
                <textarea class="form-control" id="descricaoProjeto" name="descricao" rows="3" readonly required><?= htmlspecialchars($old['descricao'] ?? '') ?></textarea>
            </div>

            <!-- Prazo -->
            <div class="col-md-6">
                <label class="form-label" for="prazoProjeto"><i class="fas fa-calendar-alt"></i> Prazo do Projeto</label>
                <input type="date" id="prazoProjeto" class="form-control" name="data_submissao" 
                    value="<?= htmlspecialchars($old['data_submissao'] ?? '') ?>" readonly required>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label" for="estatus"><i class="fas fa-tasks"></i> Status</label>
                <select class="form-select" name="estatus" id="estatus" required>
                    <option value="emAndamento" <?= ($old['estatus'] ?? '') == 'emAndamento' ? 'selected' : '' ?>>Em andamento</option>
                    <option value="concluido" <?= ($old['estatus'] ?? '') == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                    <option value="atrasado" <?= ($old['estatus'] ?? '') == 'atrasado' ? 'selected' : '' ?>>Atrasado</option>
                </select>
            </div>

            <!-- Arquivo -->
            <div class="col-md-12">
                <label class="form-label" for="arquivo"><i class="fas fa-file-upload"></i> Arquivo</label>
                <input type="file" class="form-control" id="arquivo" name="arquivo" required>
            </div>

            <!-- Feedback -->
            <div class="col-md-12">
                <label class="form-label" for="feedback"><i class="fas fa-comments"></i> Comentários (Opcional)</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="3"><?= htmlspecialchars($old['feedback'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <button type="submit" class="btn btn-success btn-submit" id="btnSubmit">
                <i class="fas fa-paper-plane"></i> Enviar Projeto
            </button>
        </div>
    </form>
</div>

<footer class="mt-5">
    <?php include_once('Rodape.php'); ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Preencher campos conforme o projeto selecionado
    function preencherCamposProjeto() {
        const projetoSelect = document.getElementById('projetoSelect');
        const selectedOption = projetoSelect.options[projetoSelect.selectedIndex];
        document.getElementById('descricaoProjeto').value = selectedOption.dataset.descricao || '';
        document.getElementById('prazoProjeto').value = selectedOption.dataset.prazo || '';
        document.getElementById('docenteNome').value = selectedOption.dataset.docente || '';
    }

    // Evento quando mudar a seleção
    document.getElementById('projetoSelect').addEventListener('change', preencherCamposProjeto);

    // Preencher na carga da página, caso já tenha um projeto selecionado (ex: reenvio após erro)
    window.addEventListener('load', preencherCamposProjeto);

    // Mostrar overlay "Processando..." ao enviar o formulário e desabilitar botão
    document.getElementById('formEnviarProjeto').addEventListener('submit', function () {
        document.getElementById('overlayProcessando').style.display = 'flex';
        this.querySelector('#btnSubmit').disabled = true;
    });

    // Garantir que overlay está escondido no carregamento da página
    window.addEventListener('load', () => {
        document.getElementById('overlayProcessando').style.display = 'none';
    });
</script>

<!-- Overlay processando -->
<div id="overlayProcessando">
    <div class="spinner-border" role="status"></div>
    <div>Processando envio do projeto, por favor aguarde...</div>
</div>

</body>
</html>

<?php unset($_SESSION['old']); ?> 
