<?php

require_once 'CRM/Core/Page.php';

class CRM_Memoriamigration_Page_Detail extends CRM_Core_Page {

  function run() {

    $id = (int) $_GET['id'];

    $migrationData = CRM_Memoriamigration_Connector_MigrationData::singleton();
    $group = $migrationData->getMigrationGroup($id);

    if (!$group) {
      CRM_Core_Session::setStatus('Groep bestaat niet: ' . $id, '', 'alert');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/memoria'));
    }

    CRM_Utils_System::setTitle(ts('Memoria-groep ' . $group->usergroup_name));
    $this->assign('group', $group);

    $memoria = CRM_Memoriamigration_Connector_Memoria::singleton();
    $detail = $memoria->getDetailPageInfo($group->usergroup_id, $group->usergroup_filter);
    $this->assign('detail', $detail);

    parent::run();
  }
}
