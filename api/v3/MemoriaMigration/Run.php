<?php

/**
 * MemoriaMigration.Run API
 * Voert een migratie van Memoria naar CiviCRM uit voor geselecteerde afdelingen.
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_memoria_migration_run($params = []) {

  return civicrm_api3_create_success(['message' => 'Not implemented yet.']);
}

/**
 * MemoriaMigration.Run API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_memoria_migration_run_spec(&$spec) {

}