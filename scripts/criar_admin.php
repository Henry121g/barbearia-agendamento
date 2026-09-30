<?php
// Cria (ou atualiza a senha de) um usuário administrador pelo terminal.
// Uso: php scripts/criar_admin.php usuario senha

if (PHP_SAPI !== 'cli') {
    exit('Este script só pode ser executado pelo terminal.');
}

require __DIR__ . '/../src/db.php';

[, $usuario, $senha] = $argv + [null, null, null];

if (!$usuario || !$senha || strlen($senha) < 8) {
    fwrite(STDERR, "Uso: php scripts/criar_admin.php usuario senha (mínimo 8 caracteres)\n");
    exit(1);
}

$stmt = db()->prepare(
    'INSERT INTO usuarios (usuario, senha_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE senha_hash = VALUES(senha_hash)'
);
$stmt->execute([$usuario, password_hash($senha, PASSWORD_DEFAULT)]);

echo "Administrador '{$usuario}' pronto.\n";
