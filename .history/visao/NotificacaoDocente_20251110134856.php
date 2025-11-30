<?php  
session_start();

if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Admin') {
    include_once('head/Admin.php');
} else {
    include_once('head/headDocente.php');
}

require_once '../modelo/crud.php';

$crud = new crud();
$docente_id = $_SESSION['id'];

// Paginação
$limite = 10; // notificações por página
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

try {
    $conn = $crud->getConexao();

    // Contar total de notificações
    $stmtCount = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM notificacoes n
        WHERE n.docente_id = ?
        AND n.estudante_id IS NOT NULL
        AND n.mensagem LIKE '%submeteu o projeto%'
    ");
    $stmtCount->execute([$docente_id]);
    $totalNotificacoes = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPaginas = ceil($totalNotificacoes / $limite);

    // Buscar notificações paginadas
    $stmt = $conn->prepare("
        SELECT 
            n.data_envio, 
            n.mensagem, 
            u.nome AS estudante_nome,
            p.titulo AS titulo_projeto,
            n.status
        FROM notificacoes n
        JOIN usuarios u ON u.id = n.estudante_id
        JOIN projectos p ON p.id = n.projeto_id
        WHERE n.docente_id = ?
          AND n.estudante_id IS NOT NULL
          AND n.mensagem LIKE '%submeteu o projeto%'
        ORDER BY n.data_envio DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $docente_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limite, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar notificações: " . $e->getMessage();
}
?>

<div id="layoutSidenav_content">
    <main class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="fas fa-bell"></i> Notificações de Submissão</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($notificacoes)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Projeto</th>
                                            <th>Estudante</th>
                                            <th>Mensagem</th>
                                            <th>Status</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notificacoes as $n): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($n['data_envio'])) ?></td>
                                                <td><?= htmlspecialchars($n['titulo_projeto']) ?></td>
                                                <td><?= htmlspecialchars($n['estudante_nome']) ?></td>
                                                <td class="text-break"><?= htmlspecialchars($n['mensagem']) ?></td>
                                                <td>
                                                    <?php 
                                                        $status = $n['status'] ?? 'Indefinido';
                                                        $class = match(strtolower($status)) {
                                                            'concluído' => 'badge bg-success',
                                                            'atrasado' => 'badge bg-danger',
                                                            'em andamento' => 'badge bg-warning text-dark',
                                                            'lida' => 'badge bg-success',
                                                            'não lida', 'nao lida' => 'badge bg-secondary',
                                                            default => 'badge bg-secondary',
                                                        };
                                                        echo "<span class='$class'>" . ucfirst($status) . "</span>";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (strtolower($n['status']) === 'não lida' || strtolower($n['status']) === 'nao lida'): ?>
                                                        <a href="../Controlo/MarcarLida.php?data=<?= urlencode($n['data_envio']) ?>&docente=<?= $docente_id ?>&projeto=<?= urlencode($n['titulo_projeto']) ?>" class="btn btn-sm btn-outline-success" title="Marcar como lida">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-success"><i class="fas fa-check-circle"></i></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginação -->
                            <?php if ($totalPaginas > 1): ?>
                                <nav aria-label="Paginação">
                                    <ul class="pagination justify-content-center mt-3">
                                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                Nenhuma notificação encontrada.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-light text-center text-muted py-3 mt-auto border-top">
        <?php include_once('../visao/Rodape.php'); ?>
    </footer>
</div>

<!-- Estilos adicionais -->
<style>
    html, body {
        height: 100%;
        margin: 0;
        background-color: #f8f9fa;
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

    table td {
        word-break: break-word;
        max-width: 300px;
    }

    .table-hover tbody tr:hover {
        background-color: #e9f5ff;
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

<!-- Scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
