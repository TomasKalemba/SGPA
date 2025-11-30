<?php
session_start();
require_once '../modelo/crud.php';

// Verifica se usuário está logado
if (!isset($_SESSION['tipo'])) {
    header('Location: ../visao/login.php');
    exit;
}
?>

<?php
// Inclui o cabeçalho de acordo com o tipo de usuário
switch ($_SESSION['tipo']) {
    case 'Admin':
        include_once('head/Admin.php');
        break;
    case 'Docente':
        include_once('head/headDocente.php');
        break;
    case 'Estudante':
        include_once('head/Estudante.php');
        break;
    default:
        include_once('head/Estudante.php'); // fallback
        break;
}
?>


<style>
body {
    background-color: #f5f7fa !important;
}
.card-custom {
    background: #fff;
    border-radius: 12px;
    border: none;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin: 40px auto;
    max-width: 900px;
}
h2, h4 {
    font-weight: 600;
    color: #2c3e50;
}
p {
    font-size: 16px;
    line-height: 1.6;
    color: #4e5d6c;
}
</style>

<div id="layoutSidenav_content">
    <main class="container mt-5">
        <div class="card-custom">
            <h2 class="mb-3"><i class="fas fa-info-circle text-primary"></i> Sobre o SGPA</h2>
            <p>
                O <strong>Sistema de Gestão de Projetos Acadêmicos (SGPA)</strong> foi desenvolvido com o objetivo de facilitar o gerenciamento e acompanhamento de projetos acadêmicos dentro da universidade. 
                Ele permite que administradores, docentes e estudantes tenham um controle completo sobre criação, submissão, avaliação e acompanhamento de projetos em tempo real.
            </p>
            <p>
                Com uma interface intuitiva e moderna, o SGPA busca reduzir a burocracia e aumentar a eficiência na gestão acadêmica, permitindo colaboração entre docentes e estudantes e garantindo maior transparência no acompanhamento de todas as etapas do projeto.
            </p>
            <p>
                Este sistema é fruto de um trabalho dedicado, utilizando tecnologias como PHP, MySQL, Bootstrap e JavaScript para oferecer uma experiência completa e segura aos seus usuários.
            </p>
            <h4 class="mt-4">Funcionalidades principais:</h4>
            <ul>
                <li>Cadastro e gerenciamento de estudantes e docentes.</li>
                <li>Criação, envio de projetos acadêmicos.</li>
                <li>Controle de grupos e atribuição de estudantes aos projetos.</li>
                <li>Histórico de submissões dos projetos.</li>
                <li>Notificações internas e mensagens para acompanhamento de atividades.</li>
            </ul>
            <p class="mt-4">
                O SGPA é constantemente atualizado e aprimorado para atender às necessidades da comunidade acadêmica, garantindo facilidade de uso, segurança e confiabilidade.
            </p>
        </div>
    </main>

    <?php include_once('../visao/Rodape.php'); ?>
</div>
