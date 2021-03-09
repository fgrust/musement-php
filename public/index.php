<?php

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../bootstrap.php';

$app = require __DIR__ . '/../routes/api.php';

$app->run();
