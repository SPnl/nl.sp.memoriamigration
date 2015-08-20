<?php

class CRM_Memoriamigration_Connector_MigrationData {
  /**
   * @var CRM_Memoriamigration_Connector_MigrationData
   */
  private static $instance;

  /**
   * @return \CRM_Memoriamigration_Connector_MigrationData
   */
  public static function singleton() {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function getMigrationGroups() {

    $ret = [];
    $res = CRM_Core_DAO::executeQuery("SELECT * FROM civicrm_migration_memoria cmm
      ORDER BY FIELD(status, 'migrating', 'queued', 'migrated', 'notmigrated'), cmm.usergroup_name ASC");
    while ($res->fetch()) {
      $ret[] = [
        'id'               => $res->id,
        'usergroup_id'     => $res->usergroup_id,
        'usergroup_name'   => $res->usergroup_name,
        'usergroup_filter' => $res->usergroup_filter,
        'status'           => $res->status,
      ];
    }
    return $ret;
  }

  public function getMigrationGroup($id) {
    $res = CRM_Core_DAO::executeQuery("SELECT cmm.* FROM civicrm_migration_memoria cmm WHERE id = %1", [1 => [$id, 'Integer']]);
    if ($res->fetch()) {
      return $res;
    }
    return NULL;
  }

  public function setMigrationGroupStatus($id, $status) {
    return CRM_Core_DAO::executeQuery("UPDATE civicrm_migration_memoria SET status = %1 WHERE id = %2",
      [1 => [$status, 'String'], 2 => [$id, 'Integer']]);
  }

  public function refreshGroups() {

    $groups = CRM_Memoriamigration_Connector_Memoria::singleton()
      ->getMemoriaUserGroups();

    foreach ($groups as $group) {

      // Wat een... idiote databasefuncties zijn het toch. Nou, dit werkt. Soort van.
      $count = CRM_Core_DAO::singleValueQuery("SELECT COUNT(cmm.id) FROM civicrm_migration_memoria cmm WHERE cmm.usergroup_id = %1", [
        1 => [$group['id'], 'Integer'],
      ]);

      if ($count == 0) {
        $res = CRM_Core_DAO::executeQuery("INSERT INTO civicrm_migration_memoria (usergroup_id, usergroup_name, usergroup_filter, status, added) VALUES (%1, %2, %3, %4, %5)", [
          1 => [$group['id'], 'Integer'],
          2 => [$group['name'], 'String'],
          3 => [$group['filter'], 'String'],
          4 => ['notmigrated', 'String'],
          5 => [time(), 'Integer'],
        ]);
      };
    }

    return TRUE;
  }
}