<?php namespace Core;

class Controller {

  protected $route;
  protected $request;
  protected $viewVars;
  protected $view;

  /**
   * Render the view with all needed variables.
   * @return \Core\Controller
   */
  public function render(): Controller
  {
    $this->set('info', $_GET['info'] ?? null);
    $this->set('error', $_GET['error'] ?? null);

    $this->createViewVarWithContent();
    $this->renderInLayout();

    return $this;
  }

  /**
   * Set a variable to be accessable in the view.
   * @param string  $name   | The name of the variable to set
   * @param any     $value  | The value to attach to the variable
   * @return \Core\Controller
   */
  public function set(string $name, $value): Controller
  {
    $this->viewVars[$name] = $value;

    return $this;
  }

  /**
   * Redirect the page to a different URL.
   * @param string $URL | The url
   * @return void
   */
  public function redirect(string $URL): void
  {
    header("Location: {$URL}");
    exit;
  }

  /**
   * Put the output of the correct subview to render in a variable to
   * later render in the layout view.
   * @return void
   */
  private function createViewVarWithContent()
  {
    extract($this->viewVars, EXTR_SKIP);

    ob_start();

    if (!$this->view) throw new Exception('View not set for this route.');
    require $this->view;

    $this->set('content', ob_get_clean());
  }

  /**
   * Render the layout view with the subview and variables needed.
   * @return void
   */
  private function renderInLayout(): void
  {
    extract($this->viewVars, EXTR_SKIP);

    // render the layout with extracted variables
    include ROOT.'view'.DS.'layout.php';
  }

}
