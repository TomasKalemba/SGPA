<?php
session_start();
require_once '../Modelo/VerProjectos.php';

$projectoDAO = new VerProjectos();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    die("Acesso negado.");
}

$usuarioId = $_SESSION['id'];
$tipoUsuario = $_SESSION['tipo'];
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Resultados da Pesquisa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: scale(1.02);
}
.card-body p {
    margin-bottom: 6px;
    font-size: 0.95rem;
}
.card-title {
    font-size: 1.1rem;
    color: #333;
}
h2, h4 {
    color: #444;
}
</style>

</head>
<body class="bg-light">
    <div class="container py-4" style="max-width: 900px;">

        <h2 class="text-center mb-5">Resultados da Pesquisa por: <em><?= htmlspecialchars($query) ?></em></h2>

        <?php if ($query !== ''): ?>

            <!-- Projetos -->
            <?php
            $projetosEncontrados = $projectoDAO->buscarProjetosComEstudantesPorUsuario($query, $tipoUsuario, $usuarioId);

            if (count($projetosEncontrados) > 0): ?>
               <h4 class="mt-4 mb-3 border-bottom pb-2">Submissões Encontradas</h4>

                <div class="row justify-content-center mb-5">
                    <?php foreach ($projetosEncontrados as $row): ?>
                        <div class="col-md-6 col-lg-5">
                            <div class="card mb-3 border-0 shadow-sm rounded-3" style="background-color: #fdfdfd;">

                                <div class="card-body">
                                    <h5 class="card-title"><strong>Título:</strong> <?= htmlspecialchars($row['titulo']) ?></h5>
                                    <p><strong>Descrição:</strong> <?= htmlspecialchars($row['descricao']) ?></p>
                                    <p><strong>Docente:</strong> <?= htmlspecialchars($row['docente_id']) ?></p>
                                    <p><strong>Data de Criação:</strong> <?= htmlspecialchars($row['data_criacao']) ?></p>
                                    <p><strong>Prazo:</strong> <?= htmlspecialchars($row['prazo']) ?></p>
                                    <p><strong>Feedback:</strong> <?= htmlspecialchars($row['feedback']) ?></p>
                                    <p><strong>Estudantes:</strong> <?= htmlspecialchars($row['estudantes']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">Nenhum projeto encontrado.</p>
            <?php endif; ?>

            <!-- Submissões -->
            <?php
            try {
                $conn = new PDO("mysql:host=localhost;dbname=sgpa", "root", "");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql_submissoes = "SELECT s.*, u.nome AS estudante_nome
                                   FROM submisoes s
                                   INNER JOIN usuarios u ON u.id = s.estudante_id
                                   WHERE s.titulo LIKE :query";

                if ($tipoUsuario === 'Estudante') {
                    $sql_submissoes .= " AND s.estudante_id = :usuarioId";
                }

                $stmt_submissoes = $conn->prepare($sql_submissoes);
                $searchTerm = '%' . $query . '%';
                $stmt_submissoes->bindParam(':query', $searchTerm, PDO::PARAM_STR);
                if ($tipoUsuario === 'Estudante') {
                    $stmt_submissoes->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
                }
                $stmt_submissoes->execute();

                if ($stmt_submissoes->rowCount() > 0): ?>
                   <h4 class="mt-4 mb-3 border-bottom pb-2">Submissões Encontradas</h4>

                    <div class="row justify-content-center">
                        <?php while ($row = $stmt_submissoes->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="col-md-6 col-lg-5">
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><strong>Título:</strong> <?= htmlspecialchars($row['titulo']) ?></h5>
                                        <p><strong>Descrição:</strong> <?= htmlspecialchars($row['descricao']) ?></p>
                                        <p><strong>Estudante:</strong> <?= htmlspecialchars($row['estudante_nome']) ?></p>
                                        <p><strong>Data de Submissão:</strong> <?= htmlspecialchars($row['data_submissao']) ?></p>
                                        <p><strong>Status:</strong> <?= htmlspecialchars($row['estatus']) ?></p>
                                        <p><strong>Feedback:</strong> <?= htmlspecialchars($row['feedback']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma submissão encontrada.</p>
                <?php endif;

            } catch (PDOException $e) {
                echo "<p class='text-danger'>Erro ao buscar submissões: " . $e->getMessage() . "</p>";
            }

        else: ?>
            <p class="text-muted text-center">Digite um termo na barra de pesquisa para ver os resultados.</p>
        <?php endif; ?>
    </div>
</body>
</html>
