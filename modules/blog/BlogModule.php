<?php
namespace Modules\Blog;

use Core\ModuleInterface;
use Core\Router;

class BlogModule implements ModuleInterface
{
    // Accept an optional Router so the module can register its routes
    public function register(Router $router = null)
    {
        // Register routes, hooks, etc.
        if ($router !== null) {
            // Require module PHP files (controllers, models) so classes are available
            $this->requireDirectoryPhpFiles(__DIR__ . '/controllers');
            $this->requireDirectoryPhpFiles(__DIR__ . '/models');

            $routesFile = __DIR__ . '/routes.php';
            if (file_exists($routesFile)) {
                $closure = require $routesFile;
                if (is_callable($closure)) {
                    $closure($router);
                }
            }
        }
    }

    private function requireDirectoryPhpFiles(string $dir)
    {
        if (! is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }
    }
}
