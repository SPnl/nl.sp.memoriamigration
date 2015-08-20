<?php

require_once 'CRM/Core/Page.php';

class CRM_Memoriamigration_Page_Do extends CRM_Core_Page {

  function run() {

    $action = $_GET['action'];
    $this->assign('action', $action);

    $id = (int) $_GET['id'];

    $memoria = CRM_Memoriamigration_Connector_Memoria::singleton();
    $migrationData = CRM_Memoriamigration_Connector_MigrationData::singleton();

    $group = $migrationData->getMigrationGroup($id);
    $this->assign('group', $group);

    if (!$group) {
      CRM_Core_Session::setStatus('Groep bestaat niet: ' . $id, '', 'alert');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/memoria'));
    }

    switch ($action) {

      case 'migration_confirm':
        CRM_Utils_System::setTitle(ts('Migratie bevestigen'));
        $detail = $memoria->getDetailPageInfo($group->usergroup_id, $group->usergroup_filter);
        $this->assign('detail', $detail);
        break;

      case 'migration':
        CRM_Utils_System::setTitle(ts('Migratie ingepland'));
        $migrationData->setMigrationGroupStatus($id, 'queued');
        break;

      case 'readonly':
        CRM_Utils_System::setTitle(ts('Alleen-lezen maken'));
        // TODO
        break;

      default:
        CRM_Utils_System::setTitle(ts('Ongeldige actie'));
        break;
    }

    parent::run();
  }
}
