<?php
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASS');

foreach ([
    'DB_HOST' => $dbHost,
    'DB_NAME' => $dbName,
    'DB_USER' => $dbUser,
    'DB_PASS' => $dbPassword,
] as $name => $value) {
    if ($value === false || trim($value) === '') {
        throw new RuntimeException('Environment variable ' . $name . ' must be configured.');
    }
}

if (strcasecmp($dbUser, 'root') === 0) {
    throw new RuntimeException('Configure a dedicated non-root database user in DB_USER.');
}

try {
    $pdo = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4', $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    throw new RuntimeException('Database connection failed. Check server logs and database configuration.', 0, $exception);
}
