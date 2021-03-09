<?php

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/config/database.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => DB_HOST,
    'port' => DB_PORT,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASSWORD,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->bootEloquent();
$capsule->setAsGlobal();
