<?php
// Agenda do dia: lista os agendamentos e permite marcar como concluído ou cancelado.

require __DIR__ . '/../src/bootstrap.php';
exigir_login();

const STATUS_VALIDOS = ['agendado', 'concluido', 'cancelado'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $status = $_POST['status'] ?? '';
    if (in_array($status, STATUS_VALIDOS, true)) {
        $stmt = db()->prepare('UPDATE agendamentos SET status = ? WHERE id = ?');
        $stmt->execute([$status, (int) ($_POST['id'] ?? 0)]);
        flash('sucesso', 'Agendamento atualizado.');
    }
    redirecionar('index.php?data=' . urlencode($_POST['data'] ?? ''));
}

$data = data_valida($_GET['data'] ?? '') ? $_GET['data'] : date('Y-m-d');

$stmt = db()->prepare(
    'SELECT a.id, a.cliente_nome, a.cliente_telefone, a.horario, a.status,
            s.nome AS servico, s.preco, b.nome AS barbeiro
       FROM agendamentos a
       JOIN servicos s  ON s.id = a.servico_id
       JOIN barbeiros b ON b.id = a.barbeiro_id
      WHERE a.data = ?
      ORDER BY a.horario, b.nome'
);
$stmt->execute([$data]);
$agendamentos = $stmt->fetchAll();

$faturamento = 0.0;
foreach ($agendamentos as $a) {
    if ($a['status'] === 'concluido') {
        $faturamento += (float) $a['preco'];
    }
}

$titulo = 'Agenda';
$base = '../';
require __DIR__ . '/../src/views/cabecalho.php';
?>
<div class="barra">
    <h1>Agenda de <?= formatar_data($data) ?></h1>
    <form method="get" class="filtro">
        <input type="date" name="data" value="<?= e($data) ?>">
        <button type="submit">Ver dia</button>
    </form>
</div>

<p class="sub">
    <?= count($agendamentos) ?> agendamento(s) ·
    Faturado (concluídos): <?= formatar_preco($faturamento) ?>
</p>

<?php if (!$agendamentos): ?>
    <p class="vazio">Nenhum agendamento para este dia.</p>
<?php else: ?>
    <div class="tabela-rolagem">
        <table>
            <thead>
                <tr>
                    <th>Horário</th><th>Cliente</th><th>Telefone</th><th>Serviço</th>
                    <th>Barbeiro</th><th>Status</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($agendamentos as $a): ?>
                <tr class="status-<?= e($a['status']) ?>">
                    <td><?= e(substr($a['horario'], 0, 5)) ?></td>
                    <td><?= e($a['cliente_nome']) ?></td>
                    <td><a href="https://wa.me/55<?= e($a['cliente_telefone']) ?>" target="_blank" rel="noopener"><?= e($a['cliente_telefone']) ?></a></td>
                    <td><?= e($a['servico']) ?></td>
                    <td><?= e($a['barbeiro']) ?></td>
                    <td><span class="etiqueta etiqueta--<?= e($a['status']) ?>"><?= e($a['status']) ?></span></td>
                    <td class="acoes">
                        <?php if ($a['status'] === 'agendado'): ?>
                            <form method="post">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                <input type="hidden" name="data" value="<?= e($data) ?>">
                                <button name="status" value="concluido">Concluir</button>
                                <button name="status" value="cancelado" class="botao--perigo">Cancelar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../src/views/rodape.php'; ?>
