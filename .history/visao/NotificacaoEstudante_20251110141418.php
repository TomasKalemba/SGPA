<?php  
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Estudante') {
    header("Location: ../index.php");
    exit;
}

include_once('head/Estudante.php');
require_once '../modelo/crud.php';

$crud = new crud();
$estudante_id = $_SESSION['id'];

// Paginação
$limite = 10;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

try {
    $conn = $crud->getConexao();

    // Contar total de notificações
    $stmtCount = $conn->prepare("
        SELECT COUNT(*) as total
        FROM notificacoes n
        INNER JOIN projectos p ON p.id = n.projeto_id
        INNER JOIN grupo g ON g.projeto_id = p.id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        WHERE ge.estudante_id = ? 
          AND n.estudante_id = ?
          AND n.mensagem LIKE 'Você foi atribuído%'
    ");
    $stmtCount->execute([$estudante_id, $estudante_id]);
    $totalNotificacoes = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPaginas = ceil($totalNotificacoes / $limite);

    // Buscar notificações paginadas
    $stmt = $conn->prepare("
        SELECT 
            n.id,
            n.data_envio, 
            n.mensagem, 
            d.nome AS docente_nome,
            p.titulo AS titulo_projeto,
            n.status
        FROM notificacoes n
        INNER JOIN projectos p ON p.id = n.projeto_id
        INNER JOIN usuarios d ON d.id = p.docente_id
        INNER JOIN grupo g ON g.projeto_id = p.id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        WHERE ge.estudante_id = ? 
          AND n.estudante_id = ?
          AND n.mensagem LIKE 'Você foi atribuído%'
        ORDER BY n.data_envio DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $estudante_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $estudante_id, PDO::PARAM_INT);
    $stmt->bindValue(3, $limite, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
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
                    <div class="card-header bg-info text-white text-center">
                        <h4 class="mb-0"><i class="fas fa-bell"></i> Minhas Notificações</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($notificacoes)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Projeto</th>
                                            <th>Docente</th>
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
                                                <td><?= htmlspecialchars($n['docente_nome']) ?></td>
                                                <td class="text-break"><?= htmlspecialchars($n['mensagem']) ?></td>
                                                <td>
                                                    <?php 
                                                        $status = strtolower($n['status']) === 'lida' ? 'Lida' : 'Não lida';
                                                        $class = $status === 'Lida' ? 'badge bg-success' : 'badge bg-danger';
                                                        echo "<span class='$class'>$status</span>";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (strtolower($n['status']) !== 'lida'): ?>
                                                        <a href="../Controlo/marcar_lida_estudante.php?id=<?= $n['id'] ?>" 
                                                           class="btn btn-sm btn-outline-success" title="Marcar como lida">
                                                            <i class="fas fa-check-circle"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">✔</span>
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
                                Nenhuma notificação recebida ainda.
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
        background-color: #e0f7fa;
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>


