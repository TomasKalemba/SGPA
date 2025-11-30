<?php 
session_start(); 

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Admin') { 
    header('Location: ../login.php'); 
    exit; 
} 

include_once('head/Admin.php'); 
?> 
<?php
// Seu código completo do index com modal, paginação e pesquisa
// Adicione aqui o conteúdo PHP inicial (session, includes, queries etc.)
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .modal-lg {
            max-width: 900px;
        }
        #searchBox {
            margin-bottom: 15px;
        }
        .pagination a {
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-docente text-white p-4 shadow">
                <h4>Projetos Docentes</h4>
                <p>Total: <?php echo $totalDocentes ?? 0; ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-estudante text-white p-4 shadow">
                <h4>Submissões Estudantes</h4>
                <p>Total: <?php echo $totalEstudantes ?? 0; ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-total text-white p-4 shadow">
                <h4>Total Geral</h4>
                <p>Total: <?php echo $totalGeral ?? 0; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal melhorado -->
<div class="modal fade" id="modalIndicadores" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTitulo"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <input type="text" id="searchBox" class="form-control" placeholder="Pesquisar...">

        <div id="modalConteudo"></div>

        <nav>
            <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<script>
let dados = [];         // Dados carregados
let porPagina = 10;      // Itens por página
let paginaAtual = 1;     // Página atual

function renderizarTabela() {
    let pesquisa = $("#searchBox").val().toLowerCase();

    let filtrado = dados.filter(item =>
        item.nome.toLowerCase().includes(pesquisa) ||
        item.data.toLowerCase().includes(pesquisa)
    );

    let inicio = (paginaAtual - 1) * porPagina;
    let fim = inicio + porPagina;
    let pagina = filtrado.slice(inicio, fim);

    let html = '<ul class="list-group">';
    if (pagina.length > 0) {
        pagina.forEach(item => {
            html += `
            <li class="list-group-item">
                <strong>${item.nome}</strong><br>
                <small class="text-muted">${item.data}</small>
            </li>`;
        });
    } else {
        html += '<li class="list-group-item">Nenhum registo encontrado</li>';
    }
    html += '</ul>';

    $("#modalConteudo").html(html);

    renderizarPaginacao(filtrado.length);
}

function renderizarPaginacao(total) {
    let totalPaginas = Math.ceil(total / porPagina);
    let html = '';

    for (let i = 1; i <= totalPaginas; i++) {
        html += `
        <li class="page-item ${i === paginaAtual ? 'active' : ''}">
            <a class="page-link" onclick="irParaPagina(${i})">${i}</a>
        </li>`;
    }

    $("#pagination").html(html);
}

function irParaPagina(p) {
    paginaAtual = p;
    renderizarTabela();
}

function abrirModal(tipo){
  paginaAtual = 1;

  $("#modalTitulo").text("Carregando...");
  $("#modalConteudo").html('<div class="text-center"><div class="spinner-border"></div></div>');

  $("#modalIndicadores").modal("show");

  $.ajax({
    url: "lista_indicadores.php",
    method: "GET",
    data: { tipo: tipo, formato: "json" },
    success: function(res){
        dados = JSON.parse(res);
        console.log(dados);

        if(tipo === "docentes") $("#modalTitulo").text("Projetos Docentes");
        if(tipo === "estudantes") $("#modalTitulo").text("Submissões Estudantes");
        if(tipo === "geral") $("#modalTitulo").text("Total Geral");

        renderizarTabela();
    }
  });
}

$("#searchBox").on("input", function(){
    paginaAtual = 1;
    renderizarTabela();
});

$(".card.bg-docente").css("cursor","pointer").click(function(){ abrirModal("docentes"); });
$(".card.bg-estudante").css("cursor","pointer").click(function(){ abrirModal("estudantes"); });
$(".card.bg-total").css("cursor","pointer").click(function(){ abrirModal("geral"); });
</script>

</body>
</html>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
