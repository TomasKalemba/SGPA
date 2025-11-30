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

/* Modal styling improvements */
.modal-header {
  background: linear-gradient(90deg,#224abe,#0d6efd);
  color: #fff;
}
.modal-title { font-weight:700; }
#searchModal { margin-bottom: 12px; }
.table-modal { max-height: 52vh; overflow:auto; display:block; }
.table-modal table { width:100%; border-collapse: collapse; }
.table-modal tbody tr td { vertical-align: middle; }
.pagination { justify-content:center; margin-top:10px; }
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
<div class="modal fade" id="modalIndicadores" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo">Título</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body">
        <input type="text" id="searchModal" class="form-control" placeholder="Pesquisar por nome ou data...">

        <div class="table-modal mt-3">
          <table class="table table-hover">
            <thead>
              <tr>
                <th style="width:5%;">#</th>
                <th>Nome / Título</th>
                <th style="width:25%;">Data</th>
              </tr>
            </thead>
            <tbody id="modalConteudo">
              <tr><td colspan="3" class="text-center">Nenhum dado carregado</td></tr>
            </tbody>
          </table>
        </div>

        <nav>
          <ul id="modalPagination" class="pagination"></ul>
        </nav>
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

  // Variáveis para paginação/search
  let registros = [];       // array de objetos {nome, data}
  let paginaAtual = 1;
  const porPagina = 8;

  function renderizarLista() {
    const busca = $('#searchModal').val().toLowerCase();
    const filtrados = registros.filter(r => {
      const nome = (r.nome || '').toString().toLowerCase();
      const data = (r.data || '').toString().toLowerCase();
      return nome.includes(busca) || data.includes(busca);
    });

    const total = filtrados.length;
    const totalPaginas = Math.max(1, Math.ceil(total / porPagina));
    if (paginaAtual > totalPaginas) paginaAtual = totalPaginas;

    const inicio = (paginaAtual - 1) * porPagina;
    const pagina = filtrados.slice(inicio, inicio + porPagina);

    // Preencher tabela
    const $tbody = $('#modalConteudo');
    $tbody.empty();

    if (pagina.length === 0) {
      $tbody.append('<tr><td colspan=\"3\" class=\"text-center text-muted\">Nenhum registo encontrado</td></tr>');
    } else {
      pagina.forEach((item, idx) => {
        const index = inicio + idx + 1;
        const nome = item.nome || '';
        const data = item.data || '';
        $tbody.append(`<tr>
          <td>${index}</td>
          <td>${nome}</td>
          <td>${data}</td>
        </tr>`);
      });
    }

    // Paginacao
    const $pag = $('#modalPagination');
    $pag.empty();

    // botão anterior
    $pag.append(`<li class="page-item ${paginaAtual === 1 ? 'disabled' : ''}">
      <button class="page-link" data-page="${paginaAtual - 1}" aria-label="Anterior">«</button></li>`);

    // páginas (limitamos a mostrar até 7 botões para evitar overflow)
    let start = Math.max(1, paginaAtual - 3);
    let end = Math.min(totalPaginas, start + 6);
    if (end - start < 6) start = Math.max(1, end - 6);

    for (let p = start; p <= end; p++) {
      $pag.append(`<li class="page-item ${p === paginaAtual ? 'active' : ''}">
        <button class="page-link" data-page="${p}">${p}</button></li>`);
    }

    // botão proximo
    $pag.append(`<li class="page-item ${paginaAtual === totalPaginas ? 'disabled' : ''}">
      <button class="page-link" data-page="${paginaAtual + 1}" aria-label="Próximo">»</button></li>`);
  }

  // clicar em paginação
  $(document).on('click', '#modalPagination .page-link', function() {
    const p = Number($(this).data('page'));
    if (!isNaN(p) && p >= 1) {
      paginaAtual = p;
      renderizarLista();
      // scroll topo do modal para melhor UX
      const modalBody = document.querySelector('#modalIndicadores .modal-body');
      if (modalBody) modalBody.scrollTop = 0;
    }
  });

  // pesquisa em tempo real
  $(document).on('input', '#searchModal', function() {
    paginaAtual = 1;
    renderizarLista();
  });

  // Abrir modal com conteúdo (tentamos JSON; se não, inserimos HTML retornado)
  $(".info-cards .card").on("click", function() {
    const tipo = $(this).data("tipo");

    // Reset UI
    registros = [];
    paginaAtual = 1;
    $('#searchModal').val('');
    $('#modalTitulo').text('A carregar...');
    $('#modalConteudo').html('<tr><td colspan=\"3\" class=\"text-center\"><div class=\"spinner-border\"></div></td></tr>');
    $('#modalPagination').html('');

    // Mostra modal
    const modalEl = document.getElementById('modalIndicadores');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();

    // Faz a requisição pedindo JSON preferencialmente
    $.ajax({
      url: 'lista_indicadores.php',
      method: 'GET',
      data: { tipo: tipo, formato: 'json' },
      dataType: 'text', // primeiro pegamos como texto para detectar HTML vs JSON
      success: function(responseText) {
        // tenta parsear JSON
        let parsed = null;
        try {
          parsed = JSON.parse(responseText);
        } catch (e) {
          parsed = null;
        }

        if (parsed && Array.isArray(parsed)) {
          // formato JSON esperado: array de { nome, data }
          registros = parsed.map(item => {
            // normalizar item (aceita vários formatos)
            if (typeof item === 'string') {
              return { nome: item, data: '' };
            }
            return {
              nome: item.nome ?? item.titulo ?? item.descricao ?? '',
              data: item.data ?? item.data_criacao ?? item.data_submissao ?? ''
            };
          });

          // definir título
          if (tipo === 'docentes') $('#modalTitulo').text('Projetos Docentes');
          else if (tipo === 'estudantes') $('#modalTitulo').text('Submissões Estudantes');
          else $('#modalTitulo').text('Total Geral');

          renderizarLista();
        } else {
          // resposta não-JSON -> assumimos que é HTML (antigo comportamento)
          // colocamos o HTML diretamente no corpo do modal (compatibilidade)
          $('#modalTitulo').text(tipo === 'docentes' ? 'Projetos Docentes' : (tipo === 'estudantes' ? 'Submissões Estudantes' : 'Total Geral'));
          // Se o HTML for uma lista ou tabela completa, colocamos dentro do tbody ou substituímos
          const html = responseText.trim();
          // tenta detectar se o HTML contém <tr> ... então substitui tbody
          if (/<tr[\s>]/i.test(html) || /<table[\s>]/i.test(html)) {
            // substitui o conteúdo do tbody
            try {
              // extrair linhas <tr> do HTML
              const matches = html.match(/<tr[\s\S]*?<\/tr>/gi);
              if (matches && matches.length) {
                $('#modalConteudo').html(matches.join(''));
                $('#modalPagination').html(''); // sem paginação quando HTML bruto
                $('#searchModal').val(''); // desativa pesquisa
              } else {
                // se não encontrou tr, insere todo HTML abaixo da tabela
                $('#modalConteudo').html('<tr><td colspan=\"3\">' + html + '</td></tr>');
              }
            } catch (err) {
              $('#modalConteudo').html('<tr><td colspan=\"3\">' + html + '</td></tr>');
            }
          } else {
            // HTML simples (listas): coloca dentro do tbody
            $('#modalConteudo').html('<tr><td colspan=\"3\">' + html + '</td></tr>');
            $('#modalPagination').html('');
          }
        }
      },
      error: function() {
        $('#modalTitulo').text('Erro');
        $('#modalConteudo').html('<tr><td colspan=\"3\" class=\"text-center text-danger\">Erro ao carregar dados</td></tr>');
        $('#modalPagination').html('');
      }
    });
  });

});
</script>

<?php include_once('../visao/Rodape.php'); ?>
</div>
    </main>
</div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
