<?php
session_start(); 
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

// Verifica se é docente
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') { 
    header('Location: ../Visao/login.php'); 
    exit; 
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8" />
<title>Painel do Docente</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ---------- ESTILOS SIMPLIFICADOS ---------- */
body { background: #f5f7fa; font-family: 'Segoe UI', sans-serif; color:#333; }
.info-cards .card { padding: 20px; border-radius: 14px; color: #fff; cursor: pointer; transition: transform .2s; }
.info-cards .card:hover { transform: translateY(-4px); }
.info-cards i { font-size: 2.2rem; margin-bottom: 6px; }
.info-cards h6 { font-size: 1rem; margin:0; font-weight:500; }
.info-cards h2 { font-size: 1.6rem; font-weight:700; margin-top:5px; }
.bg-andamento { background: linear-gradient(45deg,#17a2b8,#1dd1a1); }
.bg-concluidos { background: linear-gradient(45deg,#28a745,#85e085); }
.bg-atrasados { background: linear-gradient(45deg,#dc3545,#ff6b6b); }
.bg-emfalta { background: linear-gradient(45deg,#ffc107,#ffda65); }
.bg-total { background: linear-gradient(45deg,#0d6efd,#00bfff); }

/* Modal */
.modal-header { background: linear-gradient(90deg,#224abe,#0d6efd); color: #fff; }
.table-modal { max-height:52vh; overflow:auto; display:block; }
.table-modal table { width:100%; border-collapse: collapse; }
.table-modal tbody tr td { vertical-align: middle; }
.pagination { justify-content:center; margin-top:10px; }
</style>
</head>
<body>

<div class="container mt-4">

  <h3 class="mb-4">Bem-vindo, <?= $_SESSION['nome'] ?></h3>

  <!-- Indicadores -->
  <div class="row g-3 info-cards mb-4">
    <div class="col-12 col-sm-6 col-lg-2">
      <div class="card bg-andamento shadow-sm" data-tipo="em_andamento">
        <i class="bi bi-hourglass-split"></i>
        <h6>Em Andamento</h6>
        <h2 id="em_andamento">0</h2>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-2">
      <div class="card bg-concluidos shadow-sm" data-tipo="concluidos">
        <i class="bi bi-check-circle"></i>
        <h6>Concluídos</h6>
        <h2 id="concluidos">0</h2>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-2">
      <div class="card bg-atrasados shadow-sm" data-tipo="atrasados">
        <i class="bi bi-clock-history"></i>
        <h6>Atrasados</h6>
        <h2 id="atrasados">0</h2>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-2">
      <div class="card bg-emfalta shadow-sm" data-tipo="em_falta">
        <i class="bi bi-exclamation-circle"></i>
        <h6>Em Falta</h6>
        <h2 id="em_falta">0</h2>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-2">
      <div class="card bg-total shadow-sm" data-tipo="total">
        <i class="bi bi-collection-fill"></i>
        <h6>Total</h6>
        <h2 id="total">0</h2>
      </div>
    </div>
  </div>

</div>

<!-- Modal -->
<div class="modal fade" id="modalIndicadores" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo">Título</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="searchModal" class="form-control" placeholder="Pesquisar...">
        <div class="table-modal mt-3">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Projeto / Status</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody id="modalConteudo">
              <tr><td colspan="3" class="text-center">Nenhum dado carregado</td></tr>
            </tbody>
          </table>
        </div>
        <ul id="modalPagination" class="pagination"></ul>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

  // Carregar indicadores iniciais
  $.getJSON('dado_resumo_docente.php', function(data){
    $('#em_andamento').text(data.em_andamento ?? 0);
    $('#concluidos').text(data.concluidos ?? 0);
    $('#atrasados').text(data.atrasados ?? 0);
    $('#em_falta').text(data.em_falta ?? 0);
    $('#total').text(data.total ?? 0);
  });

  // Modal
  let registros=[], paginaAtual=1, porPagina=8;

  function renderizarLista(){
    const busca = $('#searchModal').val().toLowerCase();
    const filtrados = registros.filter(r => (r.nome||'').toLowerCase().includes(busca) || (r.data||'').toLowerCase().includes(busca));

    const total = filtrados.length;
    const totalPaginas = Math.max(1, Math.ceil(total/porPagina));
    if(paginaAtual>totalPaginas) paginaAtual=totalPaginas;

    const inicio = (paginaAtual-1)*porPagina;
    const pagina = filtrados.slice(inicio, inicio+porPagina);

    const $tbody = $('#modalConteudo');
    $tbody.empty();
    if(pagina.length===0){
      $tbody.append('<tr><td colspan="3" class="text-center text-muted">Nenhum registo encontrado</td></tr>');
    } else {
      pagina.forEach((item, idx)=>{
        $tbody.append(`<tr>
          <td>${inicio+idx+1}</td>
          <td>${item.nome}</td>
          <td>${item.data}</td>
        </tr>`);
      });
    }

    // Paginação
    const $pag = $('#modalPagination');
    $pag.empty();
    $pag.append(`<li class="page-item ${paginaAtual===1?'disabled':''}"><button class="page-link" data-page="${paginaAtual-1}">«</button></li>`);
    for(let p=1;p<=totalPaginas;p++){
      $pag.append(`<li class="page-item ${p===paginaAtual?'active':''}"><button class="page-link" data-page="${p}">${p}</button></li>`);
    }
    $pag.append(`<li class="page-item ${paginaAtual===totalPaginas?'disabled':''}"><button class="page-link" data-page="${paginaAtual+1}">»</button></li>`);
  }

  $(document).on('click','#modalPagination .page-link', function(){
    const p = Number($(this).data('page'));
    if(!isNaN(p) && p>=1){ paginaAtual=p; renderizarLista(); $('#modalIndicadores .modal-body').scrollTop(0);}
  });

  $(document).on('input','#searchModal',function(){ paginaAtual=1; renderizarLista(); });

  $(".info-cards .card").on("click",function(){
    const tipo = $(this).data("tipo");
    registros=[]; paginaAtual=1; $('#searchModal').val('');
    $('#modalTitulo').text('A carregar...');
    $('#modalConteudo').html('<tr><td colspan="3" class="text-center"><div class="spinner-border"></div></td></tr>');
    $('#modalPagination').html('');
    const modalEl = document.getElementById('modalIndicadores');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();

    $.getJSON('lista_indicadores_docente.php',{tipo:tipo},function(data){
      registros = data.map(item=>({nome:item.nome||item.titulo||'', data:item.data}));
      if(tipo==='em_andamento') $('#modalTitulo').text('Projetos Em Andamento');
      else if(tipo==='concluidos') $('#modalTitulo').text('Projetos Concluídos');
      else if(tipo==='atrasados') $('#modalTitulo').text('Projetos Atrasados');
      else if(tipo==='em_falta') $('#modalTitulo').text('Projetos Em Falta');
      else $('#modalTitulo').text('Todos os Projetos');
      renderizarLista();
    }).fail(function(){
      $('#modalTitulo').text('Erro');
      $('#modalConteudo').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar dados</td></tr>');
    });
  });

});
</script>

</body>
</html>
