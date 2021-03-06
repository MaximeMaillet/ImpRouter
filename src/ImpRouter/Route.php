<?php
/**
 * Created by PhpStorm.
 * User: MaximeMaillet
 * Date: 12/02/2017
 * Time: 21:45
 */

namespace M2Max\ImpRouter;


class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * @var array|null
     */
    private $parameters = null;

    /**
     * @var array|null
     */
    private $value_parameters = [];

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $class;

    /**
     * Route constructor.
     * @param $route
     * @param $data
     */
    public function __construct($route, $data) {

        $this->name = $route;
        $this->method = $data['method'];
        $this->controller = $data[Config::$NAME_KEY_CONTROLLER];
        $this->action = $data[Config::$NAME_KEY_ACTION];

        if(strpos($data[Config::$NAME_KEY_CONTROLLER], '\\') !== false) {
            $this->class = substr($data[Config::$NAME_KEY_CONTROLLER], strrpos($data[Config::$NAME_KEY_CONTROLLER], '\\')+1);
            $this->namespace = substr($data[Config::$NAME_KEY_CONTROLLER], 0, strrpos($data[Config::$NAME_KEY_CONTROLLER], '\\')+1);
        }

        if(strpos($route, '{') !== false) {
            preg_match_all("/{(.*?)}/", $route, $matches);

            $this->parameters = $matches[1];
        }
    }

    /**
     * Test if route match with route list
     * @param string $route
     * @return bool
     */
    public function isMatching($route) {
        $array_route_to_match = explode('/', $route);
        $counter = count($array_route_to_match);

        $array_own_route = explode('/', $this->name);

        if($counter != count($array_own_route)) {
            return false;
        }

        for($i=0; $i<$counter; $i++) {
            if(strpos($array_own_route[$i], '{') === false) {
                if($array_own_route[$i] != $array_route_to_match[$i]) {
                    return false;
                }
            }
            else {
                $this->value_parameters[] = $array_route_to_match[$i];
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array|null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return array|null
     */
    public function getValueParameters()
    {
        return $this->value_parameters;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
