<?php
/**
 * Created by PhpStorm.
 * User: MaximeMaillet
 * Date: 12/02/2017
 * Time: 16:43
 */

namespace M2Max\ImpRouter;


class Config
{
    public static $NAME_KEY_CONTROLLER = 'controller';
    public static $NAME_KEY_ACTION = 'action';

    /**
     * @var array
     */
    private $root_document;

    /**
     * @var array
     */
    private $routes;

    /**
     * @var string
     */
    private $root_path;

    /**
     * Config constructor.
     * @param $json
     */
    public function __construct($json) {
        $this->root_document = json_decode($json, true);

        $this->checkConfigFile();

        foreach ($this->root_document['route'] as $route => $route_data) {
            $this->routes[] = new Route($route, $route_data);
        }
    }

    /**
     * Check if config file is according to standard
     * @throws \Exception
     */
    private function checkConfigFile() {
        if(!array_key_exists('route', $this->root_document)) {
            throw new \Exception('You should add "route" key in config file');
        }

        foreach ($this->root_document['route'] as $route => $route_data) {
            if(!array_key_exists(self::$NAME_KEY_CONTROLLER, $route_data)) {
                throw new \Exception('There is no "'.self::$NAME_KEY_CONTROLLER.'" in route : '.$route);
            }

            if(!array_key_exists(self::$NAME_KEY_ACTION, $route_data)) {
                throw new \Exception('There is no "'.self::$NAME_KEY_ACTION.'" in route : '.$route);
            }

            if(strpos($route_data[self::$NAME_KEY_CONTROLLER], '\\') === false) {
                throw new \Exception('There is no namespace in route : '.$route);
            }
        }

        if(array_key_exists('root', $this->root_document))
            $this->root_path = dirname($_SERVER['SCRIPT_FILENAME']).$this->root_document['root'];
        else
            $this->root_path = dirname($_SERVER['SCRIPT_FILENAME']);

        if(!is_dir($this->root_path)) {
            throw new \Exception('Root directory is not a directory');
        }
    }

    /**
     * Return current Route object
     * @param string $route
     * @return Route
     * @throws \Exception
     */
    public function getCurrentRoute($route) {
        foreach ($this->routes as $Route) {
            if($Route->isMatching($route)) {
                return $Route;
            }
        }

        throw new \Exception('There is no route for '.$route);
    }

    /**
     * Test if route exist in config file
     * @param string $route
     * @return bool
     */
    public function isRouteExists($route) {

        foreach ($this->routes as $Route) {
            if($Route->isMatching($route))
                return true;
            else
                echo $Route->getController().'<br>';
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return $this->root_path;
    }
}