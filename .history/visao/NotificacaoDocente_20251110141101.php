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

try {
    $conn = $crud->getConexao();

    // Contar notificações não lidas
    $stmtNaoLidas = $conn->prepare("
        SELECT COUNT(*) as total_nao_lidas
        FROM notificacoes
        WHERE docente_id = ?
          AND estudante_id IS NOT NULL
          AND (LOWER(status) = 'não lida' OR LOWER(status) = 'nao lida')
          AND mensagem LIKE '%submeteu o projeto%'
    ");
    $stmtNaoLidas->execute([$docente_id]);
    $totalNaoLidas = $stmtNaoLidas->fetch(PDO::FETCH_ASSOC)['total_nao_lidas'] ?? 0;

} catch (PDOException $e) {
    $totalNaoLidas = 0;
}

// Paginação
$limite = 10;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

// Filtros
$statusFiltro = isset($_GET['status']) ? $_GET['status'] : 'todos';
$searchProjeto = isset($_GET['projeto']) ? trim($_GET['projeto']) : '';

$whereClauses = ["n.docente_id = :docente_id", "n.estudante_id IS NOT NULL", "n.mensagem LIKE '%submeteu o projeto%'"];
$params = ['docente_id' => $docente_id];

// Filtro por status
if ($statusFiltro === 'concluido') {
    $whereClauses[] = "LOWER(n.status) = 'concluído'";
} elseif ($statusFiltro === 'em_andamento') {
    $whereClauses[] = "LOWER(n.status) = 'em andamento'";
} elseif ($statusFiltro === 'atrasado') {
    $whereClauses[] = "LOWER(n.status) = 'atrasado'";
} elseif ($statusFiltro === 'nao_lida') {
    $whereClauses[] = "(LOWER(n.status) = 'não lida' OR LOWER(n.status) = 'nao lida')";
}

// Pesquisa projeto
if ($searchProjeto !== '') {
    $whereClauses[] = "p.titulo LIKE :projeto";
    $params['projeto'] = "%$searchProjeto%";
}

$whereSQL = implode(" AND ", $whereClauses);

try {
    // Contar total de notificações
    $stmtCount = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM notificacoes n
        JOIN projectos p ON p.id = n.projeto_id
        JOIN usuarios u ON u.id = n.estudante_id
        WHERE $whereSQL
    ");
    $stmtCount->execute($params);
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
        WHERE $whereSQL
        ORDER BY n.data_envio DESC
        LIMIT :limite OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
                    <div class="card-header bg-primary text-white text-center d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-bell"></i> Notificações de Submissão</h4>
                        <?php if ($totalNaoLidas > 0): ?>
                            <span class="badge bg-danger rounded-pill px-3 py-2"><?= $totalNaoLidas ?> não lida(s)</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">

                        <!-- Filtros e Pesquisa -->
                        <form method="GET" class="row g-2 mb-3 align-items-center">
                            <div class="col-auto">
                                <select name="status" class="form-select">
                                    <option value="todos" <?= $statusFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
                                    <option value="concluido" <?= $statusFiltro === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                    <option value="em_andamento" <?= $statusFiltro === 'em_andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                    <option value="atrasado" <?= $statusFiltro === 'atrasado' ? 'selected' : '' ?>>Atrasado</option>
                                    <option value="nao_lida" <?= $statusFiltro === 'nao_lida' ? 'selected' : '' ?>>Não Lida</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <input type="text" name="projeto" class="form-control" placeholder="Pesquisar projeto" value="<?= htmlspecialchars($searchProjeto) ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                            </div>
                        </form>

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
                                        <?php foreach ($notificacoes as $n): 
                                            $isNaoLida = strtolower($n['status']) === 'não lida' || strtolower($n['status']) === 'nao lida';
                                        ?>
                                            <tr class="<?= $isNaoLida ? 'table-warning fw-bold' : '' ?>">
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
                                                    <?php if ($isNaoLida): ?>
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
                                                <a class="page-link" href="?pagina=<?= $i ?>&status=<?= $statusFiltro ?>&projeto=<?= urlencode($searchProjeto) ?>"><?= $i ?></a>
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

    .table-warning {
        background-color: #fff3cd !important;
    }

    .fw-bold {
        font-weight: 600;
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

    .badge {
        font-size: 0.8rem;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

