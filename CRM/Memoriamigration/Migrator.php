<?php

/**
 * Class CRM_Memoriamigration_Migrator
 * (First version of an) actual migration script.
 */
class CRM_Memoriamigration_Migrator {

  private $mgroup;
  private $memoria;
  private $migrationData;

  private $jobId;
  private $log = [];

  private $propertyGroupMapping = [];
  private $manselGroupMapping = [];
  private $propertyCustomDataMapping = [];

  public function __construct($mgroup = []) {

    $this->mgroup = $mgroup;

    $this->memoria = CRM_Memoriamigration_Connector_Memoria::singleton();
    $this->migrationData = CRM_Memoriamigration_Connector_MigrationData::singleton();

    $this->jobId = CRM_Memoriamigration_Config::getCronjobId();
    $this->propertyCustomDataMapping = CRM_Memoriamigration_Config::getPropertyCustomDataMapping();
  }

  /* ----- MAIN MIGRATION LOOP ----- */

  public function run() {

    $group = &$this->mgroup;

    // Set group status to 'migrating'
    $this->migrationData->setMigrationGroupData($group['id'], ['status' => 'migrating']);

    try {

      // Fetch members for this group
      $members = $this->memoria->getRegnrsForGroup($group['usergroup_filter']);

      $this->logToJoblog('Starting ' . $group['migration_type'] . ' migration for group ' . $group['usergroup_name'] . ' (' . $group['usergroup_id'] . '). Group contains ' . count($members) . ' members.');

      // Step 1. Walk this user group's custom properties and manual selections and add them as groups
      $this->importGroups();

      // Step 2. Walk this group's members and migrate their data one by one
      // (We migreren per lid zodat we het betreffende lid als gemigreerd kunnen markeren en we daar opnieuw kunnen beginnen als er iets misgaat.
      // Dat betekent wel dat aan het eind nog iets moeten regelen voor leden die ni?t tot een gemigreerde afdeling behoren.)
      $this->importMemberData($members);

      // Update migration status
      $this->logToJoblog(ucfirst($group['migration_type']) . ' migration for group ' . $group['usergroup_id'] . ' completed.', "Log data:\n" . $this->getLog());

      $status = ($group['migration_type'] == 'test' ? 'testmigrated' : 'migrated');
      $this->migrationData->setMigrationGroupData($group['id'], [
        'status'   => $status,
        'migrated' => time(),
        'log'      => $this->getLog(),
      ]);

      if ($group['migration_type'] == 'live') {
        $this->memoria->setUserGroupReadonly($group['usergroup_id'], 1);
      }
      else {
        $this->memoria->resetMembersMigrated($group['usergroup_filter']);
      }

      return TRUE;

    } catch (\Exception $e) {

      // If an exception occurs, set error status and show exception in log
      $this->logToJoblog('An error occurred migrating group ' . $group['usergroup_id'] . ': ' . $e->getMessage() . ' (in ' . $e->getFile() . ' on ' . $e->getLine() . ').', "Backtrace:\n" . $e->getTraceAsString() . "\n\nLog data:\n" . $this->getLog());

      $this->migrationData->setMigrationGroupData($group['id'], [
        'status' => 'error',
        'log'    => $this->getLog(),
      ]);

      return FALSE;
    }
  }

  /* ----- GROUP IMPORT FUNCTIONS ----- */

  private function importGroups() {

    $properties = $this->memoria->getPropertiesForGroup($this->mgroup['usergroup_id']);
    $this->propertyGroupMapping = $this->createAndMapGroups($properties);
    // echo "Property mapping:\n" . print_r($this->propertyGroupMapping, true) . "\n";

    $this->log('importProperties: created ' . count($this->propertyGroupMapping) . ' groups.');

    $mansel = $this->memoria->getManualSelectionsForGroup($this->mgroup['usergroup_id']);
    $this->manselGroupMapping = $this->createAndMapGroups($mansel);
    // echo "Mansel mapping:\n" . print_r($this->manselGroupMapping, true) . "\n";

    $this->log('importManualSelections: created ' . count($this->manselGroupMapping) . ' groups.');

    return TRUE;
  }

