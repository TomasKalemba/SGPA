<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Location: ../visao/login.php');
    exit;
}
include_once('head/headDocente.php');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8">
<title>Painel do Docente</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<style>
html, body { height: 100%; margin:0; }
#layoutSidenav_content { min-height: 100%; display: flex; flex-direction: column; }
main { flex: 1 0 auto; }
footer { flex-shrink: 0; }
body { background: #f5f7fa; font-family: 'Segoe UI', sans-serif; color:#333; }

/* ---------- INDICADORES ---------- */
.info-cards { display: flex; gap: 10px; justify-content: space-between; flex-wrap: wrap; margin-bottom: 1.5rem; }
.info-cards .card { flex: 1 1 0; text-align: center; padding: 25px; border-radius: 14px; color: #fff; cursor: pointer; transition: transform .2s; min-width: 120px; }
.info-cards .card:hover { transform: translateY(-4px); }
.info-cards i { font-size: 2.5rem; margin-bottom: 8px; }
.info-cards h6 { font-size: 1rem; margin:0; font-weight:500; }
.info-cards h2 { font-size: 1.8rem; font-weight:700; margin-top:5px; }

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

<div id="layoutSidenav_content">
<main class="py-4">
<div class="container px-0" style="max-width:1200px;">

<!-- Painel de Boas-Vindas -->
<div class="bg-primary text-white p-4 rounded shadow text-center mb-4">
<h4><strong>Bem-vindo ao Painel do Docente!</strong></h4>
<p>Acompanhe os projetos que você supervisiona e os prazos de entrega.</p>
</div>

<!-- Indicadores -->
<div class="info-cards">
    <div class="card bg-andamento shadow-sm" data-tipo="em_andamento">
        <i class="bi bi-hourglass-split"></i>
        <h6>Em Andamento</h6>
        <h2 id="em_andamento">0</h2>
    </div>
    <div class="card bg-concluidos shadow-sm" data-tipo="concluidos">
        <i class="bi bi-check-circle"></i>
        <h6>Concluídos</h6>
        <h2 id="concluidos">0</h2>
    </div>
    <div class="card bg-atrasados shadow-sm" data-tipo="atrasados">
        <i class="bi bi-clock-history"></i>
        <h6>Atrasados</h6>
        <h2 id="atrasados">0</h2>
    </div>
    <div class="card bg-emfalta shadow-sm" data-tipo="em_falta">
        <i class="bi bi-exclamation-circle"></i>
        <h6>Em Falta</h6>
        <h2 id="em_falta">0</h2>
    </div>
    <div class="card bg-total shadow-sm" data-tipo="total">
        <i class="bi bi-collection-fill"></i>
        <h6>Total</h6>
        <h2 id="total">0</h2>
    </div>
</div>

<!-- Calendário -->
<div class="card shadow-sm mb-4">
<div class="card-header bg-primary text-white">
<i class="bi bi-calendar-event"></i> Calendário de Projetos
</div>
<div class="card-body bg-white text-dark">
<div id="calendario" style="min-height:500px;"></div>
</div>
</div>

<?php include_once('../visao/Rodape.php'); ?>

</div>
</main>
</div>

<!-- Modal indicadores -->
<div class="modal fade" id="modalIndicadores" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="modalTitulo">Título</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="text" id="searchModal" class="form-control mb-3" placeholder="Pesquisar...">
<div class="table-modal">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
$(document).ready(function(){

    // 🔹 Atualizar indicadores
    function atualizarIndicadores(){
        $.getJSON('dados_resumo.php', function(data){
            $('#em_andamento').text(data.em_andamento ?? 0);
            $('#concluidos').text(data.concluidos ?? 0);
            $('#atrasados').text(data.atrasados ?? 0);
            $('#em_falta').text(data.em_falta ?? 0);
            $('#total').text(data.total ?? 0);
        });
    }
    atualizarIndicadores();

    // 🔹 Modal indicadores
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
            // Certifique-se de que 'data' é array de objetos com titulo e data
            registros = data.map(item=>({nome:item.titulo||item.nome||'', data:item.data||''}));
            const titulos = {
                em_andamento:'Projetos Em Andamento',
                concluidos:'Projetos Concluídos',
                atrasados:'Projetos Atrasados',
                em_falta:'Projetos Em Falta',
                total:'Todos os Projetos'
            };
            $('#modalTitulo').text(titulos[tipo]||'Projetos');
            renderizarLista();
        }).fail(function(jqXHR,textStatus,errorThrown){
            console.error('Erro AJAX:', textStatus,errorThrown);
            $('#modalTitulo').text('Erro');
            $('#modalConteudo').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar dados</td></tr>');
        });
    });

    // 🔹 Calendário FullCalendar
    var calendarEl = document.getElementById('calendario');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView:'dayGridMonth',
        locale:'pt-br',
        height:500,
        events:'../controlador/eventos_docente.php',
        eventDidMount:function(info){
            let diasTexto='';
            if(typeof info.event.extendedProps.diasRestantes!=='undefined'){
                const dias=parseInt(info.event.extendedProps.diasRestantes);
                if(dias>1) diasTexto=`Dias restantes: ${dias}`;
                else if(dias===1) diasTexto='Falta 1 dia!';
                else if(dias===0) diasTexto='Entrega hoje!';
                else if(dias<0) diasTexto=`⚠️ ${Math.abs(dias)} dias de atraso`;
            }
            let title=`Projeto: ${info.event.title}\n${diasTexto}`;
            if(info.event.extendedProps.status) title+=`\nStatus: ${info.event.extendedProps.status}`;
            new bootstrap.Tooltip(info.el,{
                title:title,
                placement:'top',
                trigger:'hover',
                container:'body'
            });
            if(info.event.extendedProps.vencido===true){
                info.el.style.backgroundColor='#dc3545';
                info.el.style.borderColor='#dc3545';
            }
        }
    });
    calendar.render();
});
</script>
</body>
</html>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>  

