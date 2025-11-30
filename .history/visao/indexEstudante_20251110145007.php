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
        <div class="card text-center shadow-sm text-white" style="background-color: #0d6efd;">
            <div class="card-body">
                <i class="bi bi-hourglass-split fs-2"></i>
                <h6 class="text-white mt-2">Em Andamento</h6>
                <h2 id="emAndamento">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #198754;">
            <div class="card-body">
                <i class="bi bi-check-circle-fill fs-2"></i>
                <h6 class="text-white mt-2">Concluídos</h6>
                <h2 id="concluidos">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #dc3545;">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                <h6 class="text-white mt-2">Atrasados</h6>
                <h2 id="atrasados">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #6c757d;">
            <div class="card-body">
                <i class="bi bi-x-circle-fill fs-2"></i>
                <h6 class="text-white mt-2">Em Falta</h6>
                <h2 id="emFalta">0</h2>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-center shadow-sm text-white" style="background-color: #ffc107;">
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

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Indicadores e gráfico de status
    $.getJSON('dados_resumo_estudante.php', function(data) {
        if (data && !data.erro) {
            $('#emAndamento').text(data.emAndamento);
            $('#concluidos').text(data.concluidos);
            $('#atrasados').text(data.atrasados);
            $('#emFalta').text(data.emFalta);
            $('#totalProjetos').text(data.totalProjetos);

            atualizarGraficoStatus(data);
        } else {
            alert("Erro ao carregar os dados.");
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
                    data: [
                        data.emAndamento,
                        data.concluidos,
                        data.atrasados,
                        data.emFalta,
                        data.totalProjetos
                    ],
                    backgroundColor: [
                        '#0d6efd', // Azul
                        '#198754', // Verde
                        '#dc3545', // Vermelho
                        '#6c757d', // Cinza escuro
                        '#ffc107'  // Amarelo
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Quantidade de Projetos' }
                    }
                }
            }
        });
    }

    // Gráfico de progresso mensal
$.getJSON('dados_progresso_estudante.php', function (data) {
    if (data && !data.erro) {
        const ctx = document.getElementById('progressChart').getContext('2d');
        if (window.progressChart instanceof Chart) window.progressChart.destroy();

        const meses = Object.keys(data);
        const valores = Object.values(data);

        window.progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Projetos com Prazo',
                    data: valores,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolução Mensal dos Projetos com Prazo',
                        font: { size: 18 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `Projetos: ${context.raw}`;
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantidade de Projetos'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses'
                        }
                    }
                }
            }
        });
    } else {
        alert("Erro ao carregar gráfico de progresso.");
    }
});

    // Calendário com tooltip
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendario');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            height: 500,
            events: 'eventos.php',
            eventColor: '#0d6efd',
            noEventsContent: 'Nenhum prazo disponível',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            eventDidMount: function(info) {
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

                let title = `Status: ${info.event.extendedProps.status || 'Desconhecido'}\n${diasTexto}`;
                if (info.event.extendedProps.feedback) {
                    title += `\nFeedback: ${info.event.extendedProps.feedback}`;
                }

                if (info.event.extendedProps.vencido === true) {
                    info.el.style.backgroundColor = '#dc3545';
                    info.el.style.borderColor = '#dc3545';
                } else if (info.event.extendedProps.alerta === true) {
                    info.el.style.backgroundColor = '#fd7e14';
                    info.el.style.borderColor = '#fd7e14';
                }

                new bootstrap.Tooltip(info.el, {
                    title: title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
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