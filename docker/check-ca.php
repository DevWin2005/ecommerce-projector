<?php

$caPath = getenv('MYSQL_ATTR_SSL_CA') ?: '/etc/secrets/ca.pem';

if (! file_exists($caPath)) {
    fwrite(STDERR, "Error: CA file does not exist at {$caPath}\n");
    exit(1);
}

$content = file_get_contents($caPath);
if (empty($content) || ! str_contains($content, 'BEGIN CERTIFICATE')) {
    fwrite(STDERR, "Error: CA file at {$caPath} is empty or invalid.\n");
    exit(1);
}

echo "CA certificate verified at {$caPath}\n";
exit(0);