<?php
$caPath = getenv('MYSQL_ATTR_SSL_CA');

if (empty($caPath)) {
    echo "[CHECK-CA] Warning: MYSQL_ATTR_SSL_CA is not set in environment.\n";
    exit(0);
}

if (!file_exists($caPath)) {
    echo "[CHECK-CA] Error: CA certificate file not found at: {$caPath}\n";
    exit(1);
}

$content = file_get_contents($caPath);
if (strpos($content, 'BEGIN CERTIFICATE') === false) {
    echo "[CHECK-CA] Error: File at {$caPath} is not a valid PEM certificate.\n";
    exit(1);
}

echo "[CHECK-CA] Success: CA certificate found and verified at: {$caPath}\n";
exit(0);