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

    public function __construct($json) {
        $this->root_document = json_decode($json, true);

        $this->checkConfigFile();

        foreach ($this->root_document['route'] as $route => $route_data) {
            if(strpos($route_data[self::$NAME_KEY_CONTROLLER], '\\') !== false) {
                $this->root_document['route'][$route]['class'] = substr($route_data[self::$NAME_KEY_CONTROLLER], strrpos($route_data[self::$NAME_KEY_CONTROLLER], '\\')+1);
                $this->root_document['route'][$route]['namespace'] = substr($route_data[self::$NAME_KEY_CONTROLLER], 0, strrpos($route_data[self::$NAME_KEY_CONTROLLER], '\\')+1);
            }
        }
    }

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
    }

    public function isRouteExist($route) {
        return array_key_exists($route, $this->root_document['route']);
    }

    public function getAlternativeRootDirectory() {
        if(array_key_exists('root', $this->root_document))
            return $this->root_document['root'];
        else
            return '/';
    }

    public function getNamespace($route) {
        return $this->root_document['route'][$route]['namespace'];
    }

    public function getClass($route) {
        return $this->root_document['route'][$route]['class'];
    }

    public function getController($route) {
        $this->isRouteExists($route);

        if(!array_key_exists(self::$NAME_KEY_CONTROLLER, $this->root_document['route'][$route])) {
            throw new \Exception('Controller does not exists in config file');
        }

        return $this->root_document['route'][$route][self::$NAME_KEY_CONTROLLER];
    }

    public function getAction($route) {

        $this->isRouteExists($route);

        if(!array_key_exists(self::$NAME_KEY_ACTION, $this->root_document['route'][$route])) {
            throw new \Exception('Controller does not exists in config file');
        }

        return $this->root_document['route'][$route][self::$NAME_KEY_ACTION];
    }

    private function isRouteExists($route) {
        if(!array_key_exists($route, $this->root_document['route'])) {
            throw new \Exception('Route does not exists in config file');
        }
    }
}