<?php

require_once 'CRM/Core/Page.php';

class CRM_Memoriamigration_Page_Dashboard extends CRM_Core_Page {

  function run() {

    $mdata = CRM_Memoriamigration_Connector_MigrationData::singleton();

    $this->assign('groups', $mdata->getMigrationGroups());
    $this->assign('cronjobId', CRM_Memoriamigration_Config::getCronjobId());

    parent::run();
  }
}
