<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}
include_once('head/Admin.php');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8" />
<title>Painel do Administrador</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
 body { background: #f5f7fa; font-family: 'Segoe UI', sans-serif; color: #333; }
 .dark-mode { background: #1e1e2f; color: #f0f0f0; }
 .welcome-message { background: #fff; padding: 25px; border-radius: 14px; text-align: center; margin: 25px 0; }
 .info-cards .card { padding: 20px; min-height: 130px; border-radius: 14px; color: #fff; cursor: pointer; }
 .bg-docente { background: linear-gradient(45deg, #17a2b8, #1dd1a1); }
 .bg-estudante { background: linear-gradient(45deg, #224abe, #0d6efd); }
 .bg-total { background: linear-gradient(45deg, #0d6efd, #00bfff); }
 /* Modal melhorado */
 .modal-header { background: #224abe; color: #fff; }
 .search-box { margin-bottom: 10px; }
 .pagination { justify-content: center; }
</style>
</head>
<body>
<div class="container">
  <button class="toggle-dark" id="darkToggle"><i class="bi bi-moon"></i></button>

  <div class="welcome-message">
    <h3><strong>Bem-vindo ao Painel do Administrador</strong></h3>
    <p>Visualize o progresso dos projetos, estatísticas e atividades recentes.</p>
  </div>

  <!-- Indicadores -->
  <div class="row g-3 info-cards mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-docente shadow-sm indicador" data-tipo="docentes">
        <i class="bi bi-person-badge-fill"></i>
        <h6>Projetos Docentes</h6>
        <h2 id="docentes">0</h2>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-estudante shadow-sm indicador" data-tipo="estudantes">
        <i class="bi bi-person-workspace"></i>
        <h6>Submissões Estudantes</h6>
        <h2 id="estudantes">0</h2>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card bg-total shadow-sm indicador" data-tipo="total">
        <i class="bi bi-collection-fill"></i>
        <h6>Total Geral</h6>
        <h2 id="total">0</h2>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalIndicadores" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloModal"></h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <input type="text" id="searchInput" class="form-control search-box" placeholder="Pesquisar...">

        <table class="table table-hover table-bordered">
          <thead>
            <tr><th>#</th><th>Nome</th><th>Data</th></tr>
          </thead>
          <tbody id="tabelaModal"></tbody>
        </table>

        <nav>
          <ul id="pagination" class="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
  $.getJSON('dado_resumo_admin.php', function(data) {
    $('#docentes').text(data.totalProjetos ?? 0);
    $('#estudantes').text(data.totalSubmissoes ?? 0);
    $('#total').text(data.totalGeral ?? 0);
  });

  let registros = [];
  let paginaAtual = 1;
  const porPagina = 6;

  function carregarTabela() {
    let filtro = $('#searchInput').val().toLowerCase();
    let filtrados = registros.filter(r => r.nome.toLowerCase().includes(filtro));

    let inicio = (paginaAtual - 1) * porPagina;
    let pagina = filtrados.slice(inicio, inicio + porPagina);

    $('#tabelaModal').html('');
    pagina.forEach((item, i) => {
      $('#tabelaModal').append(`
        <tr>
          <td>${inicio + i + 1}</td>
          <td>${item.nome}</td>
          <td>${item.data}</td>
        </tr>`);
    });

    let totalPaginas = Math.ceil(filtrados.length / porPagina);
    $('#pagination').html('');

    for (let i = 1; i <= totalPaginas; i++) {
      $('#pagination').append(`
        <li class="page-item ${i === paginaAtual ? 'active' : ''}">
          <button class="page-link" onclick="mudarPagina(${i})">${i}</button>
        </li>`);
    }
  }

  window.mudarPagina = function(num) {
    paginaAtual = num;
    carregarTabela();
  };

  $('.indicador').on('click', function () {
    let tipo = $(this).data('tipo');

    $('#tituloModal').text('A carregar...');
    $('#tabelaModal').html('<tr><td colspan="3" class="text-center">A carregar...</td></tr>');

    $.getJSON('lista_indicadores.php', { tipo: tipo }, function(data) {
      registros = data;
      paginaAtual = 1;

      $('#tituloModal').text(
        tipo === 'docentes' ? 'Projetos dos Docentes' :
        tipo === 'estudantes' ? 'Submissões dos Estudantes' :
        'Total Geral'
      );

      carregarTabela();
    });

    let modal = new bootstrap.Modal(document.getElementById('modalIndicadores'));
    modal.show();
  });

  $(document).on('keyup', '#searchInput', function(){ carregarTabela(); });
});
</script>
<?php include_once('../visao/Rodape.php'); ?>
</body>
</html>
