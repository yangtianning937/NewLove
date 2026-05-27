<?php
declare(strict_types=1);

/**
 * Export MySQL tables into Firestore-friendly JSON files.
 *
 * Required environment variables:
 * - DB_NAME
 * - DB_USER
 *
 * Optional environment variables:
 * - DB_HOST, default 127.0.0.1
 * - DB_PORT, default 3306
 * - DB_PASS, default empty
 * - EXPORT_DIR, default firebase/firestore-data
 * - SKIP_TABLES, comma-separated table names
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_NAME') ?: '';
$username = getenv('DB_USER') ?: '';
$password = getenv('DB_PASS') ?: '';
$exportDir = getenv('EXPORT_DIR') ?: 'firebase/firestore-data';
$skipTables = array_filter(array_map('trim', explode(',', getenv('SKIP_TABLES') ?: 'phinxlog')));

if ($database === '' || $username === '') {
    fwrite(STDERR, "DB_NAME and DB_USER are required.\n");
    exit(1);
}

if (!is_dir($exportDir) && !mkdir($exportDir, 0775, true) && !is_dir($exportDir)) {
    fwrite(STDERR, "Could not create export directory: {$exportDir}\n");
    exit(1);
}

$pdo = new PDO(
    "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$tables = $pdo->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"')->fetchAll(PDO::FETCH_NUM);
$manifest = [
    'database' => $database,
    'exported_at' => gmdate('c'),
    'collections' => [],
];

foreach ($tables as $tableRow) {
    $table = $tableRow[0];

    if (in_array($table, $skipTables, true)) {
        continue;
    }

    $rows = $pdo->query(sprintf('SELECT * FROM `%s`', str_replace('`', '``', $table)))->fetchAll();
    $documents = [];

    foreach ($rows as $index => $row) {
        $documentId = isset($row['id']) ? (string)$row['id'] : (string)($index + 1);
        $documents[] = [
            'document_id' => $documentId,
            'data' => $row,
        ];
    }

    $filePath = $exportDir . DIRECTORY_SEPARATOR . $table . '.json';
    file_put_contents(
        $filePath,
        json_encode($documents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL
    );

    $manifest['collections'][] = [
        'name' => $table,
        'file' => $table . '.json',
        'documents' => count($documents),
    ];
}

file_put_contents(
    $exportDir . DIRECTORY_SEPARATOR . 'manifest.json',
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL
);

echo "Exported " . count($manifest['collections']) . " collections to {$exportDir}\n";
