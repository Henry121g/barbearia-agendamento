<?php
// Carregado no topo de todas as páginas: configura o ambiente e as funções comuns.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/agenda.php';

date_default_timezone_set(config()['barbearia']['fuso_horario']);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}
