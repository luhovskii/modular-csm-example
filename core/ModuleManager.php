<?php
namespace Core;

interface ModuleInterface
{
    public function register();
}

class ModuleManager
{
    private string $modulesPath;

    public function __construct(string $modulesPath)
    {
        $this->modulesPath = $modulesPath;
    }

    public function loadModules(?Router $router = null): void
    {
        foreach (glob($this->modulesPath . '/*/module.json') as $configFile) {
            $config = json_decode(file_get_contents($configFile), true);

            $namespace = $config['namespace'];
            $mainClass = $namespace . '\\' . pathinfo($config['main'], PATHINFO_FILENAME);
            $mainPath = dirname($configFile) . '/' . $config['main'];

            // Prefer autoloaded classes; only require the main file if the class is not available
            if (! class_exists($mainClass) && file_exists($mainPath)) {
                require_once $mainPath;
            }

            if (class_exists($mainClass)) {
                $module = new $mainClass();
                if ($module instanceof ModuleInterface) {
                    // If a router is provided, pass it to the module's register method when possible
                    if ($router !== null) {
                        // call register with router if it accepts a parameter
                        $module->register($router);
                    } else {
                        $module->register();
                    }
                }
            }
        }
    }
}
