<?php
session_start();
require_once '../modelo/crud.php';

// instanciar a classe CRUD
$Crud = new crud();
$conexao = $Crud->getConexao();

// buscar cursos para o <select>
$sql = "SELECT id, nome, departamento_id FROM cursos ORDER BY nome ASC"; // alterado para departamento_id
$stmt = $conexao->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// buscar departamentos direto da tabela 'departamento'
$sqlDep = "SELECT id, nome FROM departamento ORDER BY nome ASC";
$stmtDep = $conexao->prepare($sqlDep);
$stmtDep->execute();
$departamentos = $stmtDep->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Criar Conta - Sistema de Gestão de Projetos Acadêmicos</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        body {
            background: url('../visao/img/login.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 0;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .alert {
            border-radius: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header text-center">
                        <h3 class="font-weight-light my-4"><i class="fas fa-user-plus"></i> Criar Conta</h3>
                    </div>
                    <div class="card-body">

                        <!-- Mensagem de Sucesso ou Erro -->
                        <?php if (isset($_SESSION['mensagem']) && !empty($_SESSION['mensagem'])): ?>
                            <?php
                                $mensagem = $_SESSION['mensagem'];
                                $tipoAlerta = 'info';
                                if (str_contains(strtolower($mensagem), 'sucesso')) {
                                    $tipoAlerta = 'success';
                                } elseif (str_contains(strtolower($mensagem), 'erro') || str_contains(strtolower($mensagem), 'já está em uso')) {
                                    $tipoAlerta = 'danger';
                                } elseif (str_contains(strtolower($mensagem), 'aguarde')) {
                                    $tipoAlerta = 'warning';
                                }
                            ?>
                            <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($mensagem) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                            <?php unset($_SESSION['mensagem']); ?>
                        <?php endif; ?>

                        <form method="post" action="../controlo/login.php" enctype="multipart/form-data" id="createAccountForm">
                            <input type="hidden" name="criarconta" value="CriarConta">

                            <!-- Nome -->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputFirstName" name="nomeUsuario" type="text" placeholder="Digite seu nome completo" required />
                                <label for="inputFirstName"><i class="fas fa-user"></i> Nome</label>
                            </div>

                            <!-- Tipo -->
                            <div class="form-floating mb-3">
                                <select class="form-control" id="tipoUsuario" name="tipoUsuario" required>
                                    <option value="" disabled selected>Selecione o tipo de usuário</option>
                                    <option value="Estudante">Estudante</option>
                                    <option value="Docente">Docente</option>
                                </select>
                                <label for="tipoUsuario"><i class="fas fa-chalkboard-teacher"></i> Tipo de Usuário</label>
                            </div>

                            <!-- Campos adicionais (dinâmicos via JS) -->
                            <div id="camposExtras"></div>

                            <!-- E-mail -->
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="emailUsuario" type="email" placeholder="name@example.com" required />
                                <label for="inputEmail"><i class="fas fa-envelope"></i> E-mail</label>
                            </div>

                            <!-- Senha -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputPassword" name="senhaUsuario" type="password" placeholder="Digite sua senha" required />
                                        <label for="inputPassword"><i class="fas fa-lock"></i> Senha</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputPasswordConfirm" name="senhaUsuarioConfirm" type="password" placeholder="Confirme sua senha" required />
                                        <label for="inputPasswordConfirm"><i class="fas fa-lock"></i> Confirmar Senha</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto -->
                            <div class="mb-3">
                                <label for="fotoUsuario" class="form-label"><i class="fas fa-image"></i> Foto de Perfil (Opcional)</label>
                                <input class="form-control" type="file" id="fotoUsuario" name="fotoUsuario" accept="image/*">
                            </div>

                            <!-- Botão -->
                            <div class="mt-4 mb-0">
                                <div class="d-grid">
                                    <button class="btn btn-primary btn-block" type="submit">Criar Conta</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer text-center py-3">
                        <div class="small">
                            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Já tem uma conta? Faça login aqui.</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once('../visao/RodapeTelasInicial.php') ?>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para mostrar campos extras e validar matrícula -->
<script>
document.getElementById("tipoUsuario").addEventListener("change", function() {
    const tipo = this.value;
    const camposExtras = document.getElementById("camposExtras");
    camposExtras.innerHTML = "";

    if (tipo === "Estudante") {
        camposExtras.innerHTML = `
            <div class="form-floating mb-3">
                <input class="form-control" id="numeroMatricula" name="numeroMatricula" type="text" placeholder="Digite o número de matrícula" required />
                <label><i class="fas fa-id-card"></i> Número de Matrícula</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" name="curso_id" required>
                    <option value="" disabled selected>Selecione o curso</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label><i class="fas fa-graduation-cap"></i> Curso</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-control" name="ano_curricular" required>
                    <option value="" disabled selected>Selecione o ano curricular</option>
                    <option value="1">1º Ano</option>
                    <option value="2">2º Ano</option>
                    <option value="3">3º Ano</option>
                    <option value="4">4º Ano</option>
                    <option value="5">5º Ano</option>
                </select>
                <label><i class="fas fa-calendar-alt"></i> Ano Curricular</label>
            </div>
        `;

        const matriculaInput = document.getElementById("numeroMatricula");
        matriculaInput.addEventListener("input", function() {
            this.value = this.value.replace(/\D/g, '');
        });

        const form = document.getElementById("createAccountForm");
        form.addEventListener("submit", function(e) {
            const valor = matriculaInput.value;
            if (valor.length !== 6) {
                alert("O número de matrícula deve conter exatamente 6 dígitos.");
                matriculaInput.focus();
                e.preventDefault();
            }
        });

    } else if (tipo === "Docente") {
        camposExtras.innerHTML = `
            <div class="form-floating mb-3">
                <select class="form-control" name="departamento_id" required>
                    <option value="" disabled selected>Selecione o departamento</option>
                    <?php foreach ($departamentos as $dep): ?>
                        <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label><i class="fas fa-building"></i> Departamento</label>
            </div>
        `;
    }
});
</script>
