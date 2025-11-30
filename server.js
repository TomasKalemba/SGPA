// server.js

const WebSocket = require('ws');

// Cria o servidor WebSocket na porta 8080
const wss = new WebSocket.Server({ port: 8080 });

const clientes = new Map();

wss.on('connection', (ws) => {
    let userId = null;

    // Quando recebe uma mensagem
    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);

            if (data.tipo === 'registrar' && data.userId) {
                userId = data.userId;
                clientes.set(userId, ws);
                console.log(`Usuário ${userId} conectado.`);
                return;
            }

            if (data.tipo === 'mensagem') {
                const { de, para, mensagem } = data;
                const receptorSocket = clientes.get(para);

                // Envia para o destinatário se estiver online
                if (receptorSocket && receptorSocket.readyState === WebSocket.OPEN) {
                    receptorSocket.send(JSON.stringify({ de, para, mensagem }));
                }

                // Também envia de volta ao remetente para exibir imediatamente
                if (ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({ de, para, mensagem }));
                }
            }
        } catch (err) {
            console.error('Erro ao processar mensagem:', err);
        }
    });

    // Quando o cliente desconecta
    ws.on('close', () => {
        if (userId !== null) {
            clientes.delete(userId);
            console.log(`Usuário ${userId} desconectado.`);
        }
    });
});

console.log("Servidor WebSocket rodando em ws://localhost:8080");
