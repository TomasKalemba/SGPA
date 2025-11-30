<?php 

if (isset($_SESSION['tipo']) &&  $_SESSION['tipo'] == 'Admin') {
    include_once( 'head/Admin.php');
}else{
    include_once( 'head/Admin.php');
}
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

    <!-- Rodapé -->
 
    <?php include_once( '../visao/Rodape.php') ?>
    </div>
</footer>
</body>
</html>
