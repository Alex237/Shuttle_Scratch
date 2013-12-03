<?php

/**
 * Router description
 * 
 * <p>The Router core perform uri routing<p>
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 */
class Router
{

    /**
     * The router instance
     * 
     * @var \Router
     */
    private static $instance = null;

    /**
     * The mapped routes
     * 
     * @var array
     */
    private $routes = array();

    /**
     * The request uri
     * 
     * @var string
     */
    private $request;

    /**
     * Construct
     * 
     * @param string $domain The uri base to remove
     */
    private function __construct() {
        $this->request = $_SERVER['REQUEST_URI'];
        $this->mapRoutes();
    }

    /**
     * Get the unique router instance
     * 
     * @return \Router
     */
    public static function getInstance() {

        if (is_null(self::$instance)) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    /**
     * Add a route to the router
     * 
     * @param Route $route The route to add
     */
    public function addRoute(Route $route) {
        if (!in_array($route, $this->routes)) {
            $this->routes[] = $route;
        }
    }

    /**
     * 
     * @param type $request The request uri
     * @return Route The matched route
     * @throws \RuntimeException
     */
    public function findRoute($request) {

        foreach ($this->routes as $route) {

            if (($values = $route->match($request)) !== FALSE) {
                if ($route->hasParams()) {
                    $paramsNames = $route->getParams();
                    $paramsList = array();

                    foreach ($values as $key => $value) {
                        if ($key !== 0) {
                            $paramsList[$paramsNames[$key - 1]] = $value;
                        }
                    }

                    $route->setValues($paramsList);
                }

                return $route;
            }
        }

        throw new \RuntimeException('No route matching the request');
    }

    /**
     * Map all routes
     * 
     */
    public function mapRoutes() {
        $xml = new \DOMDocument;
        $xml->load('./config/routes.xml');

        $routes = $xml->getElementsByTagName('route');

        foreach ($routes as $route) {
            $params = array();

            if ($route->hasAttribute('params')) {
                $params = explode(',', $route->getAttribute('params'));
            }

            $this->addRoute(new Route($route->getAttribute('pattern'), $route->getAttribute('controller'), $route->getAttribute('action'), $params));
        }
    }

    /**
     * 
     * @return void
     */
    public function run() {
        try {
            $matchedRoute = $this->findRoute($this->request);
        } catch (\RuntimeException $e) {
            include './404.html';
            die;
        }

        $controller_name = $matchedRoute->getController();
        $action_name = $matchedRoute->getAction();

        require_once './controller/' . $controller_name . '.php';
        $controller = new $controller_name();

        return call_user_func_array(array($controller, $action_name), $matchedRoute->getValues());
    }

}

/**
 * Route description
 * 
 * <p>The Route core is a component of the Router core<p>
 * 
 * @author Fabien Morchoisne <f.morchoisne@insta.fr>
 */
class Route
{

    /**
     *
     * @var string The expected uri pattern
     */
    private $pattern;

    /**
     *
     * @var string The controller name
     */
    private $controller;

    /**
     *
     * @var string The action name
     */
    private $action;

    /**
     *
     * @var array The request parameters 
     */
    private $params;

    /**
     *
     * @var array The parameters values
     */
    private $values = array();

    public function __construct($pattern, $controller, $action, array $params) {
        $this->setPattern($pattern);
        $this->setController($controller);
        $this->setAction($action);
        $this->setParams($params);
    }

    /**
     * 
     * @return boolean
     */
    public function hasParams() {
        return !empty($this->params);
    }

    /**
     * 
     * @param string $request The request uri
     * @return boolean
     */
    public function match($request) {
        if (preg_match('#^' . $this->pattern . '$#', $request, $matches)) {
            return $matches;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param string $pattern The expected uri pattern
     */
    public function setPattern($pattern) {
        if (is_string($pattern)) {
            $this->pattern = $pattern;
        }
    }

    /**
     * 
     * @return string  The expected uri pattern
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * 
     * @param string $controller The controller name
     */
    public function setController($controller) {
        if (is_string($controller)) {
            $this->controller = $controller;
        }
    }

    /**
     * 
     * @return string The controller name
     */
    public function getController() {
        return ucfirst($this->controller);
    }

    /**
     * 
     * @param string $action The action name
     */
    public function setAction($action) {
        if (is_string($action)) {
            $this->action = $action;
        }
    }

    /**
     * 
     * @return string The action name
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 
     * @param array $params The request parameters
     */
    public function setParams(array $params) {
        $this->params = $params;
    }

    /**
     * 
     * @return array The request parameters
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * 
     * @param array $values The parameters values
     */
    public function setValues(array $values) {
        $this->values = $values;
    }

    /**
     * 
     * @return array The parameters values
     */
    public function getValues() {
        return $this->values;
    }

}