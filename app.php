<?php

require __DIR__ . '/vendor/autoload.php';

require_once './bootstrap.php';

use App\Musement;

$app = new Musement();
$app->run();
