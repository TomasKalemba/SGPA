<?php
session_start();

// Protege a página (somente Docente)
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Location: ../visao/login.php');
    exit;
}

// Impede cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('head/headDocente.php');
?>

<style>
html, body { height: 100%; }
#layoutSidenav_content { min-height: 100%; display: flex; flex-direction: column; }
main { flex: 1 0 auto; }
footer { flex-shrink: 0; }

body { background: #f4f7fa; font-family: 'Segoe UI', sans-serif; color:#333; }
.info-cards .card { padding: 20px; border-radius: 14px; color: #fff; cursor: pointer; transition: transform .2s; height: 130px; }
.info-cards .card:hover { transform: translateY(-4px); }
.info-cards i { font-size: 2.2rem; margin-bottom: 6px; }
.info-cards h6 { font-size: 1rem; margin:0; font-weight:500; }
.info-cards h2 { font-size: 1.6rem; font-weight:700; margin-top:5px; }
.bg-andamento { background: linear-gradient(45deg,#17a2b8,#1dd1a1); }
.bg-concluidos { background: linear-gradient(45deg,#28a745,#85e085); }
.bg-atrasados { background: linear-gradient(45deg,#dc3545,#ff6b6b); }
.bg-emfalta { background: linear-gradient(45deg,#ffc107,#ffda65); }
.bg-total { background: linear-gradient(45deg,#0d6efd,#00bfff); }
</style>

<div id="layoutSidenav_content">
    <main class="py-4">
        <div class="container px-4" style="max-width: 1200px; width: 100%; font-size: 1.05rem;">

            <!-- Painel de Boas-Vindas -->
            <div class="bg-primary text-white p-4 rounded shadow text-center mb-4">
                <h4><strong>Bem-vindo ao Painel do Docente!</strong></h4>
                <p>Acompanhe os projetos que você supervisiona e os prazos de entrega.</p>
            </div>

            <!-- Indicadores ponta a ponta -->
            <div class="row g-3 mb-4 info-cards text-center">
                <?php
                $cards = [
                    ['id'=>'em_andamento','bg'=>'bg-andamento','icon'=>'hourglass-split','label'=>'Em Andamento'],
                    ['id'=>'concluidos','bg'=>'bg-concluidos','icon'=>'check-circle','label'=>'Concluídos'],
                    ['id'=>'atrasados','bg'=>'bg-atrasados','icon'=>'clock-history','label'=>'Atrasados'],
                    ['id'=>'em_falta','bg'=>'bg-emfalta','icon'=>'exclamation-circle','label'=>'Em Falta'],
                    ['id'=>'total','bg'=>'bg-total','icon'=>'collection-fill','label'=>'Total']
                ];

                foreach($cards as $c){
                    echo <<<HTML
                    <div class="col-12 col-sm-6 col-lg-2">
                        <div class="card {$c['bg']} shadow-sm" data-tipo="{$c['id']}">
                            <i class="bi bi-{$c['icon']}"></i>
                            <h6>{$c['label']}</h6>
                            <h2 id="{$c['id']}">0</h2>
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
                    <div id="calendario" style="min-height:500px;"></div>
                </div>
            </div>

            <?php include_once('../visao/Rodape.php'); ?>
        </div>
    </main>
</div>

<!-- Modal indicadores -->
<div class="modal fade" id="modalIndicadores" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">Título</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchModal" class="form-control mb-3" placeholder="Pesquisar...">
                <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
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
                <ul id="modalPagination" class="pagination justify-content-center mt-2"></ul>
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

    // Carregar números reais da base de dados
    $.getJSON('dados_resumo.php', function(data){
        $('#em_andamento').text(data.emAndamento ?? 0);
        $('#concluidos').text(data.concluidos ?? 0);
        $('#atrasados').text(data.atrasados ?? 0);
        $('#em_falta').text(data.emFalta ?? 0);
        $('#total').text(data.total ?? 0);
    });

    // Modal indicadores
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
            registros = data.map(item=>({nome:item.nome||item.titulo||'', data:item.data}));
            const titulos = {
                em_andamento:'Projetos Em Andamento',
                concluidos:'Projetos Concluídos',
                atrasados:'Projetos Atrasados',
                em_falta:'Projetos Em Falta',
                total:'Todos os Projetos'
            };
            $('#modalTitulo').text(titulos[tipo]||'Projetos');
            renderizarLista();
        }).fail(function(){
            $('#modalTitulo').text('Erro');
            $('#modalConteudo').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar dados</td></tr>');
        });
    });

    // Calendário
    var calendarEl = document.getElementById('calendario');
    var calendar = new FullCalendar.Calendar(calendarEl,{
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

    // Bloqueio botão voltar
    history.pushState(null,null,location.href);
    window.onpopstate=function(){ history.go(1); };
});
</script>
