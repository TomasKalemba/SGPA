<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

$tipoUsuario = $_SESSION['tipo'];

// Inclui o cabeçalho correto conforme o tipo de usuário
if ($tipoUsuario === 'Estudante') {
    include_once('head/Estudante.php');
} elseif ($tipoUsuario === 'Admin') {
    include_once('head/Admin.php');
} elseif ($tipoUsuario === 'Docente') {
    include_once('head/headDocente.php');
} else {
    // Caso haja algum tipo inesperado, redireciona para login
    header("Location: ../visao/login.php");
    exit;
}
?>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <div class="card-custom p-4">
            <h4><i class="fas fa-envelope text-primary"></i> Contato</h4>

            <!-- Alertas de status -->
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] === 'enviado'): ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> Mensagem enviada com sucesso!
                    </div>
                <?php elseif ($_GET['status'] === 'erro'): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-times-circle"></i> Ocorreu um erro ao enviar a mensagem. Tente novamente.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form id="formContato" action="../controlo/enviar_contato.php" method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="mensagem" class="form-label">Mensagem</label>
                    <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar Mensagem
                </button>
            </form>
        </div>
    </main>

    <?php include_once('Rodape.php'); ?>
</div>

<!-- Validação JavaScript -->
<script>
$(document).ready(function() {
    $('#formContato').on('submit', function(e) {
        let nome = $('#nome').val().trim();
        let email = $('#email').val().trim();
        let mensagem = $('#mensagem').val().trim();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if(nome === '' || email === '' || mensagem === '') {
            alert('Por favor, preencha todos os campos.');
            e.preventDefault();
            return false;
        }

        if(!emailRegex.test(email)) {
            alert('Por favor, insira um email válido.');
            e.preventDefault();
            return false;
        }
    });
});
</script>

<style>
.card-custom {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.alert {
    border-radius: 10px;
    font-weight: 500;
}
</style>
