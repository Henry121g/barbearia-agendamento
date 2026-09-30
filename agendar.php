<?php
// Recebe o formulário de agendamento, valida e grava no banco.

require __DIR__ . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

csrf_validar();

$nome = trim($_POST['nome'] ?? '');
$telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
$servicoId = (int) ($_POST['servico_id'] ?? 0);
$barbeiroId = (int) ($_POST['barbeiro_id'] ?? 0);
$data = (string) ($_POST['data'] ?? '');
$horario = (string) ($_POST['horario'] ?? '');

function voltar_com_erro(string $mensagem): never
{
    $_SESSION['form_antigo'] = $_POST;
    flash('erro', $mensagem);
    redirecionar('index.php');
}

if ($nome === '' || mb_strlen($nome) > 100) {
    voltar_com_erro('Informe seu nome.');
}
if (strlen($telefone) < 10 || strlen($telefone) > 11) {
    voltar_com_erro('Informe um telefone válido com DDD.');
}

$pdo = db();

$stmt = $pdo->prepare('SELECT nome, duracao_minutos FROM servicos WHERE id = ? AND ativo = 1');
$stmt->execute([$servicoId]);
$servico = $stmt->fetch();
if (!$servico) {
    voltar_com_erro('Serviço inválido.');
}

// Transação + FOR UPDATE: se duas pessoas tentarem o mesmo horário ao mesmo tempo,
// a segunda espera a primeira terminar e então vê que o horário não está mais livre.
$pdo->beginTransaction();

$stmt = $pdo->prepare('SELECT id FROM barbeiros WHERE id = ? AND ativo = 1 FOR UPDATE');
$stmt->execute([$barbeiroId]);
if (!$stmt->fetch()) {
    $pdo->rollBack();
    voltar_com_erro('Barbeiro inválido.');
}

if (!in_array($horario, horarios_disponiveis($barbeiroId, $data, (int) $servico['duracao_minutos']), true)) {
    $pdo->rollBack();
    voltar_com_erro('Esse horário não está mais disponível. Escolha outro.');
}

$stmt = $pdo->prepare(
    'INSERT INTO agendamentos (cliente_nome, cliente_telefone, servico_id, barbeiro_id, data, horario)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$nome, $telefone, $servicoId, $barbeiroId, $data, $horario]);
$pdo->commit();

flash('sucesso', "Pronto, {$nome}! {$servico['nome']} agendado para " . formatar_data($data) . " às {$horario}.");
redirecionar('index.php');
