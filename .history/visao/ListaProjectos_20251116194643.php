<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

require_once('../Modelo/VerProjectos.php');
require_once('../Modelo/submissoes.php');

$projectoDAO = new VerProjectos();
$submissoesDAO = new submissoes();

if ($_SESSION['tipo'] === 'Admin') {
    include_once('head/Admin.php');
    $submissoes = $submissoesDAO->getSubmissoesParaAdmin();
    $projectos = $projectoDAO->getTodosProjetos();
} else {
    include_once('head/headDocente.php');
    $submissoes = $submissoesDAO->getSubmissoesParaDocente($_SESSION['id']);
    $projectos = $projectoDAO->getProjetosPorDocente($_SESSION['id']);
}

// Normalizar os IDs para sempre usar $p['id']
foreach ($projectos as &$p) {
    if (isset($p['Id']) && !isset($p['id'])) {
        $p['id'] = $p['Id'];
    }
}
?>

<div id="layoutSidenav_content">
<main class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Projetos</h5>
        </div>
        <div class="card-body">
            
            <!-- MENSAGEM DE SUCESSO OU ERRO -->
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['mensagem']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['mensagem']); ?>
            <?php endif; ?>

            <!-- Abas -->
            <ul class="nav nav-tabs mb-4" id="abasProjetos" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista" type="button" role="tab">Lista de Projetos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="submetidos-tab" data-bs-toggle="tab" data-bs-target="#submetidos" type="button" role="tab">Projetos Submetidos</button>
                </li>
            </ul>

            <div class="tab-content" id="conteudoAbas">

                <!-- TAB 1 - Lista de Projetos -->
                <div class="tab-pane fade show active" id="lista" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center tabela-projetos">
                            <thead class="table-dark">
                                <tr>
                                    <th>N.º</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Grupo</th>
                                    <th>Prazo</th>
                                    <th>Arquivo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="projectosTableBody">
                                <!-- Projetos serão inseridos aqui via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2 - Projetos Submetidos -->
                <div class="tab-pane fade" id="submetidos" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center tabela-projetos">
                            <thead class="table-dark">
                                <tr>
                                    <th>N.º</th>
                                    <th>Enviado por:</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Grupo</th>
                                    <th>Data de Submissão</th>
                                    <th>Status</th>
                                    <th>Arquivo</th>
                                    <th>Comentário</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $j = 1; foreach ($submissoes as $s): ?>
                                    <tr>
                                        <td><?= $j++ ?></td>
                                        <td><?= htmlspecialchars($s['estudante_nome'] ?? 'Desconhecido') ?></td>
                                        <td><?= htmlspecialchars($s['titulo']) ?></td>
                                        <td><?= htmlspecialchars($s['descricao']) ?></td>
                                        <td><?= !empty($s['estudantes']) ? htmlspecialchars($s['estudantes']) : '<span class="text-muted">-</span>' ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($s['data_submissao'])) ?></td>
                                        <td>
                                            <?php if ($s['estatus'] == 'concluido'): ?>
                                                <span class="badge bg-success">Concluído</span>
                                            <?php elseif ($s['estatus'] == 'emAndamento'): ?>
                                                <span class="badge bg-warning text-dark">Em andamento</span>
                                            <?php elseif ($s['estatus'] == 'atrasado'): ?>
                                                <span class="badge bg-danger">Atrasado</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                      <td>
                                          <?php if (!empty($s['arquivo'])): ?>
                                              <a href="../uploads/<?= rawurlencode($s['arquivo']) ?>" download class="btn btn-success btn-sm">
                                                  <i class="fas fa-download"></i> Download
                                              </a>
                                          <?php else: ?>
                                              <span class="text-muted">Nenhum</span>
                                          <?php endif; ?>
                                      </td>

                                        <td>
                                            <?= !empty($s['feedback']) ? htmlspecialchars($s['feedback']) : '<span class="text-muted">Nenhum</span>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- tab-content -->
        </div> <!-- card-body -->
    </div> <!-- card -->
</main>
<?php include_once('../visao/Rodape.php'); ?>
</div>

<!-- Font Awesome (caso ainda não esteja incluso) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    // Função para atualizar a tabela de projetos via AJAX
    function atualizarTabelaProjetos() {
      $.ajax({
        url: 'recuperarProjetos.php', // Arquivo que irá retornar a lista de projetos
        method: 'GET',
        success: function(data) {
          $('#projectosTableBody').html(data); // Substitui a tabela com a nova lista
        }
      });
    }

    // Chama a função para carregar os projetos na inicialização da página
    atualizarTabelaProjetos();
  });
</script>
