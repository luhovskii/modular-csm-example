<?php

use Modules\Blog\Controllers\BlogController;
return function($router) {
    $router->get('/blog', [BlogController::class, 'index']);
    $router->get('/blog/{id}', [BlogController::class, 'show']);
    $router->post('/blog/save', [BlogController::class, 'save']);
};
