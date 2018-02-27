<?php namespace Core;

use Core\Route as Route;

class Router {
  public $routes;
  public $request;
  private $matchingRoute;

  /**
   * Router constructor.
   * 
   * @return void;
   */
  public function dispatch(): void {
    $this->request = new Request;

    $this->initRouteMatching();
    $this->callFallbackHandlerIfNeeded();
    $this->callRouteHandler();
  }

  /**
   * PHP magic function for catching Router methods like "get", "post", ...
   *
   * @param string $method
   * @param array  $args
   *
   * @return \Core\Route|null
   */
  public function __call(string $method, array $args): ?Route {
    // return if the method is not a valid route method
    $validRouteMethods = ['get', 'post', 'put', 'patch','delete', 'options'];
    if (!in_array($method, $validRouteMethods, true)) {
      return null;
    }

    // get the variables from the args array
    $path = $args[0];
    $handler = $args[1];

    // parse the route and add to routes array
    $route = $this->parseRoute($method, $path, $handler);

    return $route;
  }

  /**
   * Set the fallback route
   * 
   * @param string $handler | The fallback handler
   * 
   * @return void
   */
  public function fallback(string $handler): void
  {
    $this->fallbackHandler = $handler;
  }

  /**
   * Parse the route
   *
   * @param $method
   * @param $path
   * @param $handler
   *
   * @return \Core\Route
   */
  private function parseRoute($method, $path, $handler): Route
  {
    // match all the wildcard "{...}" sections in the path
    preg_match_all('/\{[^\/]+\}/', $path, $matches);
    $count = count($matches);

    // make sure the regex path is escaped with backslashes
    $path = preg_quote($path, '/');

    // replace all the wildcards with regex wildcards
    // so we can match our request route later
    // (some strange excaping going on because of the preg_quote)
    $path = preg_replace('/\\\{[^\/]+?\\\}/', '([^\/]+)', $path);

    // make a regex from the path
    $path = '/^' . $path . '$/';

    // transform method to uppercase
    $method = strtoupper($method);

    // all the matches we counted above have the {} brackets around them
    // we will remove them here so we have the pure names of the wildcards
    $wildcards = [];
    forEach($matches[0] as $match) {
      $wildcards[] = preg_replace('/(\{|\})/', '', $match);
    }

    // add a new Route to the routes array
    $route = new Route($path, $method, $wildcards, $handler);
    $this->routes[] = $route;

    return $route;
  }

  /**
   * Start the mechanisms for matching routes
   *
   * @return void
   */
  private function initRouteMatching(): void
  {
    // get the first matching route
    $result = $this->firstMatchingRoute();
    $route = $result['route'] ?? null;

    if (!$route) return;

    // add the wildcard values to the route array
    $route = $this->fulfillWildcards($result);

    $this->matchingRoute = $route;
  }

  /**
   * Call the fallback route handler if no route matched AND the fallback
   * handler is defined.
   * 
   * @return void
   */
  private function callFallbackHandlerIfNeeded(): void
  {
    if (!$this->matchingRoute && $this->fallbackHandler) {
      $this->callRouteHandler($this->fallbackHandler);
    }
  }

  /**
   * Execute the correct method on the correct Controllers
   *
   * @param string $handler
   * 
   * @return void
   */
  private function callRouteHandler(string $handler = null): void
  {
    // generate the controller and method if they were not provided
    $h = $this->parseHandler($handler);

    $controller = 'App\\Controller\\'.$h['controller'];
    $method = $h['method'];

    // add the wildcards to the request
    if ($this->matchingRoute) {
      $this->request->url['params'] = $this->matchingRoute->wildcards;
    }

    (new $controller)
      ->$method()
      ->set('request', $this->request)
      ->render();

    exit;
  }

  /**
   * Parse the route handler to get the controller and the method.
   * Use the provided handler or the matching route handler if no provider
   * is provided.
   * 
   * @param string $handler
   * 
   * @return array
   */
  private function parseHandler(string $handler = null): array
  {
    $parsedHandler = explode('@', $handler ?: $this->matchingRoute->handler);

    return [
      'controller' => $parsedHandler[0],
      'method' => $parsedHandler[1],
    ];
  }

  /**
   * Find the first matching route object
   * If no Route matches, return null
   *
   * @return array|null
   */
  private function firstMatchingRoute(): ?array {
    $matchingRoute = null;

    // loop over the router routes array
    foreach($this->routes as $route) {
      // see if the route is a match
      // this method returns the array of matches or null
      $matches = $this->routeMatches($route);
      if ($matches !== null) {
        $matchingRoute = [
          'route' => $route,
          'matches' => $matches
        ];
        break;
      }
    }

    // return the matched route or return the initial null value
    return $matchingRoute;
  }

  /**
   * Fill in the wildcard array so we know the names of the variables
   * extracted from the route path
   *
   * @param array $result
   *
   * @return \Core\Route
   */
  private function fulfillWildcards(array $result): Route {
    $route = $result['route'];

    // the matches from the regex match
    $matches = $result['matches'];

    // the wildcards on the route array
    $routeWildcards = $route->wildcards;

    // generate the wildcard matches
    // eg: [ 'slug_id' => 'marv' ]
    $updatedWildcards = [];
    for ($i = 0; $i < count($matches); $i++) {
      $updatedWildcards[$routeWildcards[$i]] = $matches[$i];
    }
    
    // replace the wildcards on the $route array with the updated ones
    $route->wildcards = $updatedWildcards;

    return $route;
  }

  /**
   * Check weather the Route matches.
   * If it does, return the matches from the Route path wildcards.
   * Else return null
   *
   * @param \Core\Route $route
   *
   * @return array|null
   */
  private function routeMatches(Route $route): ?array {
    $path = $route->path;
    $method = $route->method;

    // if the current path doesn't match the route path
    if (!preg_match($path, $this->request->path, $matches)) return null;

    // if the current method doesn't match the route method
    if ($this->request->method !== $method) return null;

    // return true (the route matches withour the first one which is the full path)
    return array_slice($matches, 1);
  }

}
