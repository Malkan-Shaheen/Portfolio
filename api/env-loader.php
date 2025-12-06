<?php
/**
 * Simple .env file loader for PHP
 * This reads a .env file from the project root and makes variables available
 */

function loadEnv($path = null) {
    // Default to project root (one level up from api folder)
    if ($path === null) {
        $path = dirname(__DIR__) . '/.env';
    }
    
    if (!file_exists($path) || !is_readable($path)) {
        return false;
    }
    
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lines === false) {
        return false;
    }
    
    foreach ($lines as $line) {
        // Skip comments
        $trimmed = trim($line);
        if (empty($trimmed) || strpos($trimmed, '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set as environment variable
                if (!empty($key)) {
                    $_ENV[$key] = $value;
                    @putenv("$key=$value");
                }
            }
        }
    }
    
    return true;
}

// Auto-load .env if it exists (silently fail if not)
@loadEnv();
?>

