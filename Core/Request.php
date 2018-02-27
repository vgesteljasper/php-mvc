<?php namespace Core;

class Request {

  public $headers;
  public $path;
  public $method;
  public $query;
  public $body;
  public $url;

  /**
   * Request constructor.
   */
  public function __construct() {
    $url = $this->generateURL();

    $this->path = parse_url($url, PHP_URL_PATH);
    $this->query = parse_url($url, PHP_URL_QUERY);
    $this->method = $_SERVER['REQUEST_METHOD'];

    $this->initHeaders();
    $this->initBody();
  }

  /**
   * Generate the current page url
   * @return string
   */
  private function generateURL(): string
  {
    $tsl = $_SERVER['HTTPS'] ?? null;
    $http = $tsl ? 'https://' : 'http://';
    return $http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  }

  /**
   * Get the request headers
   *
   * @return void
   */
  private function initHeaders(): void {
    $this->headers = apache_request_headers();
  }

  /**
   * Get the request body
   *
   * @return void
   */
  private function initBody():void {
    $this->body = !empty($_POST)
      ? $_POST
      : null;
  }

}
