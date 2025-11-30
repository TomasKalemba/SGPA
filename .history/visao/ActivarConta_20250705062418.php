<?php 
require_once '../modelo/conexao.php'; // ou onde estiver sua conexão PDO
if (isset($_SESSION['tipo']) &&  $_SESSION['tipo'] == 'Admin') {
    include_once( 'head/Admin.php');
}else{
    include_once( 'head/Admin.php');
}
// Buscar todos os docentes inativos
$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE tipo = 'Docente' AND ativo = 0");
$stmt->execute();
$docentes = $stmt->fetchAll();
?>        
            <div id="layoutSidenav_content">
                <br>
                                  
                      
                <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Criar Novo Projeto</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
    <!-- Link para o Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- CSS Customizado -->
    </head>
<body>
    <h2>Docentes Pendentes de Ativação</h2>

    <?php if (count($docentes) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docentes as $docente): ?>
                    <tr>
                        <td><?= $docente['nome'] ?></td>
                        <td><?= $docente['email'] ?></td>
                        <td>
                            <form action="ativar_docente.php" method="POST" onsubmit="return confirm('Deseja ativar este docente?');">
                                <input type="hidden" name="id" value="<?= $docente['id'] ?>">
                                <button type="submit">Ativar Conta</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum docente pendente de ativação.</p>
    <?php endif; ?>
    <!-- Rodapé -->
 
    <?php include_once( '../visao/Rodape.php') ?>
    </div>
</footer>
</body>
</html>
