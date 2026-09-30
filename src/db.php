<?php
// Leitura da configuração e conexão com o MySQL via PDO.

function config(): array
{
    static $config = null;

    if ($config === null) {
        $arquivo = __DIR__ . '/../config.php';
        if (!file_exists($arquivo)) {
            http_response_code(500);
            exit('Arquivo config.php não encontrado. Copie config.example.php para config.php e preencha os dados do banco.');
        }
        $config = require $arquivo;
    }

    return $config;
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $c = config()['db'];
        $dsn = "mysql:host={$c['host']};port={$c['porta']};dbname={$c['nome']};charset=utf8mb4";

        $pdo = new PDO($dsn, $c['usuario'], $c['senha'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}
