<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Memoriamigration_Upgrader extends CRM_Memoriamigration_Upgrader_Base {

  public function install() {
    $this->executeSqlFile('sql/migration.sql');
  }

}
