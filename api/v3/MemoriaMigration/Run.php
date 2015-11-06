<?php

/**
 * MemoriaMigration.Run API
 * Voert een migratie van Memoria naar CiviCRM uit voor afdelingen in de wachtrij.
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_memoria_migration_run($params = []) {

  // return civicrm_api3_create_success(['message' => 'Call disabled while not working on it.']);

  $migrationData = CRM_Memoriamigration_Connector_MigrationData::singleton();

  $groups = $migrationData->getMigrationGroupsByStatus('queued', 999);
  if ($groups && count($groups) > 0) {
    foreach ($groups as $group) {

      $migrator = new CRM_Memoriamigration_Migrator($group);
      $migrator->run();

      unset($migrator);
    }

    return civicrm_api3_create_success(['message' => 'Ran migration script for ' . count($groups) . ' groups.']);
  }
  else {
    return civicrm_api3_create_success(['message' => 'Nothing to do.']);
  }
}

/**
 * MemoriaMigration.Run API specification (optional)
 * This is used for documentation and validation.
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_memoria_migration_run_spec(&$spec) {

}