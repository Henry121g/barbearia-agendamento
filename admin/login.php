<?php
require __DIR__ . '/../src/bootstrap.php';

if (usuario_logado()) {
    redirecionar('index.php');
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $stmt = db()->prepare('SELECT id, senha_hash FROM usuarios WHERE usuario = ?');
    $stmt->execute([trim($_POST['usuario'] ?? '')]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($_POST['senha'] ?? '', $usuario['senha_hash'])) {
        session_regenerate_id(true); // evita fixação de sessão
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        redirecionar('index.php');
    }

    // Mesma mensagem para usuário ou senha errados: não revela qual dos dois existe.
    $erro = 'Usuário ou senha incorretos.';
}

$titulo = 'Entrar';
$base = '../';
require __DIR__ . '/../src/views/cabecalho.php';
?>
<form class="cartao cartao--estreito" method="post">
    <h1>Área do barbeiro</h1>
    <?php if ($erro): ?>
        <div class="alerta alerta--erro"><?= e($erro) ?></div>
    <?php endif; ?>
    <?= csrf_campo() ?>
    <label>Usuário
        <input type="text" name="usuario" required autocomplete="username">
    </label>
    <label>Senha
        <input type="password" name="senha" required autocomplete="current-password">
    </label>
    <button type="submit">Entrar</button>
</form>
<?php require __DIR__ . '/../src/views/rodape.php'; ?>
