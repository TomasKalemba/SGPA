<?php
session_start();

// ✅ Verifica se é Admin
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../visao/login.php');
    exit;
}

// ✅ Evita cache para não permitir voltar após logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('head/Admin.php');
?>

<div id="layoutSidenav_content">
<main>
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
            <?php
            require_once("../modelo/crud.php");
            $crud = new crud();

            $filtroData = '';
            if (isset($_GET['periodo']) && in_array($_GET['periodo'], ['7','30'])) {
                $dias = (int)$_GET['periodo'];
                $filtroData = "AND p.data_criacao >= DATE_SUB(NOW(), INTERVAL $dias DAY)";
            }

            $sql = "SELECT u.nome, p.titulo, p.data_criacao
                    FROM projectos p
                    JOIN usuarios u ON p.docente_id = u.id
                    WHERE 1=1 $filtroData
                    ORDER BY p.data_criacao DESC
                    LIMIT 5";
            $stmt = $crud->getConexao()->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $dataFormatada = date('d/m/Y H:i', strtotime($row['data_criacao']));
                    echo "<li class='list-group-item'>
                            <i class='fas fa-user text-primary me-2'></i>
                            <strong>{$row['nome']}</strong> Criou o projeto: <em>{$row['titulo']}</em><br>
                            <small class='text-muted'>em {$dataFormatada}</small>
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
            <i class="fas fa-user-graduate me-2"></i> Atividades dos Estudantes
          </div>
          <ul class="list-group list-group-flush">
            <?php
            $filtroDataEstudante = '';
            if (isset($_GET['periodo']) && in_array($_GET['periodo'], ['7','30'])) {
                $dias = (int)$_GET['periodo'];
                $filtroDataEstudante = "AND s.data_submissao >= DATE_SUB(NOW(), INTERVAL $dias DAY)";
            }

            $sql = "SELECT u.nome, s.data_submissao
                    FROM submisoes s
                    JOIN usuarios u ON s.estudante_id = u.id
                    WHERE 1=1 $filtroDataEstudante
                    ORDER BY s.data_submissao DESC
                    LIMIT 5";
            $stmt = $crud->getConexao()->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $dataFormatada = date('d/m/Y H:i', strtotime($row['data_submissao']));
                    echo "<li class='list-group-item'>
                            <i class='fas fa-upload text-success me-2'></i>
                            <strong>{$row['nome']}</strong> submeteu um projeto<br>
                            <small class='text-muted'>em {$dataFormatada}</small>
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
</main>

<?php include_once('../visao/Rodape.php'); ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Atualiza indicadores
    $.getJSON('dado_resumo_admin.php', function(data) {
        $('#docentes').text(data.totalProjetos ?? 0);
        $('#estudantes').text(data.totalSubmissoes ?? 0);
        $('#total').text(data.totalGeral ?? 0);
    });

    // Dark Mode
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

// ✅ Bloqueio botão voltar
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>

<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

