<?php
session_start();

// ✅ Protege a página (somente Docente)
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Location: ../visao/login.php');
    exit;
}

// ✅ Impede cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('head/headDocente.php');
?>

<div id="layoutSidenav_content">
    <main class="py-4" style="min-height: 100vh; background-color: #f4f7fa;">
        <div class="container px-4" style="max-width: 1200px; width: 100%; font-size: 1.05rem;">

            <!-- Painel de Boas-Vindas -->
            <div class="bg-primary text-white p-4 rounded shadow text-center mb-4" style="width: 100%; margin-left: 0;">
                <h4><strong>Bem-vindo ao Painel do Docente!</strong></h4>
                <p>Acompanhe os projetos que você supervisiona e os prazos de entrega.</p>
            </div>

            <!-- Indicadores -->
            <div class="row g-3 mb-4">
                <?php
                $cards = [
                    ['id' => 'em_andamento', 'bg' => '#ffc107', 'icon' => 'hourglass-split', 'label' => 'Em Andamento'],
                    ['id' => 'concluidos', 'bg' => '#198754', 'icon' => 'check-circle-fill', 'label' => 'Concluídos'],
                    ['id' => 'atrasados', 'bg' => '#dc3545', 'icon' => 'exclamation-triangle-fill', 'label' => 'Atrasados'],
                    ['id' => 'em_falta', 'bg' => '#6c757d', 'icon' => 'x-circle-fill', 'label' => 'Em Falta'],
                    ['id' => 'total', 'bg' => '#0d6efd', 'icon' => 'collection-fill', 'label' => 'Total']
                ];

                foreach ($cards as $c) {
                    echo <<<HTML
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="card text-center shadow-sm text-white card-indicador" 
                             data-tipo="{$c['id']}" style="background-color: {$c['bg']}; cursor:pointer;">
                            <div class="card-body py-3">
                                <i class="bi bi-{$c['icon']}" style="font-size: 1.5rem;"></i>
                                <h6 class="mt-2" style="font-size: 0.9rem;">{$c['label']}</h6>
                                <h4 id="{$c['id']}" style="font-size: 1.2rem; margin:0;">0</h4>
                            </div>
                        </div>
                    </div>
                    HTML;
                }
                ?>
            </div>

            <!-- Calendário -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-calendar-alt"></i> Calendário de Projetos
                </div>
                <div class="card-body bg-white text-dark">
                    <div id="calendario" style="max-height: 500px; overflow-y: auto;"></div>
                </div>
            </div>

            <?php include_once('../visao/Rodape.php'); ?>
        </div>
    </main>
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
        <input type="text" id="searchModal" class="form-control mb-2" placeholder="Pesquisar...">
        <div class="table-modal mt-3">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Projeto</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody id="modalConteudo">
              <tr><td colspan="3" class="text-center text-muted">Nenhum dado carregado</td></tr>
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
$(document).ready(function() {
    // Carregar indicadores
    $.getJSON('dado_resumo_docente.php', function(data){
        $('#em_andamento').text(data.em_andamento ?? 0);
        $('#concluidos').text(data.concluidos ?? 0);
        $('#atrasados').text(data.atrasados ?? 0);
        $('#em_falta').text(data.em_falta ?? 0);
        $('#total').text(data.total ?? 0);
    });

    // Modal
    let registros=[], paginaAtual=1, porPagina=8;

    function renderizarLista() {
        const busca = $('#searchModal').val().toLowerCase();
        const filtrados = registros.filter(r => (r.nome||'').toLowerCase().includes(busca) || (r.data||'').toLowerCase().includes(busca));
        const total = filtrados.length;
        const totalPaginas = Math.max(1, Math.ceil(total / porPagina));
        if(paginaAtual>totalPaginas) paginaAtual=totalPaginas;
        const inicio = (paginaAtual-1)*porPagina;
        const pagina = filtrados.slice(inicio,inicio+porPagina);

        const $tbody = $('#modalConteudo');
        $tbody.empty();
        if(pagina.length===0) $tbody.append('<tr><td colspan="3" class="text-center text-muted">Nenhum registo encontrado</td></tr>');
        else pagina.forEach((item, idx)=>{
            $tbody.append(`<tr>
                <td>${inicio+idx+1}</td>
                <td>${item.nome}</td>
                <td>${item.data}</td>
            </tr>`);
        });

        const $pag = $('#modalPagination'); $pag.empty();
        $pag.append(`<li class="page-item ${paginaAtual===1?'disabled':''}"><button class="page-link" data-page="${paginaAtual-1}">«</button></li>`);
        for(let p=1;p<=totalPaginas;p++) $pag.append(`<li class="page-item ${p===paginaAtual?'active':''}"><button class="page-link" data-page="${p}">${p}</button></li>`);
        $pag.append(`<li class="page-item ${paginaAtual===totalPaginas?'disabled':''}"><button class="page-link" data-page="${paginaAtual+1}">»</button></li>`);
    }

    $(document).on('click','#modalPagination .page-link',function(){
        const p = Number($(this).data('page'));
        if(!isNaN(p) && p>=1){ paginaAtual=p; renderizarLista(); $('#modalIndicadores .modal-body').scrollTop(0);}
    });

    $(document).on('input','#searchModal',function(){ paginaAtual=1; renderizarLista(); });

    $(".card-indicador").on("click",function(){
        const tipo = $(this).data("tipo");
        registros=[]; paginaAtual=1; $('#searchModal').val('');
        $('#modalTitulo').text('A carregar...');
        $('#modalConteudo').html('<tr><td colspan="3" class="text-center"><div class="spinner-border"></div></td></tr>');
        $('#modalPagination').html('');
        const modalEl = document.getElementById('modalIndicadores');
        const bsModal = new bootstrap.Modal(modalEl); bsModal.show();

        $.getJSON('lista_indicadores_docente.php',{tipo:tipo},function(data){
            registros = data.map(item=>({nome:item.nome||'', data:item.data}));
            const labels = {
                em_andamento:'Projetos Em Andamento',
                concluidos:'Projetos Concluídos',
                atrasados:'Projetos Atrasados',
                em_falta:'Projetos Em Falta',
                total:'Todos os Projetos'
            };
            $('#modalTitulo').text(labels[tipo]??'Projetos');
            renderizarLista();
        }).fail(function(){
            $('#modalTitulo').text('Erro');
            $('#modalConteudo').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar dados</td></tr>');
        });
    });

    // Calendário
    var calendarEl = document.getElementById('calendario');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: 500,
        events: '../controlador/eventos_docente.php',
        eventDidMount: function(info){
            let diasTexto = '';
            if(typeof info.event.extendedProps.diasRestantes!=='undefined'){
                const dias = parseInt(info.event.extendedProps.diasRestantes);
                if(dias>1) diasTexto = `Dias restantes: ${dias}`;
                else if(dias===1) diasTexto='Falta 1 dia!';
                else if(dias===0) diasTexto='Entrega hoje!';
                else diasTexto=`⚠️ ${Math.abs(dias)} dias de atraso`;
            }
            let title = `Projeto: ${info.event.title}\n${diasTexto}`;
            new bootstrap.Tooltip(info.el,{title:title,placement:'top',trigger:'hover',container:'body'});
            if(info.event.extendedProps.vencido===true){
                info.el.style.backgroundColor='#dc3545';
                info.el.style.borderColor='#dc3545';
            }
        }
    });
    calendar.render();
});
</script>


    // ✅ Impede o botão "Voltar" do navegador
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>

<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>  
