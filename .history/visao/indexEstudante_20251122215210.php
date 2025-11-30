<?php 
session_start(); 
if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Admin') { 
    include_once('head/Admin.php'); 
} else { 
    include_once('head/Estudante.php'); 
} 
?>
<div id="layoutSidenav_content">
<main class="bg-light py-4 d-flex justify-content-center align-items-start" style="min-height: 100vh;">
<div class="container px-4" style="max-width: 1200px; width: 100%; font-size: 1.05rem;">

<!-- Boas-Vindas -->
<div class="p-4 rounded shadow text-white text-center mb-4" style="background-color: #007c91;">
    <h4><strong>Bem-vindo ao Painel do Estudante!</strong></h4>
    <p>Acompanhe seus projetos, tarefas e prazos de forma organizada.</p>
</div>

<!-- Indicadores com Ícones -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #0d6efd;" data-tipo="em_andamento">
            <div class="card-body">
                <i class="bi bi-hourglass-split fs-2"></i>
                <h6 class="text-white mt-2">Em Andamento</h6>
                <h2 id="emAndamento">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #198754;" data-tipo="concluidos">
            <div class="card-body">
                <i class="bi bi-check-circle-fill fs-2"></i>
                <h6 class="text-white mt-2">Concluídos</h6>
                <h2 id="concluidos">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #dc3545;" data-tipo="atrasados">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                <h6 class="text-white mt-2">Atrasados</h6>
                <h2 id="atrasados">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #6c757d;" data-tipo="em_falta">
            <div class="card-body">
                <i class="bi bi-x-circle-fill fs-2"></i>
                <h6 class="text-white mt-2">Em Falta</h6>
                <h2 id="emFalta">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #ffc107;" data-tipo="total">
            <div class="card-body">
                <i class="bi bi-collection-fill fs-2"></i>
                <h6 class="text-white mt-2">Total de Projetos</h6>
                <h2 id="totalProjetos">0</h2>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color: #007c91;">
                <i class="fas fa-chart-bar"></i> Status dos Projetos
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header text-white" style="background-color: #007c91;">
                <i class="fas fa-chart-line"></i> Progresso Mensal
            </div>
            <div class="card-body" style="height: 370px;">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Calendário -->
<div class="card shadow-sm mb-5">
    <div class="card-header text-white" style="background-color: #007c91;">
        <i class="fas fa-calendar-alt"></i> Calendário de Entregas
    </div>
    <div class="card-body">
        <div id="calendario"></div>
    </div>
</div>

</div> <!-- /.container -->
</main>
<?php include_once('../visao/Rodape.php'); ?>
</div> <!-- /.layoutSidenav_content -->

<!-- Modal indicadores estudante -->
<div class="modal fade" id="modalIndicadoresEstudante" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTituloEstudante">Título</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchModalEstudante" class="form-control mb-3" placeholder="Pesquisar...">
                <div class="table-modal">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Projeto / Status</th>
                                <th>Prazo</th>
                            </tr>
                        </thead>
                        <tbody id="modalConteudoEstudante">
                            <tr><td colspan="3" class="text-center">Nenhum dado carregado</td></tr>
                        </tbody>
                    </table>
                </div>
                <ul id="modalPaginationEstudante" class="pagination"></ul>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Atualizar indicadores
$.getJSON('dados_resumo_estudante.php', function(data) {
    if (data && !data.erro) {
        $('#emAndamento').text(data.emAndamento);
        $('#concluidos').text(data.concluidos);
        $('#atrasados').text(data.atrasados);
        $('#emFalta').text(data.emFalta);
        $('#totalProjetos').text(data.totalProjetos);
        atualizarGraficoStatus(data);
    }
});

function atualizarGraficoStatus(data) {
    const ctx = document.getElementById('statusChart').getContext('2d');
    if (window.statusChart instanceof Chart) window.statusChart.destroy();
    window.statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Em andamento', 'Concluídos', 'Atrasados', 'Em Falta', 'Total'],
            datasets: [{
                label: 'Projetos',
                data: [data.emAndamento, data.concluidos, data.atrasados, data.emFalta, data.totalProjetos],
                backgroundColor: ['#0d6efd','#198754','#dc3545','#6c757d','#ffc107']
            }]
        },
        options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, title:{display:true, text:'Quantidade de Projetos'}}} }
    });
}

