<?php

namespace Modules\Gallery;

use Core\Router;

class GalleryModule implements \Core\ModuleInterface
{
    public function register(?Router $router = null)
    {
        if ($router === null) {
            return;
        }

        // Public gallery routes
        $router->get('/gallery', [\Modules\Gallery\Controllers\GalleryController::class, 'index']);
        $router->get('/gallery/{id}', [\Modules\Gallery\Controllers\GalleryController::class, 'show']);
    }
}
