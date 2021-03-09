<?php

$router = new \Bramus\Router\Router();

$router->setBasePath('/api');
$router->setNamespace('App\\Controllers');
$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    echo 'Not Found';
});

$router->get('cities', 'Controller@index');
$router->get('cities/(\d+)', 'Controller@show');

return $router;
