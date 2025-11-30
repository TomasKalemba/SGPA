

jQuery(function () {
    let JanelasAbertas = 0;
    const LIMITE_JANELAS = 3;
    const userOnline = Number(jQuery('span.user_online').data('user-id'));

    // 🔔 Solicita permissão para notificações do navegador
    if ("Notification" in window && Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    // Inicializa conexão WebSocket
    const socket = new WebSocket("ws://localhost:8080");

    socket.onopen = function () {
        console.log("Conectado ao WebSocket");
    };

    socket.onerror = function (error) {
        console.error("WebSocket erro:", error);
    };

    // Funções para manipular o contador
    function incrementarContador(idUsuario) {
        const contador = $(`#user_online a[id$=":${idUsuario}"] .contador-mensagens`);
        if (contador.length) {
            let valor = parseInt(contador.text()) || 0;
            contador.text(valor + 1).show();
        }
    }

    function zerarContador(idUsuario) {
        const contador = $(`#user_online a[id$=":${idUsuario}"] .contador-mensagens`);
        if (contador.length) {
            contador.text('0').hide();
        }
    }

    // 🔔 Função para exibir notificação
    function mostrarNotificacao(remetente, mensagem, idRemetente) {
        if (!("Notification" in window)) return;
        if (Notification.permission !== "granted") return;

        const notification = new Notification(`Nova mensagem de ${remetente}`, {
            body: mensagem,
            icon: "/SGPA/public/img/chat-icon.png" // 🔧 ajusta o caminho do ícone
        });

        // Ao clicar na notificação → foca na aba e abre janela de chat
        notification.onclick = function () {
            window.focus();
            if (!$(`#janela_${idRemetente}`).length) {
                add_janela(idRemetente, remetente, 'on');
                RetornarHistorico(idRemetente);
                JanelasAbertas++;
            }
            zerarContador(idRemetente);
        };
    }

    socket.onmessage = function (event) {
        const msg = JSON.parse(event.data);

        if (msg.para == userOnline) {
            const janelaExiste = $(`#janela_${msg.de}`).length > 0;
            const janelaVisivel = $(`#janela_${msg.de}`).is(':visible');

            if (!janelaExiste) {
                add_janela(msg.de, msg.nome, 'on');
                incrementarContador(msg.de);

                // 🔔 Notificação se a janela não existir
                mostrarNotificacao(msg.nome, msg.mensagem, msg.de);
            } else if (!janelaVisivel) {
                incrementarContador(msg.de);

                // 🔔 Notificação se a janela existir mas estiver minimizada/fechada
                mostrarNotificacao(msg.nome, msg.mensagem, msg.de);
            }

            const ul = $(`#janela_${msg.de} .mensagens ul`);
            const mensagemHTML = $('<div>').text(msg.mensagem).html();

            ul.append(`
                <li class="ele">
                    <p>${mensagemHTML}</p>
                </li>
            `);

            const altura = $(`#janela_${msg.de} .mensagens`)[0].scrollHeight;
            $(`#janela_${msg.de} .mensagens`).animate({ scrollTop: altura }, 300);
        }
    };

    function add_janela(id_para, nome, status) {
        if ($(`#janela_${id_para}`).length) return;

        const Janela = `
            <div class="window" id="janela_${id_para}">
                <div class="header_window">
                    <a href="#user_online" class="close">X</a>
                    <span class="nome">${nome}</span>
                    <span id="${id_para}" class="${status}"></span>
                </div>
                <div class="corpo">
                    <div class="mensagens"><ul></ul></div>
                    <div class="enviar_mensagem" id="enviar_${id_para}">
                        <input type="text" name="mensagem" class="msg" id="${id_para}" placeholder="Nova mensagem..." autocomplete="off" />
                    </div>
                </div>
            </div>
        `;

        $('#chats').append(Janela);
    }

    function RetornarHistorico(id_conversa) {
        if (isNaN(id_conversa) || id_conversa <= 0) return;

        $.ajax({
            url: '/SGPA/Controlo/mensagem.php',
            method: 'POST',
            dataType: 'json',
            data: {
                acao: 'ler',
                conversacom: id_conversa,
                online: userOnline
            },
            success: function (resposta) {
                if (resposta.status === 'erro') return;

                const ul = $(`#janela_${id_conversa} .mensagens ul`);
                ul.empty();

                (resposta.mensagens || []).forEach(function (msg) {
                    const classe = (msg.id_de === userOnline) ? 'eu' : 'ele';
                    const fotoHTML = msg.foto ? `<div class="imgSmall"><img src="${msg.foto}" alt="Foto" /></div>` : '';
                    const mensagemHTML = $('<div>').text(msg.mensagem).html();

                    ul.append(`<li id="${msg.id}" class="${classe}">${fotoHTML}<p>${mensagemHTML}</p></li>`);
                });

                const altura = $(`#janela_${id_conversa} .mensagens`)[0].scrollHeight;
                $(`#janela_${id_conversa} .mensagens`).animate({ scrollTop: altura }, 500);
            }
        });
    }

    $('body').on('click', '#user_online a.comecar', function (e) {
        e.preventDefault();

        const id_str = $(this).attr('id');
        if (!id_str) return;

        const [id_de, id_para] = id_str.split(':').map(Number);
        if (!id_de || !id_para) return;

        const nome = $(this).clone().children().remove().end().text().trim();
        const status = $(this).next('span').attr('class') || '';

        if (JanelasAbertas >= LIMITE_JANELAS) {
            alert(`Você só pode abrir até ${LIMITE_JANELAS} janelas.`);
            return;
        }

        if (!$(`#janela_${id_para}`).length) {
            add_janela(id_para, nome, status);
            RetornarHistorico(id_para);
            JanelasAbertas++;
        }

        zerarContador(id_para);

        $(this).removeClass('comecar');
    });

    $('body').on('click', '.header_window', function () {
        $(this).siblings('.corpo').toggle(100);
    });

    $('body').on('click', '.close', function () {
        const janela = $(this).closest('.window');
        const idFechada = janela.attr('id').replace('janela_', '');

        janela.remove();
        JanelasAbertas--;

        $(`#user_online a[id$=":${idFechada}"]`).addClass('comecar');
    });

    $('body').on('keyup', '.msg', function (e) {
        if (e.which === 13) {
            const texto = $(this).val().trim();
            if (!texto) return;

            const para = Number($(this).attr('id'));
            if (!para) {
                alert("Erro: destinatário inválido.");
                return;
            }

            const $janela = $(`#janela_${para}`);
            const ul = $janela.find('.mensagens ul');
            const mensagemHTML = $('<div>').text(texto).html();

            ul.append(`
                <li class="eu">
                    <p>${mensagemHTML}</p>
                </li>
            `);

            $janela.find('.msg').val('');
            const altura = $janela.find('.mensagens')[0].scrollHeight;
            $janela.find('.mensagens').animate({ scrollTop: altura }, 300);

            $.ajax({
                url: '/SGPA/Controlo/mensagem.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    acao: 'enviar',
                    mensagem: texto,
                    de: userOnline,
                    para: para
                }
            });

            const payload = {
                de: userOnline,
                para: para,
                nome: $('span.user_online').data('nome') || 'Usuário',
                mensagem: texto
            };

            socket.send(JSON.stringify(payload));
        }
    });
});
