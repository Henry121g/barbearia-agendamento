<?php
// Modelo de configuração.
// Copie este arquivo para config.php e preencha com os dados do SEU banco.
// O config.php está no .gitignore, então a senha real nunca vai para o GitHub.

return [
    'db' => [
        'host'    => 'localhost',
        'porta'   => 3306,
        'nome'    => 'barbearia',
        'usuario' => 'root',
        'senha'   => '',
    ],
    'barbearia' => [
        'nome'               => 'Barbearia Exemplo',
        'abertura'           => '09:00',
        'fechamento'         => '19:00',
        'intervalo_minutos'  => 30,
        // Dias em que a barbearia abre: 1 = segunda ... 7 = domingo
        'dias_funcionamento' => [2, 3, 4, 5, 6],
        'fuso_horario'       => 'America/Sao_Paulo',
    ],
];
