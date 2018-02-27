<?php namespace App\Controller;

use Core\Controller as Controller;
use App\Model\ProjectModel as ProjectModel;

class ProjectController extends Controller {

  private $projectModel;

  /**
   * ProjectController constructor.
   */
  function __construct()
  {
     $this->projectModel = new ProjectModel;
  }

  /**
   * Method for homepage.
   * @return \Core\Controller
   */
  public function index(): Controller
  {
    $this->view = ROOT.'view'.DS.'project'.DS.'index.php';

    $this->set('title', 'Index');
    $this->set('testVariable', 'Hello Index');

    return $this;
  }

  /**
   * Method for project detail page.
   * @return \Core\Controller
   */
  public function show(): Controller
  {
    $this->view = ROOT.'view'.DS.'project'.DS.'show.php';

    $this->set('title', 'Project Detail');
    $this->set('testVariable', 'Hello Project');

    return $this;
  }

}
