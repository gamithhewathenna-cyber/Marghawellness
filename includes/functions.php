<?php
declare(strict_types=1);

/** HTML-escape. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build a site URL relative to the install folder. */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/** Asset URL with cache-busting version based on file mtime. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

/** Image URL — accepts a bare filename (assets/images) or absolute URL. */
function img(string $src): string
{
    if (preg_match('#^(https?:)?//#', $src)) {
        return $src;
    }
    return url('assets/images/' . ltrim($src, '/'));
}

/** Current request slug, used to mark the active nav item. */
function current_slug(): string
{
    return $GLOBALS['page_slug'] ?? 'home';
}

/** CSRF token for forms. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/** Send a JSON response and stop. */
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Render a partial with variables in scope. */
function partial(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require ROOT_PATH . '/partials/' . $name . '.php';
}

/** Inline SVG arrow used on buttons. */
function arrow_icon(): string
{
    return '<svg class="btn__arrow" width="16" height="12" viewBox="0 0 16 12" aria-hidden="true"><path d="M0 6h14.5M9.5 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.3"/></svg>';
}
