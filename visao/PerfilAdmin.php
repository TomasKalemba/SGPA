<?php
session_start();
include_once('head/Admin.php');

// Conexão para buscar a foto do usuário
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

$stmt = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Define o caminho da foto
$fotoUsuario = !empty($usuario['foto'])
    ? "fotos/" . htmlspecialchars($usuario['foto'])
    : "estilo/img/default.png";
?>

<style>
body {
    background-color: #f8f9fa;
    padding-top: 5px;
}
.card {
    margin-top: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
}
.card-header {
    height: 70px;
    background-color: #0056b3;
    color: white;
    font-weight: 600;
}
.card-body {
    background-color: white;
    padding: 20px;
    text-align: center;
}
.perfil-foto {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #007bff;
    margin-bottom: 15px;
}
.welcome-message {
    background-color: #0056b3;
    color: white;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 30px;
}
.btn-edit {
    margin-top: 15px;
}
</style>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-8">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow-lg border-0 rounded-lg mt-4">
                            <div class="card-header text-center">
                                <h3 class="font-weight-light my-4"><i class="fas fa-user"></i> PERFIL</h3>
                            </div>
                            <div class="card-body">
                                <!-- Foto do usuário -->
                                <img src="<?= $fotoUsuario ?>" alt="Foto de <?= htmlspecialchars($_SESSION['nome']) ?>" class="perfil-foto">

                                <!-- Mensagem de boas-vindas -->
                                <div class="welcome-message">
                                    BEM VINDO AO TEU PERFIL ADMINISTRADOR: <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong>
                                </div>

                                <!-- Informações do usuário -->
                                <div class="small">EMAIL: <strong><?= htmlspecialchars($_SESSION['email']) ?></strong></div>
                                <div class="small" style="display:none;">ID: <strong><?= htmlspecialchars($_SESSION['id']) ?></strong></div>

                                <!-- Botão de Editar Perfil -->
                                <div class="btn-edit">
                                    <a href="EditarPerfilAdmin.php" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar Perfil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <footer>
        <?php include_once('../visao/Rodape.php'); ?>
    </footer>
</div>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts adicionais -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="js/scripts.js"></script>

</body>
</html>
