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
    public static $NAME_KEY_METHOD = 'method';
    public static $NAME_KEY_TARGET = 'target';

    public static $ENV = 'dev';

    private static $METHOD_ACCEPTED = ['get'];

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
    * @var string
    */
    private $root_url;

    /**
     * Config constructor.
     * @param $json
     */
    public function __construct($json) {
        $this->root_document = json_decode($json, true);

        $this->checkConfigFile();
    }

    /**
     * Check if config file is according to standard
     * @throws \Exception
     */
    private function checkConfigFile() {
        $this->checkJsonError();

        if(!array_key_exists('route', $this->root_document)) {
            throw new \Exception('You should add "route" key in config file');
        }

        foreach ($this->root_document['route'] as $route => $route_data) {

            foreach ($route_data as $method => $method_data) {

                if(!in_array($method, self::$METHOD_ACCEPTED)) {
                    throw new \Exception('Method is not accepted ('.$method.')');
                }

                if(!array_key_exists(self::$NAME_KEY_TARGET, $method_data)) {
                    throw new \Exception('There is no "'.self::$NAME_KEY_METHOD.'" in route : '.$route);
                }

                if(substr($method_data[self::$NAME_KEY_TARGET], 0, 1) != '\\') {
                    throw new \Exception('There is no namespace in route : '.$route);
                }

                $this->routes[] = new Route($route, $method, $method_data);
            }
        }

        if(array_key_exists('root_path', $this->root_document))
            $this->root_path = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$this->root_document['root_path'];
        else
            $this->root_path = dirname($_SERVER['SCRIPT_FILENAME']).'/';

        $this->root_url = dirname($_SERVER['SCRIPT_NAME']);

        if(!is_dir($this->root_path)) {
            throw new \Exception('Root directory is not a directory');
        }
    }

    private function checkJsonError() {
      switch (json_last_error()) {
        case JSON_ERROR_NONE:
          return true;
          break;
        case JSON_ERROR_DEPTH:
            throw new \Exception("JSON_ERROR_DEPTH :: The maximum stack depth has been exceeded");
            break;
        case JSON_ERROR_STATE_MISMATCH:
            throw new \Exception("JSON_ERROR_STATE_MISMATCH :: Invalid or malformed JSON");
            break;
        case JSON_ERROR_CTRL_CHAR:
            throw new \Exception("JSON_ERROR_CTRL_CHAR :: Control character error, possibly incorrectly encoded");
            break;
        case JSON_ERROR_SYNTAX:
            throw new \Exception("JSON_ERROR_SYNTAX :: Syntax error");
            break;
        case JSON_ERROR_UTF8:
            throw new \Exception("JSON_ERROR_UTF8 :: Malformed UTF-8 characters, possibly incorrectly encoded");
            break;
        case JSON_ERROR_RECURSION:
            throw new \Exception("JSON_ERROR_RECURSION :: One or more recursive references in the value to be encoded");
            break;
        case JSON_ERROR_INF_OR_NAN:
            throw new \Exception("JSON_ERROR_INF_OR_NAN :: One or more NAN or INF values in the value to be encoded");
            break;
        case JSON_ERROR_UNSUPPORTED_TYPE:
            throw new \Exception("JSON_ERROR_UNSUPPORTED_TYPE :: A value of a type that cannot be encoded was given");
            break;
        case JSON_ERROR_INVALID_PROPERTY_NAME:
            throw new \Exception("JSON_ERROR_INVALID_PROPERTY_NAME :: A property name that cannot be encoded was given");
            break;
        case JSON_ERROR_UTF16:
            throw new \Exception("JSON_ERROR_UTF16 :: Malformed UTF-16 characters, possibly incorrectly encoded");
            break;
        default:
            throw new \Exception("JSON_ERROR : Unknown error");
      }
    }

    /**
     * Return current Route object
     * @param string $route
     * @return Route
     * @throws \Exception
     */
    public function getCurrentRoute($route) {

        if(count($this->routes) > 0) {
            foreach ($this->routes as $Route) {
                if($Route->isMatching($route)) {
                    return $Route;
                }
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

    /**
     * @return string
     */
    public function getRootUrl()
    {
        return $this->root_url;
    }

}
