<?php
// Endpoint JSON usado pelo JavaScript da página de agendamento.
// Exemplo: horarios.php?barbeiro_id=1&servico_id=2&data=2026-10-01

require __DIR__ . '/src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$barbeiroId = (int) ($_GET['barbeiro_id'] ?? 0);
$servicoId = (int) ($_GET['servico_id'] ?? 0);
$data = (string) ($_GET['data'] ?? '');

$stmt = db()->prepare('SELECT duracao_minutos FROM servicos WHERE id = ? AND ativo = 1');
$stmt->execute([$servicoId]);
$duracao = $stmt->fetchColumn();

if ($duracao === false || $barbeiroId <= 0) {
    echo json_encode(['horarios' => []]);
    exit;
}

echo json_encode(['horarios' => horarios_disponiveis($barbeiroId, $data, (int) $duracao)]);
