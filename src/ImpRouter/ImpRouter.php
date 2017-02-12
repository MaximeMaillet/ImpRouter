<?php

namespace M2Max\ImpRouter;

class ImpRouter
{
    /**
     * @var string
     */
    private $root_directory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $route;

    private $instance;

    public function __construct($config_file_path) {
        $this->root_directory = dirname($_SERVER['SCRIPT_FILENAME']);
        $this->config = new Config(file_get_contents($this->root_directory.'/'.$config_file_path));

        $this->loadConfig();

        $this->load();

        $this->checkActionExists();

        call_user_func_array([$this->instance, $this->config->getAction($this->route)], []);
    }

    private function loadConfig() {

        if(!is_dir($this->root_directory.$this->config->getAlternativeRootDirectory())) {
            throw new \Exception('Root directory is not a directory');
        }

        $this->root_directory .= $this->config->getAlternativeRootDirectory();
        $this->route = $_SERVER['PATH_INFO'];

        if(!$this->config->isRouteExist($this->route)) {
            throw new \Exception('This route does not exists ('.$this->route.')');
        }
    }

    private function checkControllerExists() {

        if(file_exists($this->root_directory.str_replace('\\', '', $this->config->getNamespace($this->route)).'/'.$this->config->getClass($this->route).'.php')) {
            return $this->root_directory.str_replace('\\', '', $this->config->getNamespace($this->route)).'/'.$this->config->getClass($this->route).'.php';
        }

        if(file_exists($this->root_directory.$this->config->getClass($this->route).'.php')) {
            return $this->root_directory.$this->config->getClass($this->route).'.php';
        }

        $class_path = $this->scanFile($this->root_directory);
        if($class_path === null) {
            throw new \Exception('This file does not exists : '.$this->config->getClass($this->route).'.php');
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

            if($file == $this->config->getClass($this->route).'.php') {
                return $path.$file;
            }
        }

        return null;
    }

    private function load() {
        $class_path = $this->checkControllerExists();
        require $class_path;
        $class_name = $this->config->getNamespace($this->route).$this->config->getClass($this->route);
        $this->instance = new $class_name();
    }

    private function checkActionExists() {

        $reflectionClass = new \ReflectionClass($this->instance);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $reflectionMethod) {

            if($reflectionMethod->getName() == $this->config->getAction($this->route)) {
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