<?php 
session_start(); 
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Admin') { header('Location: ../login.php'); exit; } 

include_once('head/Admin.php'); 

?>

<div id="layoutSidenav_content">
<main>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8" />
<title>Painel do Administrador</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
 body {
   background: #f5f7fa;
   font-family: 'Segoe UI', sans-serif;
   color: #333;
   transition: background 0.3s, color 0.3s;
 }
 .dark-mode {
   background: #1e1e2f;
   color: #f0f0f0;
 }
 .welcome-message {
   background: #fff;
   color: #333;
   padding: 25px;
   border-radius: 14px;
   text-align: center;
   margin-bottom: 30px;
   box-shadow: 0 4px 12px rgba(0,0,0,0.08);
   transition: background 0.3s, color 0.3s;
 }
 .dark-mode .welcome-message {
   background: #2a2a40;
   color: #f0f0f0;
 }
 .welcome-message h3 { font-weight: 700; color: #224abe; }
 .dark-mode .welcome-message h3 { color: #4a8dff; }

 /* Cards indicadores */
 .info-cards .card {
   padding: 20px;
   min-height: 130px;
   display: flex;
   flex-direction: column;
   align-items: center;
   justify-content: center;
   border-radius: 14px;
   color: #fff;
   transition: transform 0.2s ease, box-shadow 0.2s ease;
   background-size: 200% 200%;
   animation: gradientShift 6s ease infinite;
 }
 .info-cards .card:hover {
   transform: translateY(-4px);
   box-shadow: 0 8px 20px rgba(0,0,0,0.25);
 }
 .info-cards i { font-size: 2.2rem; margin-bottom: 6px; }
 .info-cards h6 { font-size: 1rem; margin: 0; font-weight: 500; }
 .info-cards h2 { font-size: 1.6rem; font-weight: 700; margin-top: 5px; }

 /* Gradientes */
 .bg-docente { background: linear-gradient(45deg, #17a2b8, #1dd1a1); }
 .bg-estudante { background: linear-gradient(45deg, #224abe, #0d6efd); }
 .bg-total { background: linear-gradient(45deg, #0d6efd, #00bfff); }

 @keyframes gradientShift {
   0% { background-position: 0% 50%; }
   50% { background-position: 100% 50%; }
   100% { background-position: 0% 50%; }
 }

 /* Cards de atividades */
 .activity-cards {
   background: #fff;
   border-radius: 14px;
   padding: 25px;
   margin-top: 30px;
   box-shadow: 0 4px 12px rgba(0,0,0,0.1);
   transition: background 0.3s, color 0.3s;
 }
 .dark-mode .activity-cards {
   background: #2a2a40;
   color: #f0f0f0;
 }
 .card-header {
   border-radius: 12px 12px 0 0 !important;
   font-weight: 600;
 }
 .list-group-item {
   border: none;
   border-bottom: 1px solid #eee;
   padding: 12px 15px;
   transition: background 0.3s, color 0.3s;
 }
 .dark-mode .list-group-item {
   background: #2a2a40;
   border-bottom: 1px solid #444;
   color: #f0f0f0;
 }
 .list-group-item:last-child { border-bottom: none; }

 /* Botão Dark Mode */
 .toggle-dark {
   position: absolute;
   top: 20px;
   right: 20px;
   border: none;
   background: none;
   font-size: 1.5rem;
   cursor: pointer;
   color: #224abe;
 }
 .dark-mode .toggle-dark { color: #f0f0f0; }
</style>
</head>
<body>
<div class="container">
  <button class="toggle-dark" id="darkToggle"><i class="bi bi-moon"></i></button>

  <!-- Boas-Vindas -->
  <div class="welcome-message">
    <h3><strong>Bem-vindo ao Painel do Administrador</strong></h3>
    <p>Visualize o progresso dos projetos, estatísticas e atividades recentes.</p>
  </div>

  <!-- Indicadores -->
  <div class="row g-3 info-cards mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-docente shadow-sm">
        <i class="bi bi-person-badge-fill"></i>
        <h6>Projetos Docentes</h6>
        <h2 id="docentes">0</h2>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-estudante shadow-sm">
        <i class="bi bi-person-workspace"></i>
        <h6>Submissões Estudantes</h6>
        <h2 id="estudantes">0</h2>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-total shadow-sm">
        <i class="bi bi-collection-fill"></i>
        <h6>Total Geral</h6>
        <h2 id="total">0</h2>
      </div>
    </div>
  </div>

  <!-- Filtro -->
  <div class="mb-4">
    <form method="get" class="row align-items-center">
      <label class="col-auto col-form-label"><strong>Filtrar por período:</strong></label>
      <div class="col-auto">
        <select name="periodo" class="form-select" onchange="this.form.submit()">
          <option value="todos" <?= (!isset($_GET['periodo']) || $_GET['periodo'] == 'todos') ? 'selected' : '' ?>>Todos</option>
          <option value="7" <?= (isset($_GET['periodo']) && $_GET['periodo'] == '7') ? 'selected' : '' ?>>Últimos 7 dias</option>
          <option value="30" <?= (isset($_GET['periodo']) && $_GET['periodo'] == '30') ? 'selected' : '' ?>>Últimos 30 dias</option>
        </select>
      </div>
    </form>
  </div>

  <!-- Atividades -->
  <div class="activity-cards">
    <div class="row">
      <!-- Atividades Docentes -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-primary text-white">
            <i class="fas fa-chalkboard-teacher me-2"></i> Atividades dos Docentes
          </div>
          <ul class="list-group list-group-flush">
            <?php require_once("../modelo/crud.php"); $crud = new crud(); $filtroData = ''; if (isset($_GET['periodo']) && in_array($_GET['periodo'], ['7','30'])) { $dias = (int)$_GET['periodo']; $filtroData = "AND p.data_criacao >= DATE_SUB(NOW(), INTERVAL $dias DAY)"; } $sql = "SELECT u.nome, p.titulo, p.data_criacao FROM projectos p JOIN usuarios u ON p.docente_id = u.id WHERE 1=1 $filtroData ORDER BY p.data_criacao DESC LIMIT 5"; $stmt = $crud->getConexao()->prepare($sql); $stmt->execute(); if ($stmt->rowCount() > 0) { while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $dataFormatada = date('d/m/Y H:i', strtotime($row['data_criacao'])); echo "<li class='list-group-item'> <i class='fas fa-user text-primary me-2'></i> <strong>{$row['nome']}</strong> Criou o projecto: <em>{$row['titulo']}</em><br> <small class='text-muted'>em {$dataFormatada}</small> </li>"; } } else { echo "<li class='list-group-item text-muted'>Sem atividades recentes</li>"; } ?>
          </ul>
        </div>
      </div>

      <!-- Atividades Estudantes -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-success text-white">
            <i class="fas fa-user-graduate me-2"></i> Atividades dos Estudantes
          </div>
          <ul class="list-group list-group-flush">
            <?php $filtroDataEstudante = ''; if (isset($_GET['periodo']) && in_array($_GET['periodo'], ['7','30'])) { $dias = (int)$_GET['periodo']; $filtroDataEstudante = "AND s.data_submissao >= DATE_SUB(NOW(), INTERVAL $dias DAY)"; } $sql = "SELECT u.nome, s.data_submissao FROM submisoes s JOIN usuarios u ON s.estudante_id = u.id WHERE 1=1 $filtroDataEstudante ORDER BY s.data_submissao DESC LIMIT 5"; $stmt = $crud->getConexao()->prepare($sql); $stmt->execute(); if ($stmt->rowCount() > 0) { while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $dataFormatada = date('d/m/Y H:i', strtotime($row['data_submissao'])); echo "<li class='list-group-item'> <i class='fas fa-upload text-success me-2'></i> <strong>{$row['nome']}</strong> submeteu um projeto<br> <small class='text-muted'>em {$dataFormatada}</small> </li>"; } } else { echo "<li class='list-group-item text-muted'>Sem atividades recentes</li>"; } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  $.getJSON('dado_resumo_admin.php', function(data) {
    $('#docentes').text(data.totalProjetos ?? 0);
    $('#estudantes').text(data.totalSubmissoes ?? 0);
    $('#total').text(data.totalGeral ?? 0);
  });

  $('#darkToggle').on('click', function(){
    $('body').toggleClass('dark-mode');
    const icon = $(this).find('i');
    if ($('body').hasClass('dark-mode')) {
      icon.removeClass('bi-moon').addClass('bi-sun');
    } else {
      icon.removeClass('bi-sun').addClass('bi-moon');
    }
  });
});
</script>
<?php include_once('../visao/Rodape.php') ?>
</body>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
