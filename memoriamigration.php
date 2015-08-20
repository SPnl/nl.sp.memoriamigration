<?php
/**
 * nl.sp.memoriamigration: tijdelijke extensie voor het migreren van gegevens van afdelingen vanuit Memoria.
 */

require_once 'memoriamigration.civix.php';


/**
 * Implementation of hook_civicrm_navigationMenu *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function memoriamigration_civicrm_navigationMenu(&$params) {

  foreach ($params as &$menu) {
    if (array_key_exists('attributes', $menu) && $menu['attributes']['name'] == 'Administer') {

      $maxKey = (max(array_keys($menu['child'])));
      $menu['child'][$maxKey + 1] = [
        'attributes' => [
          'label'      => ts('Migratie Memoria'),
          'name'       => ts('Migratie Memoria'),
          'url'        => 'civicrm/admin/memoria',
          'permission' => 'administer CiviCRM',
          'operator'   => NULL,
          'separator'  => NULL,
          'parentID'   => 2,
          'navID'      => $maxKey + 1,
          'active'     => 1,
        ],
      ];
    }
  }
}

/* Default Civix hooks follow */

function memoriamigration_civicrm_config(&$config) {
  _memoriamigration_civix_civicrm_config($config);
}

function memoriamigration_civicrm_xmlMenu(&$files) {
  _memoriamigration_civix_civicrm_xmlMenu($files);
}

function memoriamigration_civicrm_install() {
  _memoriamigration_civix_civicrm_install();
}

function memoriamigration_civicrm_uninstall() {
  _memoriamigration_civix_civicrm_uninstall();
}

function memoriamigration_civicrm_enable() {
  _memoriamigration_civix_civicrm_enable();
}

function memoriamigration_civicrm_disable() {
  _memoriamigration_civix_civicrm_disable();
}

function memoriamigration_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _memoriamigration_civix_civicrm_upgrade($op, $queue);
}

function memoriamigration_civicrm_managed(&$entities) {
  _memoriamigration_civix_civicrm_managed($entities);
}

function memoriamigration_civicrm_caseTypes(&$caseTypes) {
  _memoriamigration_civix_civicrm_caseTypes($caseTypes);
}

function memoriamigration_civicrm_angularModules(&$angularModules) {
  _memoriamigration_civix_civicrm_angularModules($angularModules);
}

function memoriamigration_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _memoriamigration_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
