<?php
require_once '../modelo/crud.php';
$crud = new crud();
$conn = $crud->getConexao();

// ID do usuário logado
$usuarioLogadoId = $_SESSION['id'] ?? 0;

// Buscar todos os usuários, exceto o logado, incluindo a coluna 'foto'
$sql = "SELECT id, nome, tipo, foto 
        FROM usuarios 
        WHERE id != :id_logado
        ORDER BY nome ASC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':id_logado', $usuarioLogadoId, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Rodapé -->
<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Desenvolvido por:&copy; <i>TOMAS KALEMBA-2025</i></div>
            <div>
                <a href="#">Privacidade & Politicas</a>
                &middot;
                <a href="#">Termos &amp; Condiçoes</a>
            </div>
        </div>
    </div>
</footer>

<!-- Span invisível com ID do usuário logado -->
<span class="user_online" data-user-id="<?= $usuarioLogadoId ?>" style="display:none;"></span>

<!-- Aside do bate-papo lateral -->
<aside id="user_online" class="logado">
    <div class="header_Chat">
        <a href="#" style="color: red;" class="fecharChat">X</a> 
        <a href="#user_online" style="color: black;" > --CHAT--</a>
    </div>

    <div class="usuario_logado">
        <ul>
            <?php foreach ($usuarios as $user): ?>
                <?php
                    $idUsuario = (int)$user['id'];
                    $nomeUsuario = htmlspecialchars($user['nome']);
                    $imgPath = !empty($user['foto']) 
                        ? "../visao/fotos/" . htmlspecialchars($user['foto'])
                        : "../visao/estilo/img/default.png";
                ?>
                <li id="<?= $idUsuario ?>">
                    <div class="imgSmall">
                        <img src="<?= $imgPath ?>" alt="Foto de <?= $nomeUsuario ?>" />
                    </div>
                    <a href="#" 
                       id="<?= $usuarioLogadoId . ':' . $idUsuario ?>" 
                       class="comecar" 
                       data-nome="<?= $nomeUsuario ?>">
                        <?= $nomeUsuario ?>
                    </a>
                    <span id="<?= $idUsuario ?>" class="status on"></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</aside>

<!-- Container de chats -->
<aside id="chats"></aside>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JQuery e JS do chat -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="js/jsChat.js"></script>

<style>
/* Miniatura de fotos no chat */
.imgSmall {
    width: 40px;
    height: 40px;
    overflow: hidden;
    border-radius: 50%;
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
    border: 2px solid #ccc;
}
.imgSmall img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.status.on {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: green;
    border-radius: 50%;
    margin-left: 5px;
}
#chats {
    position: fixed;
    bottom: 10px;
    right: 10px;
    z-index: 3000;
}
.chatWindow {
    width: 250px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
.chatHeader {
    background: #007bff;
    color: white;
    padding: 5px 10px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    border-radius: 8px 8px 0 0;
}
.chatBody {
    max-height: 200px;
    overflow-y: auto;
    padding: 5px 10px;
}
.chatBody ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.chatInput {
    display: flex;
    border-top: 1px solid #ccc;
}
.chatInput input {
    flex: 1;
    padding: 5px;
    border: none;
}
.chatInput button {
    background: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
}
</style>

<script>
$(document).ready(function(){
    const usuarioLogadoId = $('.user_online').data('user-id');

    // Abrir chat ao clicar no nome do usuário
    $(document).on('click', '.comecar', function(e){
        e.preventDefault();
        const usuarioId = $(this).attr('id'); // formato antigo
        const nomeUsuario = $(this).data('nome');

        // Chama a função do jsChat.js com os parâmetros corretos
        abrirChat(usuarioId, nomeUsuario);
    });

    // Fechar chat
    $(document).on('click', '.fecharChat', function(){
        $(this).closest('.chatWindow').remove();
    });
});
</script>
