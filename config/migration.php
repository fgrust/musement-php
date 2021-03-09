<?php

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require 'database.php';

return [
    'paths' => [
        'migrations' => [
            'App\\Migration' => '%%PHINX_CONFIG_DIR%%/../migrations'
        ]
    ],
    'migration_base_class' => 'App\\Migration\\Migration',
    'environments' => [
        'default_migration_table' => 'migration',
        'default_database' => 'jagaad_db',
        'jagaad_db' => [
            'adapter' => DB_CONNECTION,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASSWORD,
            'port' => DB_PORT,
        ],
    ],
];