  private function createAndMapGroups($groupList) {

    $parentGroup = $this->getParentGroup();
    if (!$parentGroup) {
      $this->log('Could not import group list: parent group does not exist or could not be created.');
      return FALSE;
    }

    $groupMapping = [];

    if (count($groupList) > 0) {
      foreach ($groupList as $groupItem) {
        $newGroupId = $this->createGroup($parentGroup['short_name'] . ' ' . $groupItem['name'], 'Import: ' . $groupItem['name'], $parentGroup['id']);
        if (!$newGroupId) {
          $this->log('Warning: no group id for group ' . $groupItem['name']);
          continue;
        }
        $groupMapping[$groupItem['id']] = $newGroupId;
      }
    }

    return $groupMapping;
  }

  private function getParentGroup() {

    $geo_id = $this->mgroup['migration_spgeo'];
    if (!$geo_id) {
      return FALSE;
    }

    $customFieldAfdGroep = civicrm_api3('CustomField', 'getvalue', ['name' => 'afdeling_groep', 'return' => 'id']);
    $customFieldAfdGroepName = 'custom_' . $customFieldAfdGroep;
    $params = ['contact_id' => $geo_id, 'return'     => 'contact_id,display_name,' . $customFieldAfdGroepName];
    $spgeo = civicrm_api3('Contact', 'getsingle', $params);
    // $this->log('Geo return data for contact_id,display_name,' . $customFieldAfdGroepName . ' with contact id ' . $geo_id . ' result ' . $spgeo[$customFieldAfdGroepName] . ' -- params ' . print_r($params, true) . ', result ' . print_r($spgeo, true));

    if (!empty($spgeo[$customFieldAfdGroepName])) {
      $parentGroupId = $spgeo[$customFieldAfdGroepName];
    }
    else {
      $parentGroupId = $this->createGroup($spgeo['display_name'], $spgeo['display_name']);
      if ($parentGroupId) {
        civicrm_api3('Contact', 'create', [
          'contact_id'              => $geo_id,
          $customFieldAfdGroepName =>  $parentGroupId,
        ]);
        $this->log('Created new parent group: id ' . $parentGroupId);
      }
      else {
        return FALSE;
      }
    }

    $parentGroup = civicrm_api3('Group', 'getsingle', ['id' => $parentGroupId]);
    $parentGroup['short_name'] = str_replace(['SP-afdeling ', 'SP-regio ', 'SP-provincie ', 'SP-werkgroep ', 'Afdelingsgroepen '], '', $parentGroup['title']);
    // $this->log('Parent group data: ' . print_r($parentGroup, true));
    return $parentGroup;
  }

  private function createGroup($name, $title, $parentGroupId = NULL) {
    $title = substr(trim($title), 0, 64);
    $name  = substr(strtolower(preg_replace('/[^a-zA-Z0-9\._-]/', '-', trim($name))), 0, 64);

    try {
      // Check if group exists and if so, return group ID
      $groupId = civicrm_api3('Group', 'getvalue', [
        'name'   => $name,
        'return' => 'id',
      ]);
      $this->log('Group exists: name \'' . $name . '\'' . ($parentGroupId ? ', parent id ' . $parentGroupId : ''));
      return $groupId;

    } catch (\CiviCRM_API3_Exception $e) {
      // Otherwise, create group
      $this->log('Creating group: name \'' . $name . '\', title \'' . $title . '\'' . ($parentGroupId ? ', parent id ' . $parentGroupId : ''));

      try {
        $ret = civicrm_api3('Group', 'create', [
          'name'  => $name,
          'title' => $title
        ]);
      } catch (\CiviCRM_API3_Exception $e) {
        $this->log("Could not create group (API error): " . $title . " (" . $name . ") - " . $e->getMessage());
        return FALSE;
      }

      if ($parentGroupId) {
        // Create mapping
        try {
          if (!$ret || $ret['is_error']) {
            throw new \Exception("Could not set group parent: group was not created.");
          }

          $retnest = civicrm_api3('GroupNesting', 'create', [
            'child_group_id'  => $ret['id'],
            'parent_group_id' => $parentGroupId,
          ]);
        } catch (\CiviCRM_API3_Exception $e) {
          $this->log("Could not set group parent (API error): " . $title . " (" . $name . ", parent id " . $parentGroupId . ") - " . $e->getMessage() . " - " . print_r($parentGroupId, TRUE));
        }
      }

      // Return group ID
      return $ret['id'];
    }

  }

  /* ----- DATA IMPORT FUNCTIONS ----- */

