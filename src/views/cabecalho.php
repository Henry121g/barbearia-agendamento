<?php
/** @var string $titulo */
/** @var string $base  caminho até a raiz do site ('' ou '../') */
$base = $base ?? '';
$flash = pegar_flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | <?= e(config()['barbearia']['nome']) ?></title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<header class="topo">
    <div class="container topo__conteudo">
        <a class="logo" href="<?= $base ?>index.php">✂ <?= e(config()['barbearia']['nome']) ?></a>
        <nav>
            <?php if (usuario_logado()): ?>
                <a href="<?= $base ?>admin/index.php">Agenda</a>
                <a href="<?= $base ?>admin/servicos.php">Serviços</a>
                <a href="<?= $base ?>admin/logout.php">Sair</a>
            <?php else: ?>
                <a href="<?= $base ?>admin/login.php">Área do barbeiro</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
<?php if ($flash): ?>
    <div class="alerta alerta--<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
<?php endif; ?>
