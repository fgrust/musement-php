<?php

$router = new \Bramus\Router\Router();

$router->setBasePath('/api');
$router->setNamespace('App\\Controllers');
$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    echo 'Not Found';
});

$router->get('cities', 'Controller@index'); // @phpstan-ignore-line
$router->get('cities/(\d+)', 'Controller@show'); // @phpstan-ignore-line

return $router;
