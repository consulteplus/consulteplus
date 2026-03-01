<?php
/**
 * Loader de variáveis de ambiente (.env)
 * Não requer Composer — implementação nativa PHP.
 */

function loadEnv(string $filePath): void
{
    if (!file_exists($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignorar comentários
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Separar chave=valor
        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Remover aspas opcionais ao redor do valor
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Definir apenas se ainda não definido (permite override via $_ENV real)
        if (!isset($_ENV[$key]) && !isset($_SERVER[$key])) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Helper para obter variável de ambiente com valor padrão.
 */
function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false) {
        return $default;
    }

    // Converter strings booleanas
    return match (strtolower((string) $value)) {
        'true', '1', 'yes' => true,
        'false', '0', 'no' => false,
        'null', '' => null,
        default => $value,
    };
}

// Carregar o .env da raiz do projeto
loadEnv(__DIR__ . '/../.env');
