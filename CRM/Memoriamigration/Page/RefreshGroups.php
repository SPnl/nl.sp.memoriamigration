<?php

require_once 'CRM/Core/Page.php';

class CRM_Memoriamigration_Page_RefreshGroups extends CRM_Core_Page {

  function run() {

    CRM_Memoriamigration_Connector_MigrationData::singleton()->refreshGroups();
    parent::run();

    CRM_Core_Session::setStatus('De gebruikersgroepen zijn ververst vanuit Memoria.', 'Groepen ververst', 'info');
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/memoria'));
  }
}
