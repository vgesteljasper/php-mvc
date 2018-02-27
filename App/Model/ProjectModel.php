<?php namespace App\Model;

use Core\Model as Model;

class ProjectModel extends Model
{

  /**
   * Get all the projects.
   */
  public function getAll()
  {
    $sql = "SELECT * FROM `mh17v2_projects`";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Get one project by its slug.
   * @param string $projectSlug | The slug of the desired project
   */
  public function getBySlug(string $projectSlug)
  {
    $sql = "SELECT * FROM `mh17v2_products` WHERE `slug` = :slug";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':slug', $projectSlug);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

}