// Gráfico de progresso mensal
$.getJSON('dados_progresso_estudante.php', function(data) {
    if(data && !data.erro) {
        const ctx = document.getElementById('progressChart').getContext('2d');
        if(window.progressChart instanceof Chart) window.progressChart.destroy();
        const meses = Object.keys(data);
        const valores = Object.values(data);
        window.progressChart = new Chart(ctx, {
            type:'line',
            data:{ labels:meses, datasets:[{label:'Projetos com Prazo', data:valores, borderColor:'#0d6efd', backgroundColor:'rgba(13,110,253,0.1)', pointBackgroundColor:'#0d6efd', pointRadius:5, tension:0.4, fill:true }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{title:{display:true,text:'Evolução Mensal dos Projetos com Prazo',font:{size:18}}}, scales:{y:{beginAtZero:true,title:{display:true,text:'Quantidade de Projetos'}},x:{title:{display:true,text:'Meses'}}} }
        });
    }
});

// Calendário
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario');
    var calendar = new FullCalendar.Calendar(calendarEl,{
        initialView:'dayGridMonth',
        locale:'pt-br',
        height:500,
        events:'eventos.php',
        eventColor:'#0d6efd',
        headerToolbar:{ left:'prev,next today', center:'title', right:'dayGridMonth,listWeek' },
        eventDidMount:function(info){
            let diasTexto='';
            if(typeof info.event.extendedProps.diasRestantes!=='undefined'){
                const dias=parseInt(info.event.extendedProps.diasRestantes);
                if(dias>1) diasTexto=`Dias restantes: ${dias}`;
                else if(dias===1) diasTexto='Falta 1 dia!';
                else if(dias===0) diasTexto='Entrega hoje!';
                else if(dias<0) diasTexto=`⚠️ ${Math.abs(dias)} dias de atraso`;
            }
            let title=`Status: ${info.event.extendedProps.status || 'Desconhecido'}\n${diasTexto}`;
            if(info.event.extendedProps.feedback) title+=`\nFeedback: ${info.event.extendedProps.feedback}`;
            if(info.event.extendedProps.vencido===true){ info.el.style.backgroundColor='#dc3545'; info.el.style.borderColor='#dc3545'; }
            else if(info.event.extendedProps.alerta===true){ info.el.style.backgroundColor='#fd7e14'; info.el.style.borderColor='#fd7e14'; }
            new bootstrap.Tooltip(info.el,{title:title,placement:'top',trigger:'hover',container:'body'});
        }
    });
    calendar.render();
});

// Modal indicadores estudante
let registrosEstudante=[], paginaAtualEstudante=1, porPaginaEstudante=8;

function renderizarListaEstudante(){
    const busca=$('#searchModalEstudante').val().toLowerCase();
    const filtrados=registrosEstudante.filter(r=>(r.nome||'').toLowerCase().includes(busca)||(r.data||'').toLowerCase().includes(busca));
    const total=filtrados.length;
    const totalPaginas=Math.max(1,Math.ceil(total/porPaginaEstudante));
    if(paginaAtualEstudante>totalPaginas) paginaAtualEstudante=totalPaginas;
    const inicio=(paginaAtualEstudante-1)*porPaginaEstudante;
    const pagina=filtrados.slice(inicio,inicio+porPaginaEstudante);
    const $tbody=$('#modalConteudoEstudante');
    $tbody.empty();
    if(pagina.length===0){
        $tbody.append('<tr><td colspan="3" class="text-center text-muted">Nenhum registo encontrado</td></tr>');
    } else {
        pagina.forEach((item,idx)=>{
            $tbody.append(`<tr><td>${inicio+idx+1}</td><td>${item.nome}</td><td>${item.data}</td></tr>`);
        });
    }
    const $pag=$('#modalPaginationEstudante');
    $pag.empty();
    $pag.append(`<li class="page-item ${paginaAtualEstudante===1?'disabled':''}"><button class="page-link" data-page="${paginaAtualEstudante-1}">«</button></li>`);
    for(let p=1;p<=totalPaginas;p++){
        $pag.append(`<li class="page-item ${p===paginaAtualEstudante?'active':''}"><button class="page-link" data-page="${p}">${p}</button></li>`);
    }
    $pag.append(`<li class="page-item ${paginaAtualEstudante===totalPaginas?'disabled':''}"><button class="page-link" data-page="${paginaAtualEstudante+1}">»</button></li>`);
}

$(document).on('click','#modalPaginationEstudante .page-link', function(){
    const p=Number($(this).data('page'));
    if(!isNaN(p)&&p>=1){ paginaAtualEstudante=p; renderizarListaEstudante(); $('#modalIndicadoresEstudante .modal-body').scrollTop(0);}
});
$(document).on('input','#searchModalEstudante',function(){ paginaAtualEstudante=1; renderizarListaEstudante(); });

$(".row .card").on("click", function(){
    const tipo = $(this).data("tipo");
    registrosEstudante=[]; paginaAtualEstudante=1; $('#searchModalEstudante').val('');
    $('#modalTituloEstudante').text('A carregar...');
    $('#modalConteudoEstudante').html('<tr><td colspan="3" class="text-center"><div class="spinner-border"></div></td></tr>');
    $('#modalPaginationEstudante').html('');
    const modalEl = document.getElementById('modalIndicadoresEstudante');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();

    $.getJSON('lista_indicadores_estudante.php',{tipo:tipo},function(data){
        registrosEstudante = data.map(item=>({
            nome: item.titulo,
            data: `${item.prazo} (${item.status})`
        }));
        const titulos = {
            em_andamento:'Projetos Em Andamento',
            concluidos:'Projetos Concluídos',
            atrasados:'Projetos Atrasados',
            em_falta:'Projetos Em Falta',
            total:'Todos os Projetos'
        };
        $('#modalTituloEstudante').text(titulos[tipo]||'Projetos');
        renderizarListaEstudante();
    }).fail(function(){
        $('#modalTituloEstudante').text('Erro');
        $('#modalConteudoEstudante').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar dados</td></tr>');
    });
});


</script>

</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>



