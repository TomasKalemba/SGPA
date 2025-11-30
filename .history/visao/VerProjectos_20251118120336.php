<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: ../visao/login.php");
    exit;
}

require_once('../Modelo/VerProjectos.php');
require_once('../Modelo/submissoes.php');

$projectoDAO   = new VerProjectos();
$submissoesDAO = new submissoes();

$tipoUsuario = $_SESSION['tipo'] ?? '';
$ehAdmin     = ($tipoUsuario === 'Admin');

// Cabeçalho + dados conforme o tipo
if ($tipoUsuario === 'Estudante') {
    include_once('head/Estudante.php');

    // Projetos atribuídos ao estudante
    $projectosPendentes = $projectoDAO->getProjetosPorEstudante($_SESSION['id']);

    // Submissões feitas pelo grupo do estudante
    $submissoes = $submissoesDAO->getSubmissoesRelacionadasAoGrupo($_SESSION['id']);

} elseif ($ehAdmin) {
    include_once('head/Admin.php');

    // Admin vê tudo
    $projectosPendentes = $projectoDAO->getTodosProjetos();
    $submissoes         = $submissoesDAO->getSubmissoesParaAdmin();

} else {
    header("Location: ../visao/login.php");
    exit;
}

// Garante que sejam arrays, evitando warnings
$projectosPendentes = is_array($projectosPendentes) ? $projectosPendentes : [];
$submissoes         = is_array($submissoes) ? $submissoes : [];
?>

<!-- Estilos para a tabela igual à do primeiro código -->
<style>
body {
    background-color: #f5f7fa !important;
}

h2, h4, h5 {
    font-weight: 600;
    color: #2c3e50;
}

