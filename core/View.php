<?php
namespace Core;

class View
{
    public static string $basePath = __DIR__ . '/../modules/';

    public static function render(string $view, array $data = [])
    {
        $viewPath = self::findView($view);

        if (!$viewPath) {
            throw new \Exception("View '$view' not found");
        }

        extract($data);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    private static function findView(string $view)
    {
        $path = self::$basePath;

        // view format example: blog/list
        $parts = explode('/', $view);
        $module = $parts[0];
        $file = $parts[1] . '.php';

        $full = $path . "$module/views/$file";

        return file_exists($full) ? $full : null;
    }
}
