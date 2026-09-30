<?php
require __DIR__ . '/src/bootstrap.php';

$servicos = db()->query('SELECT id, nome, preco, duracao_minutos FROM servicos WHERE ativo = 1 ORDER BY nome')->fetchAll();
$barbeiros = db()->query('SELECT id, nome FROM barbeiros WHERE ativo = 1 ORDER BY nome')->fetchAll();

// Se o envio falhou, o agendar.php devolve o que a pessoa digitou para não precisar redigitar.
$antigo = $_SESSION['form_antigo'] ?? [];
unset($_SESSION['form_antigo']);

$titulo = 'Agende seu horário';
require __DIR__ . '/src/views/cabecalho.php';
?>
<section class="grade">
    <div>
        <h1>Agende seu horário</h1>
        <p class="sub">Escolha o serviço, o barbeiro e o melhor horário para você.</p>

        <ul class="servicos">
            <?php foreach ($servicos as $s): ?>
                <li>
                    <strong><?= e($s['nome']) ?></strong>
                    <span><?= (int) $s['duracao_minutos'] ?> min · <?= formatar_preco((float) $s['preco']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <form class="cartao" method="post" action="agendar.php" id="form-agendamento">
        <?= csrf_campo() ?>

        <label>Seu nome
            <input type="text" name="nome" required maxlength="100" value="<?= e($antigo['nome'] ?? '') ?>">
        </label>

        <label>Telefone (WhatsApp)
            <input type="tel" name="telefone" required maxlength="20" placeholder="(11) 90000-0000" value="<?= e($antigo['telefone'] ?? '') ?>">
        </label>

        <label>Serviço
            <select name="servico_id" id="servico" required>
                <option value="">Selecione</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= (int) $s['id'] ?>" <?= ($antigo['servico_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= e($s['nome']) ?> (<?= formatar_preco((float) $s['preco']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Barbeiro
            <select name="barbeiro_id" id="barbeiro" required>
                <option value="">Selecione</option>
                <?php foreach ($barbeiros as $b): ?>
                    <option value="<?= (int) $b['id'] ?>" <?= ($antigo['barbeiro_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                        <?= e($b['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Data
            <input type="date" name="data" id="data" required min="<?= date('Y-m-d') ?>" value="<?= e($antigo['data'] ?? '') ?>">
        </label>

        <label>Horário
            <select name="horario" id="horario" required disabled>
                <option value="">Escolha serviço, barbeiro e data</option>
            </select>
        </label>

        <button type="submit">Confirmar agendamento</button>
    </form>
</section>

<script src="assets/js/agendamento.js"></script>
<?php require __DIR__ . '/src/views/rodape.php'; ?>
