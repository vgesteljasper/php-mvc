<?php namespace App\Controller;

use Core\Controller as Controller;

class ErrorController extends Controller {

  /**
   * Method for 404 Not Found
   * @return \Core\Controller
   */
  public function notFound(): Controller
  {
    $this->view = ROOT.'view'.DS.'error'.DS.'show.php';
    $this->set('errorMessage', '404 Page Not Found');

    http_response_code(404);

    return $this;
  }

}
