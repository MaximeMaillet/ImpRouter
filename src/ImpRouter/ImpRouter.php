<?php

namespace M2Max\ImpRouter;

class ImpRouter
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var \stdClass
     */
    private $instance;

    /**
     * ImpRouter constructor.
     * @param $config_file_path
     */
    public function __construct($config_file_path) {
        $this->config = new Config(file_get_contents(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$config_file_path), true);

        if(isset($_SERVER['PATH_INFO']))
          $this->route = $this->config->getCurrentRoute($_SERVER['PATH_INFO']);
        else
          $this->route = $this->config->getCurrentRoute('/');

        $this->load();

        $this->checkActionExists();

        call_user_func_array([$this->instance, $this->route->getAction($this->route)], $this->route->getValueParameters());
    }

    /**
     * Check if controller exists
     * @return null|string
     * @throws \Exception
     */
    private function checkControllerExists() {

        if(file_exists($this->config->getRootPath().str_replace('\\', '', $this->route->getNamespace($this->route)).'/'.$this->route->getClass($this->route).'.php')) {
            return $this->config->getRootPath().str_replace('\\', '', $this->route->getNamespace($this->route)).'/'.$this->route->getClass($this->route).'.php';
        }

        if(file_exists($this->config->getRootPath().$this->route->getClass($this->route).'.php')) {
            return $this->config->getRootPath().$this->route->getClass($this->route).'.php';
        }

        $class_path = $this->scanFile($this->config->getRootPath());
        if($class_path === null) {
            throw new \Exception('This file does not exists : '.$this->route->getClass($this->route).'.php');
        }

        if(file_exists($class_path)) {
            return $class_path;
        }

        throw new \Exception('This file does not exists : '.$this->config->getClass($this->route).'.php');
    }

    private function scanFile($path) {
        $array_files = scandir($path);
        foreach ($array_files as $file) {

            if($file == '.' || $file == '..')
                continue;

            if(is_dir($file)) {
                return $this->scanFile($path.$file.'/');
            }

            if($file == $this->route->getClass($this->route).'.php') {
                return $path.$file;
            }
        }

        return null;
    }

    private function load() {
        $class_path = $this->checkControllerExists();
        require $class_path;
        $class_name = $this->route->getNamespace().$this->route->getClass($this->route);
        $this->instance = new $class_name();
    }

    private function checkActionExists() {

        $reflectionClass = new \ReflectionClass($this->instance);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $reflectionMethod) {

            if($reflectionMethod->getName() == $this->route->getAction($this->route)) {
                $this->checkMethod($reflectionMethod);
            }
        }
    }

    private function checkMethod(\ReflectionMethod $method) {
        if(!$method->isPublic()) {
            throw new \Exception('This method is not public ('.$method->getName().')');
        }
    }
 }
