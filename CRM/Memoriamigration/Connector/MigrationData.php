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
      ORDER BY FIELD(status,'migrating','queued','error','migrated','testmigrated','notmigrated','none'), cmm.usergroup_name ASC");
    while ($res->fetch()) {
      $ret[] = (array)$res;
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

  public function getMigrationGroupsByStatus($status = 'queued', $limit = 1) {
    $ret = [];
    $res = CRM_Core_DAO::executeQuery("SELECT * FROM civicrm_migration_memoria cmm WHERE status = %1 ORDER BY cmm.usergroup_name ASC LIMIT %2", [1 => [$status, 'String'], 2 => [$limit, 'Integer']]);
    while ($res->fetch()) {
      $ret[] = (array)$res;
    }
    return $ret;
  }

  public function countMigrationGroupsByStatus() {
    $ret = [];
    $res = CRM_Core_DAO::executeQuery("SELECT COUNT(*) AS count, status FROM civicrm_migration_memoria cmm GROUP BY status");
    while ($res->fetch()) {
      $ret[] = [
        'count'  => $res->count,
        'status' => $res->status,
      ];
    }
    return $ret;
  }

  public function setMigrationGroupData($id, $params = []) {

    $q = 'UPDATE civicrm_migration_memoria SET ';
    foreach ($params as $key => $value) {
      if (!in_array($key, ['usergroup_id', 'usergroup_name', 'usergroup_filter', 'status', 'migration_type', 'migration_spgeo', 'migration_users', 'added', 'migrated', 'log'])) {
        continue;
      }
      $q .= $key . " = '" . htmlspecialchars($value, ENT_QUOTES) . "', "; // Lekker 1999.
    }
    $q = substr($q, 0, -2);
    $q .= " WHERE id = '" . (int) $id . "'";
    return CRM_Core_DAO::executeQuery($q);
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