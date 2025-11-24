<?php
namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, $callback)
    {
        // Convert path with {param} into a regex with named captures
        $paramNames = [];
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $path);

        $pattern = '#^' . $regex . '$#';

        $this->routes['GET'][] = [
            'pattern' => $pattern,
            'callback' => $callback,
            'paramNames' => $paramNames,
            'original' => $path,
        ];
    }

    public function post(string $path, $callback)
    {
        $paramNames = [];
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $path);

        $pattern = '#^' . $regex . '$#';

        $this->routes['POST'][] = [
            'pattern' => $pattern,
            'callback' => $callback,
            'paramNames' => $paramNames,
            'original' => $path,
        ];
    }

    public function dispatch(string $uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($uri, PHP_URL_PATH);

        if (empty($this->routes[$method])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                // extract named params
                $params = [];
                foreach ($route['paramNames'] as $name) {
                    if (isset($matches[$name])) {
                        $params[] = $matches[$name];
                    }
                }

                $callback = $route['callback'];

                // support array-style controller callbacks and closures
                $result = null;
                if (is_array($callback) && count($callback) === 2) {
                    $class = $callback[0];
                    $methodName = $callback[1];
                    if (is_string($class) && class_exists($class)) {
                        $instance = new $class();
                        $result = call_user_func_array([$instance, $methodName], $params);
                    } else {
                        // if it's already an object
                        if (is_object($class)) {
                            $result = call_user_func_array([$class, $methodName], $params);
                        } else {
                            http_response_code(500);
                            echo "Handler class not found";
                            return;
                        }
                    }
                } else {
                    // closure or function
                    $result = call_user_func_array($callback, $params);
                }

                if (is_string($result)) {
                    echo $result;
                }

                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
