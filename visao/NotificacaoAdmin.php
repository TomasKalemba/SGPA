<?php
session_start();

if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Admin') {
    include_once('head/Admin.php');
} else {
    header('Location: ../visao/login.php');
    exit;
}

require_once '../modelo/crud.php';
$crud = new crud();
$docentesPendentes = [];
$totalPendentes = 0;

try {
    $conn = $crud->getConexao();
    $stmt = $conn->prepare("
        SELECT id, nome, email 
        FROM usuarios 
        WHERE tipo = 'Docente' AND ativo = 0
        ORDER BY nome ASC
    ");
    $stmt->execute();
    $docentesPendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPendentes = count($docentesPendentes);
} catch (PDOException $e) {
    echo "Erro ao buscar docentes pendentes: " . $e->getMessage();
}
?>

<!-- Conteúdo principal -->
<div id="layoutSidenav_content">
    <main class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white text-center">
                        <h4><i class="fas fa-user-clock"></i> Notificações de Docentes Pendentes 
                            <span class="badge bg-light text-dark"><?= $totalPendentes ?></span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($totalPendentes > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($docentesPendentes as $docente): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($docente['nome']) ?></td>
                                                <td><?= htmlspecialchars($docente['email']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                Nenhum docente pendente de ativação.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <footer class="bg-light text-center text-muted py-3 mt-auto border-top">
        <?php include_once('../visao/Rodape.php'); ?>
    </footer>
</div>

<!-- Estilo adicional -->
<style>
    html, body {
        height: 100%;
        margin: 0;
        background-color: #f4f7fc;
        display: flex;
        flex-direction: column;
    }

    #layoutSidenav_content {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }

    main.container-fluid {
        flex: 1;
    }

    footer {
        flex-shrink: 0;
        width: 100%;
    }

    table td {
        word-break: break-word;
        max-width: 300px;
    }

    @media (max-width: 768px) {
        .card-header h4 {
            font-size: 1.2rem;
        }

        .table thead {
            font-size: 0.9rem;
        }

        .table td, .table th {
            font-size: 0.85rem;
        }
    }
</style>
<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
