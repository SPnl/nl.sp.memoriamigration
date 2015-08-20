<?php

class CRM_Memoriamigration_Config {

  public static $extName = 'nl.sp.memoriamigration';
  public static $tblName = 'civicrm_migration_memoria';

  public static function get($name) {
    return CRM_Core_BAO_Setting::getItem(self::$extName, $name);
  }

  public static function set($name, $value) {
    CRM_Core_BAO_Setting::setItem($value, self::$extName, $name);
  }
}