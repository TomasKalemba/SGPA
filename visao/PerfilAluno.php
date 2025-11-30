<?php
session_start();

if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/Estudante.php');
}

// Conexão para buscar os dados do usuário
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

$stmt = $conn->prepare("SELECT foto, numero_matricula, curso_id FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Foto do usuário
$fotoUsuario = !empty($usuario['foto']) 
    ? "fotos/" . htmlspecialchars($usuario['foto'])  
    : "estilo/img/default.png";

// Buscar o nome do curso, se houver curso_id
$nomeCurso = '';
if (!empty($usuario['curso_id'])) {
    $stmtCurso = $conn->prepare("SELECT nome FROM cursos WHERE id = ?");
    $stmtCurso->execute([$usuario['curso_id']]);
    $curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);
    $nomeCurso = $curso['nome'] ?? '';
}
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
                            <div class="card-body text-center">
                                <!-- Foto do usuário -->
                                <img src="<?= $fotoUsuario ?>" alt="Foto de <?= htmlspecialchars($_SESSION['nome']) ?>" class="perfil-foto">

                                <!-- Informações do usuário -->
                                <div class="small">BEM VINDO AO TEU PERFIL ESTUDANTE: <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong></div>
                                <div class="small">EMAIL: <strong><?= htmlspecialchars($_SESSION['email']) ?></strong></div>
                                <div class="small">Nº DE MATRÍCULA: <strong><?= htmlspecialchars($usuario['numero_matricula']) ?></strong></div>
                                <?php if ($nomeCurso): ?>
                                    <div class="small">CURSO: <strong><?= htmlspecialchars($nomeCurso) ?></strong></div>
                                    <div class="small">ANO CURRICULAR: <strong><?= htmlspecialchars($_SESSION['ano_curricular']) ?></strong></div>
                                <?php endif; ?>

                                <!-- Botão de Editar Perfil -->
                                <div class="mt-3">
                                    <a href="EditarPerfilAluno.php" class="btn btn-warning btn-sm">
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
