<?php
session_start();

// Cabeçalho conforme tipo de usuário (se estiver logado)
if (isset($_SESSION['tipo'])) {
    $tipoUsuario = $_SESSION['tipo'];

    if ($tipoUsuario === 'Estudante') {
        include_once('head/Estudante.php');
    } elseif ($tipoUsuario === 'Docente') {
        include_once('head/Docente.php');
    } elseif ($tipoUsuario === 'Admin') {
        include_once('head/Admin.php');
    } else {
        include_once('head/Publico.php'); // caso haja um tipo inesperado
    }
} else {
    include_once('head/Publico.php'); // usuário não logado
}
?>

<div id="layoutSidenav_content">
    <main class="container mt-4">
        <div class="card-custom p-4">
            <h4><i class="fas fa-cog text-primary"></i> Definições</h4>
            <p class="text-muted mb-4">Ajuste suas preferências e configurações do sistema.</p>

            <div class="row">
                <!-- Alterar Senha -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-key fa-2x mb-3 text-primary"></i>
                            <h5 class="card-title">Alterar Senha</h5>
                            <p class="card-text">Modifique sua senha de acesso à conta.</p>
                            <a href="password.php" class="btn btn-primary btn-sm">Ir</a>
                        </div>
                    </div>
                </div>

                <!-- Preferências de Notificação -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-bell fa-2x mb-3 text-warning"></i>
                            <h5 class="card-title">Notificações</h5>
                            <p class="card-text">Gerencie alertas e notificações do sistema.</p>
                            <a href="notificacoes.php" class="btn btn-warning btn-sm">Ir</a>
                        </div>
                    </div>
                </div>

                <!-- Informações da Conta -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-user-cog fa-2x mb-3 text-success"></i>
                            <h5 class="card-title">Minha Conta</h5>
                            <p class="card-text">Veja e edite suas informações pessoais.</p>
                            <a href="minha_conta.php" class="btn btn-success btn-sm">Ir</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include_once('Rodape.php'); ?>
</div>

<!-- JQuery e Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
.card-custom {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.card h5 {
    font-weight: 600;
}
.card p {
    font-size: 14px;
    color: #555;
}
</style>

</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

