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

try {
    $conn = $crud->getConexao();

    // 🔹 Contador de notificações não lidas
    $stmtNaoLidas = $conn->prepare("
        SELECT COUNT(*) as total_nao_lidas
        FROM notificacoes n
        INNER JOIN grupo g ON g.projeto_id = n.projeto_id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        WHERE ge.estudante_id = :estudante_id
          AND (LOWER(n.status) = 'não lida' OR LOWER(n.status) = 'nao lida')
          AND n.mensagem LIKE 'Você foi atribuído%'
    ");
    $stmtNaoLidas->execute(['estudante_id' => $estudante_id]);
    $totalNaoLidas = $stmtNaoLidas->fetch(PDO::FETCH_ASSOC)['total_nao_lidas'] ?? 0;

} catch (PDOException $e) {
    $totalNaoLidas = 0;
}

// ------------------- Paginação e filtros -------------------
$limite = 10;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

$statusFiltro = $_GET['status'] ?? 'todos';
$searchProjeto = trim($_GET['projeto'] ?? '');

$whereClauses = ["ge.estudante_id = :estudante_id", "n.mensagem LIKE 'Você foi atribuído%'"];
$params = ['estudante_id' => $estudante_id];

// Status
if ($statusFiltro === 'lida') {
    $whereClauses[] = "LOWER(n.status) = 'lida'";
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
    // Total de notificações
    $stmtCount = $conn->prepare("
        SELECT COUNT(*) as total
        FROM notificacoes n
        INNER JOIN projectos p ON p.id = n.projeto_id
        INNER JOIN usuarios d ON d.id = p.docente_id
        INNER JOIN grupo g ON g.projeto_id = p.id
        INNER JOIN grupo_estudante ge ON ge.grupo_id = g.id
        WHERE $whereSQL
    ");
    $stmtCount->execute($params);
    $totalNotificacoes = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $totalPaginas = ceil($totalNotificacoes / $limite);

    // Buscar notificações
    $sql = "
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
        WHERE $whereSQL
        ORDER BY n.data_envio DESC
        LIMIT :limite OFFSET :offset
    ";
    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    $notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro ao buscar notificações: " . $e->getMessage();
}
?>

<!-- Sino com contador -->
<ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
    <li class="nav-item dropdown">
        <a class="nav-link position-relative" href="NotificacaoEstudante.php">
            <i class="fas fa-bell text-warning"></i>
            <?php if ($totalNaoLidas > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $totalNaoLidas ?>
                    <span class="visually-hidden">novas notificações</span>
                </span>
            <?php endif; ?>
        </a>
    </li>
</ul>

<!-- Resto da página (tabela de notificações, filtros e paginação) -->



