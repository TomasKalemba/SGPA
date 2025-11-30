<!-- Rodapé -->
<?php
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

// ID do usuário logado (se quiser excluir ele da lista)
$usuarioLogadoId = $_SESSION['id'] ?? 0;

// Buscar todos os usuários, exceto o logado
$sql = "SELECT id, nome, tipo  
        FROM usuarios 
        WHERE id != :id_logado
        ORDER BY nome ASC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':id_logado', $usuarioLogadoId, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright &copy; <i>TOMAS KALEMBA-2025</i> </div>
                <div>
                    <a href="#">Privacidade & Politicas</a>
                    &middot;
                    <a href="#">Termos &amp; Condiçoes</a>
                </div>
            </div>
        </div>
    </footer>
  <!-- Span invisível que informa ao JavaScript o ID do usuário logado -->
<span class="user_online" data-user-id="<?= (int)$usuarioLogadoId ?>"></span>
