<?php
/**
 * Environment loader
 * - Uses Render environment variables automatically
 * - Falls back to .env file only if it exists (for local development)
 */

function loadEnv($path)
{
    // If running on Render (or any server with env vars already set),
    // no need to load .env file
    if (getenv('RENDER') !== false || getenv('SMTP_USER') !== false) {
        return true;
    }

    // Fallback to .env file (for local XAMPP/dev)
    if (!file_exists($path) || !is_readable($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return false;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!isset($_ENV[$name]) && !isset($_SERVER[$name])) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    return true;
}

// Attempt local .env load (harmless on Render)
loadEnv(__DIR__ . '/../.env');