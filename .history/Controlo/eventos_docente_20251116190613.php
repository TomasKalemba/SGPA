<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

require_once '../modelo/conexao.php'; // ajuste conforme necessário

$docente_id = $_SESSION['id'];

try {
    $pdo = conectar();

    $stmt = $pdo->prepare("
        SELECT id, titulo, prazo, estatus, feedback
        FROM projectos
        WHERE docente_id = :docente_id
    ");
    $stmt->execute([':docente_id' => $docente_id]);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventos = []; // Inicializa o array antes do loop

    foreach ($projetos as $row) {
        // Aqui você deve calcular as variáveis usadas abaixo, por exemplo:
        $data_submissao = strtotime($row['prazo']); // Exemplo: converte prazo para timestamp
        $cor = '#3788d8'; // Exemplo de cor padrão, pode ajustar conforme seu critério

        // Exemplo de cálculo simples para status de vencimento:
        $hoje = strtotime(date('Y-m-d'));
        $vencido = $data_submissao < $hoje;
        $alerta = false; // Defina conforme sua lógica
        $diasRestantes = max(0, ($data_submissao - $hoje) / 86400); // dias entre hoje e prazo

        $eventos[] = [
            'title' => $row['titulo'],
            'start' => date('Y-m-d', $data_submissao),
            'color' => $cor,
            'extendedProps' => [
                'projeto_id' => $row['id'], // Use o id correto do projeto
                'status' => $vencido ? 'Atrasado (prazo vencido)' : $row['estatus'],
                'feedback' => $row['feedback'],
                'vencido' => $vencido,
                'alerta' => $alerta,
                'diasRestantes' => $diasRestantes
            ]
        ];
    }

    echo json_encode($eventos);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro ao carregar eventos: ' . $e->getMessage()]);
}

