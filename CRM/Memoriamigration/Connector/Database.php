<?php

class CRM_Memoriamigration_Connector_Database extends \mysqli {

  /**
   * @var CRM_Memoriamigration_Connector_Database
   */
  private static $instance;

  /**
   * @return \CRM_Memoriamigration_Connector_Database
   */
  public static function singleton() {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * @throws \Exception Can't connect to database
   */
  public function __construct() {

    $dbhost = CRM_Memoriamigration_Config::get('memoriamigr_dbhost');
    $dbuser = CRM_Memoriamigration_Config::get('memoriamigr_dbuser');
    $dbpass = CRM_Memoriamigration_Config::get('memoriamigr_dbpass');
    $dbname = CRM_Memoriamigration_Config::get('memoriamigr_dbname');

    parent::__construct($dbhost, $dbuser, $dbpass, $dbname);

    if ($this->connect_error) {
      throw new \Exception('Could not connect to database (' . $dbhost . ').');
    }

    $this->set_charset('utf8');
  }

}