  private function importMemberData($members, $markAsMigrated = FALSE) {

    foreach ($members as $regnr) {

      $data = $this->memoria->getDataByRegnr($regnr);
      foreach ($data as $type => $records) {

        switch ($type) {

          case 'comments':
            $comment = '';
            foreach ($records as $r) {
              $comment .= $r['comment'] . " (" . $r['buser'] . ", " . date('d-m-Y', strtotime($r['bdat'])) . ")\n";
            }
            $this->addComment($regnr, $comment);
            break;

          case 'properties':
            $setCustomDataProperties = [];
            foreach ($records as $record) {
              if (array_key_exists($record['property'], $this->propertyGroupMapping)) {
                $this->addToGroup($regnr, $this->propertyGroupMapping[$record['property']]);
              }
              elseif (array_key_exists($record['property'], $this->propertyCustomDataMapping)) {
                $setCustomDataProperties[] = $record['property'];
              }
              else {
                // Niet per se erg -> kan ook betekenen dat iemand bijv in een landelijke selectie zit
                $this->log("Could not add property {$record['property']} to {$regnr}: not mapped.");
              }
            }
            if (count($setCustomDataProperties) > 0) {
              $this->addPropertiesAsCustomData($regnr, $setCustomDataProperties);
            }
            break;

          case 'mansel':
            foreach ($records as $record) {
              if (array_key_exists($record['selid'], $this->manselGroupMapping)) {
                $this->addToGroup($regnr, $this->manselGroupMapping[$record['selid']]);
              }
              else {
                $this->log("Could not add mansel entry {$record['selid']} to {$regnr}: not mapped.");
              }
            }
            break;

        }
      }

      // $this->log('Imported member ' . $regnr . '.');
      $this->memoria->setMemberMigrated($regnr);

    }

    $this->log('importMemberData: imported data for ' . count($members) . ' members.');

  }

  private function addToGroup($contact_id, $group_id) {
    try {
      return civicrm_api3('GroupContact', 'create', [
        'contact_id' => $contact_id,
        'group_id'   => $group_id,
      ]);
    } catch (\CiviCRM_API3_Exception $e) {
      $this->log('API error in addToGroup: ' . $e->getMessage() . ' (' . $contact_id . ').');
    }
  }

  private function addPropertiesAsCustomData($contact_id, $property_ids) {
    $params = [
      'contact_id' => $contact_id,
    ];
    foreach ($property_ids as $property_id) {
      list($fieldName, $fieldValue) = $this->propertyCustomDataMapping[$property_id];
      if (isset($params[$fieldName])) {
        $params[$fieldName] = [$params[$fieldName]];
        $params[$fieldName][] = $fieldValue;
      }
      else {
        $params[$fieldName] = $fieldValue;
      }
    }
    try {
      return civicrm_api3('Contact', 'create', $params);
    } catch (\CiviCRM_API3_Exception $e) {
      $this->log('API error in addPropertiesAsCustomData: ' . $e->getMessage() . ' (' . $contact_id . ').');
    }
  }

  private function addComment($contact_id, $comment) {

    try {
      $chk = civicrm_api3('Note', 'getsingle', [
        'entity_table' => "civicrm_contact",
        'entity_id'    => $contact_id,
        'subject'      => 'Import Memoria',
      ]);
      if ($chk) {
        return TRUE; // Already exists
      }
    } catch (\CiviCRM_API3_Exception $e) {
      // Create new
    }

    try {
      if (trim($comment) == '') {
        return FALSE;
      }
      return civicrm_api3('Note', 'create', [
        'entity_table' => "civicrm_contact",
        'entity_id'    => $contact_id,
        'subject'      => 'Import Memoria',
        'note'         => $comment,
      ]);
    } catch (\CiviCRM_API3_Exception $e) {
      $this->log('API error in addComment: ' . $e->getMessage() . ' (' . $contact_id . ').');
    }
  }

  /* ----- LOG FUNCTIONS ----- */

  private function log($message) {
    $this->log[] = $message;
    echo $message . "\n"; // For CLI use
  }

  private function getLog() {
    return implode("\n", $this->log);
  }

  private function logToJoblog($message, $data = '') {
    // Als cron naar het job log mag loggen, waarom zou ik dat dan niet mogen?

    $dao = new CRM_Core_DAO_JobLog();

    $dao->domain_id = CRM_Core_Config::domainID();
    $dao->job_id = $this->jobId;
    $dao->name = 'Memoria Migration';
    $dao->description = substr($message, 0, 235);
    if (strlen($message) > 235) {
      $dao->description .= " (...)";
    }
    $dao->data = $data;
    $dao->save();
  }
}