.card.shadow {
    background: #fff;
    border-radius: 12px;
    border: none;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

table.dataTable tbody td {
    padding: 6px 10px !important;
    font-size: 14px;
}

table.dataTable thead th {
    padding: 8px 10px !important;
    font-size: 14px;
    background: linear-gradient(to right, #4e73df, #224abe);
    color: #fff;
}

.table tbody tr:hover {
    background-color: #eef3ff;
}

.btn-danger {
    background-color: #e74a3b;
    border: none;
}

.btn-danger:hover {
    background-color: #c0392b;
}

.alert {
    border-radius: 10px;
    font-weight: 500;
}
</style>

<div id="layoutSidenav_content">
<main class="container py-4">
    <div id="mensagemSucessoContainer"></div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Meus Projetos</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="abasProjetos" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pendentes-tab" data-bs-toggle="tab" data-bs-target="#pendentes" type="button" role="tab">Projetos Pendentes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="enviados-tab" data-bs-toggle="tab" data-bs-target="#enviados" type="button" role="tab">Projetos Enviados</button>
                </li>
            </ul>

            <div class="tab-content" id="conteudoAbas">

                <!-- TAB 1 - PENDENTES -->
                <div class="tab-pane fade show active" id="pendentes" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center tabela-projetos" id="tabelaPendentes">
                            <thead>
                                <tr>
                                    <th>N</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Docente</th>
                                    <th>Grupo</th>
                                    <th>Prazo</th>
                                    <th>Arquivo</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($projectosPendentes as $p): ?>
                                    <?php
                                        $pid = $p['id'] ?? $p['Id'] ?? null;
                                        if ($pid === null) continue;

                                        $mostrarLinha = true;
                                        if (!$ehAdmin) {
                                            $submetido   = $submissoesDAO->jaExisteSubmissaoPorGrupo($_SESSION['id'], $pid);
                                            $mostrarLinha = !$submetido;
                                        }
                                        if (!$mostrarLinha) continue;
                                    ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($p['titulo'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($p['descricao'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($p['docente_nome'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($p['estudantes'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($p['prazo'])): ?>
                                                <?= date('d/m/Y H:i:s', strtotime($p['prazo'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($p['arquivo'])): ?>
                                                <a href="download.php?file=<?= urlencode($p['arquivo']) ?>" class="btn btn-success btn-sm" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Nenhum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-secondary">Pendente</span></td>
                                        <td>
                                            <?php if (!$ehAdmin): ?>
                                                <button class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEnviar"
                                                        data-id="<?= (int)$pid ?>"
                                                        data-titulo="<?= htmlspecialchars($p['titulo'] ?? '') ?>"
                                                        title="Enviar">
                                                    Enviar
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2 - ENVIADOS -->
                <div class="tab-pane fade show" id="enviados" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center tabela-projetos" id="tabelaEnviados">
                            <thead>
                                <tr>
                                    <th>N</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Docente</th>
                                    <th>Grupo</th>
                                    <th>Enviado por:</th>
                                    <th>Data de Submissão</th>
                                    <th>Status</th>
                                    <th>Arquivo</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $j = 1; ?>
                                <?php foreach ($submissoes as $s): ?>
                                    <tr>
                                        <td><?= $j++ ?></td>
                                        <td><?= htmlspecialchars($s['titulo'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($s['descricao'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($s['docente_nome'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($s['estudantes'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($s['estudante_nome'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($s['data_submissao'])): ?>
                                                <?= date('d/m/Y H:i:s', strtotime($s['data_submissao'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-success">Enviado</span></td>
                                        <td>
                                            <?php if (!empty($s['arquivo'])): ?>
                                                <a href="download.php?file=<?= urlencode($s['arquivo']) ?>" class="btn btn-success btn-sm" title="Download">
                                                    <i class="fas fa-download"></i>
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

            </div>
        </div>
    </div>
</main>
<?php include_once('../visao/Rodape.php'); ?>
</div>

<!-- Modal de Envio -->
<div class="modal fade" id="modalEnviar" tabindex="-1" aria-labelledby="modalEnviarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEnviarLabel">Enviar Projeto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form id="formEnvioProjeto" enctype="multipart/form-data">
          <input type="hidden" name="projeto_id" id="modal_projeto_id">

          <div class="mb-3">
            <label for="arquivo" class="form-label"><i class="fas fa-file-upload"></i> Selecionar Arquivo</label>
            <input class="form-control" type="file" name="arquivo" required>
          </div>
          <div class="mb-3">
            <label for="estatus" class="form-label"><i class="fas fa-tasks"></i> Status do Projeto</label>
            <select class="form-select" name="estatus" required>
              <option value="emAndamento">Em andamento</option>
              <option value="concluido">Concluído</option>
              <option value="atrasado">Atrasado</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="feedback" class="form-label"><i class="fas fa-comments"></i> Comentários ou Observações</label>
            <textarea class="form-control" name="feedback" rows="3" placeholder="Opcional"></textarea>
          </div>
          <div id="statusEnvio" class="mb-2 text-danger"></div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modalEnviar = document.getElementById('modalEnviar');
  modalEnviar.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const projetoId = button.getAttribute('data-id');
    const titulo = button.getAttribute('data-titulo');

    document.getElementById('modal_projeto_id').value = projetoId;
    document.getElementById('modalEnviarLabel').innerText = `Enviar Projeto: ${titulo}`;
    document.getElementById('statusEnvio').innerText = "";
  });

  document.getElementById('formEnvioProjeto').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    const response = await fetch('../controlo/EnviarProjecto.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.text();
    if (result.trim().toLowerCase().includes('sucesso')) {
      sessionStorage.setItem('mensagem_sucesso', 'Projeto enviado com sucesso!');
      location.reload();
    } else {
      document.getElementById('statusEnvio').innerText = result;
    }
  });

  window.addEventListener('DOMContentLoaded', () => {
    const mensagem = sessionStorage.getItem('mensagem_sucesso');
    if (mensagem) {
      const alerta = document.createElement('div');
      alerta.className = 'alert alert-success alert-dismissible fade show';
      alerta.role = 'alert';
      alerta.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      `;
      document.getElementById('mensagemSucessoContainer').appendChild(alerta);
      sessionStorage.removeItem('mensagem_sucesso');
    }

    // Inicializa DataTables com botões e linguagem PT-BR
    $('.tabela-projetos').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'excelHtml5', title: 'Projetos' },
        { extend: 'pdfHtml5', title: 'Projetos', orientation: 'landscape', pageSize: 'A4' },
        { extend: 'print', title: 'Projetos' }
      ],
      language: {
        search: "Pesquisar:",
        lengthMenu: "Mostrar _MENU_ registros",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        infoEmpty: "Nenhum registro encontrado",
        zeroRecords: "Nenhum registro correspondente encontrado",
        paginate: {
          first: "Primeiro",
          last: "Último",
          next: "Próximo",
          previous: "Anterior"
        },
      }
    });
  });
</script>

<!-- JQuery (necessário para Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (necessário para tooltips, dropdowns, etc) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts adicionais -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
