<?php 
session_start(); 

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Admin') { 
    header('Location: ../login.php'); 
    exit; 
} 

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
   margin-top: 20px;
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
   cursor: pointer;
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

 /* Atividades */
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
      <div class="card bg-docente shadow-sm" data-tipo="docentes">
        <i class="bi bi-person-badge-fill"></i>
        <h6>Projetos Docentes</h6>
        <h2 id="docentes">0</h2>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-estudante shadow-sm" data-tipo="estudantes">
        <i class="bi bi-person-workspace"></i>
        <h6>Submissões Estudantes</h6>
        <h2 id="estudantes">0</h2>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-total shadow-sm" data-tipo="geral">
        <i class="bi bi-collection-fill"></i>
        <h6>Total Geral</h6>
        <h2 id="total">0</h2>
      </div>
    </div>
  </div>

  <!-- Atividades -->
  <div class="activity-cards">
    <div class="row">

      <!-- Atividades Docentes -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-primary text-white">
            Atividades dos Docentes
          </div>
          <ul class="list-group list-group-flush">
            <?php 
            require_once("../modelo/crud.php");
            $crud = new crud();
            $sql = "SELECT u.nome, p.titulo, p.data_criacao
                    FROM projectos p 
                    JOIN usuarios u ON p.docente_id = u.id 
                    ORDER BY p.data_criacao DESC LIMIT 5";

            $stmt = $crud->getConexao()->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data = date("d/m/Y H:i", strtotime($row["data_criacao"]));
                echo "<li class='list-group-item'>
                        <strong>{$row['nome']}</strong> criou o projeto <em>{$row['titulo']}</em><br>
                        <small class='text-muted'>{$data}</small>
                      </li>";
              }
            } else {
              echo "<li class='list-group-item text-muted'>Sem atividades recentes</li>";
            }
            ?>
          </ul>
        </div>
      </div>

      <!-- Atividades Estudantes -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-success text-white">
            Atividades dos Estudantes
          </div>
          <ul class="list-group list-group-flush">
            <?php 
            $sql = "SELECT u.nome, s.data_submissao
                    FROM submisoes s 
                    JOIN usuarios u ON s.estudante_id = u.id 
                    ORDER BY s.data_submissao DESC LIMIT 5";

            $stmt = $crud->getConexao()->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data = date("d/m/Y H:i", strtotime($row["data_submissao"]));
                echo "<li class='list-group-item'>
                        <strong>{$row['nome']}</strong> submeteu um projeto<br>
                        <small class='text-muted'>{$data}</small>
                      </li>";
              }
            } else {
              echo "<li class='list-group-item text-muted'>Sem atividades recentes</li>";
            }
            ?>
          </ul>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- --------------- MODAL --------------- -->
<div class="modal fade" id="modalIndicadores" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo"></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="modalConteudo">
        <div class="text-center"><div class="spinner-border"></div></div>
      </div>

    </div>
  </div>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

  // Carregar dados do topo
  $.getJSON('dado_resumo_admin.php', function(data) {
    $('#docentes').text(data.totalProjetos ?? 0);
    $('#estudantes').text(data.totalSubmissoes ?? 0);
    $('#total').text(data.totalGeral ?? 0);
  });

  // Dark Mode
  $('#darkToggle').on('click', function() {
    $('body').toggleClass('dark-mode');
    const icon = $(this).find('i');
    icon.toggleClass('bi-sun bi-moon');
  });

  // 🌟 Abrir Modal ao clicar nos cards
  $(".info-cards .card").on("click", function() {
    let tipo = $(this).data("tipo");

    $("#modalTitulo").text("Carregando...");
    $("#modalConteudo").html('<div class="text-center"><div class="spinner-border"></div></div>');

    $("#modalIndicadores").modal("show");

    $.get("lista_indicadores.php", { tipo: tipo }, function(res) {
      $("#modalConteudo").html(res);

      if(tipo === "docentes") $("#modalTitulo").text("Projetos Docentes");
      if(tipo === "estudantes") $("#modalTitulo").text("Submissões Estudantes");
      if(tipo === "geral") $("#modalTitulo").text("Total Geral");
    });
  });

});
</script>

<?php include_once('../visao/Rodape.php'); ?>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
