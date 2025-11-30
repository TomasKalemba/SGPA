<?php 
session_start();

// Cabeçalho
if (isset($_SESSION['tipo']) &&  $_SESSION['tipo'] == 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/Admin.php');
}

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

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo/css/styles.css">
    <style>
        .perfil-foto {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow-lg border-0 rounded-lg mt-4">
                            <div class="card-header text-center">
                                <h3 class="font-weight-light my-4"><i class="fas fa-user-plus"></i> Editar Conta</h3>
                            </div>
                            <div class="card-body">
                                <!-- Mensagem de Sucesso ou Erro -->
                                <?php if(isset($_SESSION['mensagem']) && !empty($_SESSION['mensagem'])): ?>
                                    <div id="feedbackMessage" class="alert alert-info">
                                        <?= $_SESSION['mensagem'] ?>
                                    </div>
                                    <?php $_SESSION['mensagem'] = ""; ?>
                                <?php endif; ?>

                                <form method="post" action="../controlo/EditarPerfilAdmin.php" enctype="multipart/form-data" id="createAccountForm">
                                    <input type="hidden" name="EditarPerfilAdmin" value="EditarPerfilAdmin">
                                    <input type="hidden" name="id" value="<?= $_SESSION['id']?>">

                                    <!-- Foto atual -->
                                    <div class="text-center mb-3">
                                        <img src="<?= $fotoUsuario ?>" alt="Foto de <?= htmlspecialchars($_SESSION['nome']) ?>" class="perfil-foto mb-2">
                                        <div class="form-group">
                                            <label for="foto">Alterar Foto</label>
                                            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                                        </div>
                                    </div>

                                    <!-- Nome do Usuário -->
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputFirstName" name="nome" type="text" value="<?= $_SESSION['nome']?>" required />
                                        <label for="inputFirstName"><i class="fas fa-user"></i> Nome</label>
                                    </div>

                                    <!-- Tipo de Usuário -->
                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="tipo" required>
                                            <option value="<?= $_SESSION['tipo']?>"><?= $_SESSION['tipo']?></option>
                                        </select>
                                        <label for="tipoUsuario"><i class="fas fa-chalkboard-teacher"></i> Tipo de Usuário</label>
                                    </div>

                                    <!-- E-mail -->
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputEmail" name="email" type="email" value="<?= $_SESSION['email']?>" required />
                                        <label for="inputEmail"><i class="fas fa-envelope"></i> E-mail</label>
                                    </div>

                                    <!-- Senha -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 mb-md-0">
                                                <input class="form-control" id="inputPassword" name="senha" type="password" placeholder="Digite sua senha" />
                                                <label for="inputPassword"><i class="fas fa-lock"></i> Senha</label>
                                            </div>
                                        </div>

                                        <!-- Confirmação de Senha -->
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 mb-md-0">
                                                <input class="form-control" id="inputPasswordConfirm" name="senhaUsuarioConfirm" type="password" placeholder="Confirme sua senha" />
                                                <label for="inputPasswordConfirm"><i class="fas fa-lock"></i> Confirmar Senha</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botão de Editar -->
                                    <div class="mt-4 mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary btn-block" type="submit">Editar</button>
                                        </div>
                                    </div>
                                </form>
                            </div> <!-- card-body -->
                        </div> <!-- card -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once('../visao/Rodape.php') ?>  
</div>

<!-- JQuery e Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
