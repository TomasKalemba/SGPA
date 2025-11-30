<?php foreach ($mensagens as $msg): ?>
    <div class="mensagem">
        <strong><?= htmlspecialchars($msg['usuario_nome']) ?>:</strong>
        <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
        <span class="hora"><?= date('H:i', strtotime($msg['data_envio'])) ?></span>
    </div>
<?php endforeach; ?>
