<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    header('Location: ../login.php');
    exit;
}
include_once('head/headDocente.php');
?>

<!-- Conteúdo principal -->
<div id="layoutSidenav_content">
    <main class="py-4" style="min-height: 100vh; background-color: #f4f7fa;">
        <div class="container px-4" style="max-width: 1200px; width: 100%; font-size: 1.05rem;">
            
         <!-- Painel de Boas-Vindas -->
<div class="bg-primary text-white p-4 rounded shadow text-center mb-4" style="width: 100%; margin-left: 0;">
    <h4><strong>Bem-vindo ao Painel do Docente!</strong></h4>
    <p>Acompanhe os projetos que você supervisiona e os prazos de entrega.</p>
</div>



           <!-- Layout principal -->
<div class="row g-3 mb-4">
    <!-- Coluna esquerda: Gráfico -->
    <div class="col-md-9">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-chart-bar"></i> Status dos Projetos
            </div>
            <div class="card-body bg-white text-dark">
                <canvas id="statusChartDocente" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Coluna direita: Indicadores -->
    <div class="col-md-3 d-flex flex-column gap-2">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <?php
        $cards = [
            ['id' => 'emAndamento', 'bg' => '#ffc107', 'icon' => 'hourglass-split', 'label' => 'Em Andamento'],
            ['id' => 'concluidos', 'bg' => '#198754', 'icon' => 'check-circle-fill', 'label' => 'Concluídos'],
            ['id' => 'atrasados', 'bg' => '#dc3545', 'icon' => 'exclamation-triangle-fill', 'label' => 'Atrasados'],
            ['id' => 'emFalta', 'bg' => '#6c757d', 'icon' => 'x-circle-fill', 'label' => 'Em Falta'],
            ['id' => 'total', 'bg' => '#0d6efd', 'icon' => 'collection-fill', 'label' => 'Total']
        ];

        foreach ($cards as $c) {
            echo <<<HTML
            <div class="card text-center shadow-sm text-white" style="background-color: {$c['bg']}; padding: 0rem;">
                <div class="card-body py-1" style="padding: 0.5rem;">
                    <i class="bi bi-{$c['icon']}" style="font-size: 1.2rem;"></i>
                    <h6 class="mt-1" style="font-size: 0.75rem;">{$c['label']}</h6>
                    <h4 id="{$c['id']}" style="font-size: 1rem; margin: 0;">0</h4>
                </div>
            </div>
            HTML;
        }
        ?>
    </div>
</div> <!-- fecha .row g-3 mb-4 -->
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
    // Carregar dados resumo + gráfico
    $.getJSON('dados_resumo.php', function (data) {
        if (data && !data.erro) {
            $('#emAndamento').text(data.emAndamento);
            $('#concluidos').text(data.concluidos);
            $('#atrasados').text(data.atrasados);
            $('#emFalta').text(data.emFalta);
            $('#total').text(data.total);

            const ctx = document.getElementById('statusChartDocente').getContext('2d');
            if (window.statusChartDocente instanceof Chart) window.statusChartDocente.destroy();

            window.statusChartDocente = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Em Andamento', 'Concluídos', 'Atrasados', 'Em Falta', 'Total'],
                    datasets: [{
                        label: 'Projetos',
                        data: [
                            data.emAndamento,
                            data.concluidos,
                            data.atrasados,
                            data.emFalta,
                            data.total
                        ],
                        backgroundColor: [
                            '#ffc107',
                            '#198754',
                            '#dc3545',
                            '#6c757d',
                            '#0d6efd'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        } else {
            alert("Erro ao carregar dados.");
        }
    });

    // Calendário
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendario');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            height: 500,
            events: '../controlador/eventos_docente.php',
            eventDidMount: function (info) {
                let diasTexto = '';
                if (typeof info.event.extendedProps.diasRestantes !== 'undefined') {
                    const dias = parseInt(info.event.extendedProps.diasRestantes);
                    if (dias > 1) {
                        diasTexto = `Dias restantes: ${dias}`;
                    } else if (dias === 1) {
                        diasTexto = 'Falta 1 dia!';
                    } else if (dias === 0) {
                        diasTexto = 'Entrega hoje!';
                    } else if (dias < 0) {
                        diasTexto = `⚠️ ${Math.abs(dias)} dias de atraso`;
                    }
                }

                let title = `Projeto: ${info.event.title}\n${diasTexto}`;
                if (info.event.extendedProps.status) {
                    title += `\nStatus: ${info.event.extendedProps.status}`;
                }

                new bootstrap.Tooltip(info.el, {
                    title: title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });

                if (info.event.extendedProps.vencido === true) {
                    info.el.style.backgroundColor = '#dc3545';
                    info.el.style.borderColor = '#dc3545';
                }
            }
        });
        calendar.render();
    });
</script>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>  
