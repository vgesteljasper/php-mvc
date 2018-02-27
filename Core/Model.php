<?php namespace Core;

class Model {

  private static $dbHost;
  private static $dbName;
  private static $dbUser;
  private static $dbPass;
  private static $sharedPDO;

  protected $pdo;

  /**
   * Model constructor.
   */
  function __construct()
  {
    self::$dbHost = $_ENV['DB_HOST'];
    self::$dbName = $_ENV['DB_NAME'];
    self::$dbUser = $_ENV['DB_USER'];
    self::$dbPass = $_ENV['DB_PASS'];

    if(empty(self::$sharedPDO)) {
      self::$sharedPDO = new \PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbPass);
      self::$sharedPDO->exec("SET CHARACTER SET utf8");
      self::$sharedPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      self::$sharedPDO->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }
    $this->pdo =& self::$sharedPDO;
  }
}
