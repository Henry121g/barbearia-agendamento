<?php
// Regras de agenda: quais horários estão livres para um barbeiro em um dia.

// "14:30" ou "14:30:00" -> 870 (minutos desde a meia-noite)
function para_minutos(string $hora): int
{
    [$h, $m] = array_map('intval', explode(':', $hora));
    return $h * 60 + $m;
}

function para_hora(int $minutos): string
{
    return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
}

function data_valida(string $data): ?DateTimeImmutable
{
    $dia = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    return ($dia && $dia->format('Y-m-d') === $data) ? $dia : null;
}

/**
 * Lista os horários de início livres para um serviço com a duração informada.
 * Um horário é livre se o serviço inteiro cabe antes do fechamento
 * e não se sobrepõe a nenhum agendamento ativo do barbeiro.
 */
function horarios_disponiveis(int $barbeiroId, string $data, int $duracao): array
{
    $cfg = config()['barbearia'];
    $dia = data_valida($data);
    $hoje = new DateTimeImmutable('today');

    if ($dia === null || $dia < $hoje) {
        return [];
    }
    if (!in_array((int) $dia->format('N'), $cfg['dias_funcionamento'], true)) {
        return [];
    }

    $stmt = db()->prepare(
        "SELECT a.horario, s.duracao_minutos
           FROM agendamentos a
           JOIN servicos s ON s.id = a.servico_id
          WHERE a.barbeiro_id = ? AND a.data = ? AND a.status <> 'cancelado'"
    );
    $stmt->execute([$barbeiroId, $data]);

    $ocupados = [];
    foreach ($stmt as $linha) {
        $inicio = para_minutos($linha['horario']);
        $ocupados[] = [$inicio, $inicio + (int) $linha['duracao_minutos']];
    }

    $abertura = para_minutos($cfg['abertura']);
    $fechamento = para_minutos($cfg['fechamento']);
    $agora = para_minutos(date('H:i'));
    $ehHoje = $dia == $hoje;

    $livres = [];
    for ($inicio = $abertura; $inicio + $duracao <= $fechamento; $inicio += $cfg['intervalo_minutos']) {
        if ($ehHoje && $inicio <= $agora) {
            continue;
        }
        $fim = $inicio + $duracao;
        foreach ($ocupados as [$ocupadoInicio, $ocupadoFim]) {
            if ($inicio < $ocupadoFim && $fim > $ocupadoInicio) {
                continue 2;
            }
        }
        $livres[] = para_hora($inicio);
    }

    return $livres;
}
