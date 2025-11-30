<?php
session_start();

if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/headDocente.php');
}

// Conexão para buscar a foto do usuário e o nome do departamento
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

$stmt = $conn->prepare("
    SELECT u.foto, d.nome AS departamento_nome
    FROM usuarios u
    LEFT JOIN departamento d ON u.departamento_id = d.id
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Define o caminho da foto
$fotoUsuario = !empty($usuario['foto']) 
    ? "fotos/" . htmlspecialchars($usuario['foto'])  
    : "estilo/img/default.png";

// Nome do departamento
$departamentoNome = !empty($usuario['departamento_nome']) 
    ? $usuario['departamento_nome'] 
    : 'Não informado';
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
    margin-bottom: 15px;
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
                                    BEM VINDO AO TEU PERFIL DOCENTE: <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong>
                                </div>

                                <!-- Informações do usuário -->
                                <div class="small">EMAIL: <strong><?= htmlspecialchars($_SESSION['email']) ?></strong></div>
                                <div class="small">DEPARTAMENTO: <strong><?= htmlspecialchars($departamentoNome) ?></strong></div>

                                <!-- Botão de Editar Perfil -->
                                <div class="btn-edit">
                                    <a href="EditarPerfilDocente.php" class="btn btn-warning btn-sm">
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

