<?php
// Cadastro de serviços: adicionar novos e ativar/desativar os existentes.

require __DIR__ . '/../src/bootstrap.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    if (($_POST['acao'] ?? '') === 'adicionar') {
        $nome = trim($_POST['nome'] ?? '');
        $preco = (float) str_replace(',', '.', $_POST['preco'] ?? '0');
        $duracao = (int) ($_POST['duracao'] ?? 0);

        if ($nome === '' || $preco <= 0 || $duracao <= 0 || $duracao % 30 !== 0) {
            flash('erro', 'Preencha nome, preço e uma duração múltipla de 30 minutos.');
        } else {
            $stmt = db()->prepare('INSERT INTO servicos (nome, preco, duracao_minutos) VALUES (?, ?, ?)');
            $stmt->execute([$nome, $preco, $duracao]);
            flash('sucesso', 'Serviço adicionado.');
        }
    }

    if (($_POST['acao'] ?? '') === 'alternar') {
        $stmt = db()->prepare('UPDATE servicos SET ativo = 1 - ativo WHERE id = ?');
        $stmt->execute([(int) ($_POST['id'] ?? 0)]);
        flash('sucesso', 'Serviço atualizado.');
    }

    redirecionar('servicos.php');
}

$servicos = db()->query('SELECT * FROM servicos ORDER BY ativo DESC, nome')->fetchAll();

$titulo = 'Serviços';
$base = '../';
require __DIR__ . '/../src/views/cabecalho.php';
?>
<h1>Serviços</h1>

<form method="post" class="cartao linha-form">
    <?= csrf_campo() ?>
    <input type="hidden" name="acao" value="adicionar">
    <label>Nome <input type="text" name="nome" required maxlength="100"></label>
    <label>Preço (R$) <input type="text" name="preco" required inputmode="decimal" placeholder="40,00"></label>
    <label>Duração
        <select name="duracao">
            <option value="30">30 min</option>
            <option value="60">60 min</option>
            <option value="90">90 min</option>
        </select>
    </label>
    <button type="submit">Adicionar</button>
</form>

<div class="tabela-rolagem">
    <table>
        <thead><tr><th>Serviço</th><th>Preço</th><th>Duração</th><th>Situação</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($servicos as $s): ?>
            <tr class="<?= $s['ativo'] ? '' : 'inativo' ?>">
                <td><?= e($s['nome']) ?></td>
                <td><?= formatar_preco((float) $s['preco']) ?></td>
                <td><?= (int) $s['duracao_minutos'] ?> min</td>
                <td><?= $s['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                <td>
                    <form method="post">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="acao" value="alternar">
                        <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                        <button type="submit"><?= $s['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../src/views/rodape.php'; ?>
