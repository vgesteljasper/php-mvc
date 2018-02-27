<?php namespace Core;

class Route {

  public $path;
  public $method;
  public $wildcards;
  public $handler;

  /**
   * Route constructor.
   *
   * @param string $path
   * @param string $method
   * @param array  $wildcards
   * @param string $handler
   */
  public function __construct(string $path, string $method, array $wildcards, string $handler) {
    $this->path = $path;
    $this->method = $method;
    $this->wildcards = $wildcards;
    $this->handler = $handler;
  }

}
