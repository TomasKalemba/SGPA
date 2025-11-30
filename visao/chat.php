<link rel="stylesheet" href="/public/css/chat.css">

<div class="chat-container">
    <div id="chat-mensagens" class="chat-mensagens"></div>

    <form id="chat-form">
        <input type="hidden" name="projeto_id" id="projeto_id" value="<?= $projeto_id ?>">
        <input type="text" name="mensagem" id="mensagem" placeholder="Digite sua mensagem..." autocomplete="off">
        <button type="submit">Enviar</button>
    </form>
</div>

<script src="/public/js/chat.js"></script>
<script>
    carregarMensagens(<?= $projeto_id ?>);
    setInterval(() => carregarMensagens(<?= $projeto_id ?>), 2000);
</script>
