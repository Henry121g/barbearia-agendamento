<?php
// Funções auxiliares usadas em várias páginas.

// Escapa texto antes de mostrar no HTML (evita XSS).
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function redirecionar(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// Mensagem que aparece uma única vez na próxima página.
function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function pegar_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Proteção CSRF: cada formulário leva um token que só o nosso site conhece.
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_validar(): void
{
    if (!hash_equals(csrf_token(), $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Formulário expirado. Volte e tente novamente.');
    }
}

function usuario_logado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigir_login(): void
{
    if (!usuario_logado()) {
        redirecionar('login.php');
    }
}

function formatar_preco(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatar_data(string $data): string
{
    return DateTimeImmutable::createFromFormat('Y-m-d', $data)->format('d/m/Y');
}
