<?php

class CRM_Memoriamigration_Connector_Memoria {

  /**
   * @var CRM_Memoriamigration_Connector_Memoria
   */
  private static $instance;

  /**
   * @var CRM_Memoriamigration_Connector_Database
   */
  private $db;

  /**
   * @return \CRM_Memoriamigration_Connector_Memoria
   */
  public static function singleton() {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function __construct() {

    $this->db = CRM_Memoriamigration_Connector_Database::singleton();
  }

  public function getDetailPageInfo($groupId, $filterString) {
    return [
      'users'              => $this->getUsersForGroup($groupId),
      'memberCount'        => $this->getMemberCountForGroup($filterString),
      'migrateMemberCount' => $this->getMemberCountForGroup($filterString, TRUE),
      'propertyCount'      => $this->getPropertyCountForGroup($groupId),
      'manselCount'        => $this->getManualSelectionCountForGroup($groupId),
      'commentCount'       => $this->getCommentCountForGroup($filterString),
      'changeCount'        => $this->getChangeCountForGroup($groupId),
    ];
  }

  public function getMemoriaUserGroups() {
    $groups = $this->db->query("SELECT * FROM mem_usergroups ORDER BY name ASC");
    return $groups->fetch_all(MYSQLI_ASSOC);
  }

  public function setUserGroupReadonly($groupId, $status = 1) {
    return $this->db->query("UPDATE mem_usergroups SET is_readonly = '" . (int) $status . "' WHERE id = '" . (int) $groupId . "'");
  }

  public function setMemberMigrated($regnr) {
    return $this->db->query("UPDATE mem_adr SET is_migrated = 1 WHERE regnr = '" . (int) $regnr . "'");
  }

  public function resetMembersMigrated($filterString) {
    if (!$filterString) {
      return [];
    }

    return $this->db->query("UPDATE mem_adr SET is_migrated = 0 WHERE " . $filterString);
  }

  public function getDataByRegnr($regnr) {
    return [
      // Comments -> sort DESC, will be added as one comment in descending order
      'comments'   => $this->db->query("SELECT * FROM mem_commentxuser WHERE regnr = '" . (int) $regnr . "' ORDER BY bdat DESC")
        ->fetch_all(MYSQLI_ASSOC),
      // Properties -> show ASC, most recent data will overwrite older data for custom fields
      'properties' => $this->db->query("SELECT * FROM mem_propxuser WHERE regnr = '" . (int) $regnr . "' ORDER BY bdat ASC")
        ->fetch_all(MYSQLI_ASSOC),
      // Manual selections -> will be added to groups
      'mansel'     => $this->db->query("SELECT * FROM mem_manselxuser WHERE regnr = '" . (int) $regnr . "'")
        ->fetch_all(MYSQLI_ASSOC),
    ];
  }

  public function getGlobalProperties() {
    $properties = $this->db->query("SELECT * FROM mem_proplist WHERE groep = '0' ORDER BY name ASC");
    return $properties->fetch_all(MYSQLI_ASSOC);
  }

  public function getUsersForGroup($groupId) {
    $users = $this->db->query("SELECT * FROM mem_users WHERE groep = '" . (int) $groupId . "' ORDER BY `user` ASC");
    return $users->fetch_all(MYSQLI_ASSOC);
  }

  public function getRegnrsForGroup($filterString) {
    if (!$filterString) {
      return [];
    }

    $members = $this->db->query("SELECT regnr FROM mem_adr WHERE " . $filterString . " AND is_migrated = 0 ORDER BY regnr ASC");
    $regnrs = [];
    while ($member = $members->fetch_assoc()) {
      $regnrs[] = $member['regnr'];
    }
    return $regnrs;
  }

  public function getMemberCountForGroup($filterString, $checkMigrated = FALSE) {
    if (!$filterString) {
      return 0;
    }

    $members = $this->db->query("SELECT COUNT(regnr) FROM mem_adr WHERE " . $filterString . ($checkMigrated ? " AND is_migrated = 0 " : "") . " ORDER BY regnr ASC");
    return $members->fetch_array()[0];
  }

  public function getCommentCountForGroup($filterString) {
    if (!$filterString) {
      return 0;
    }

    $properties = $this->db->query("SELECT COUNT(DISTINCT regnr) FROM mem_commentxuser WHERE regnr IN (SELECT regnr FROM mem_adr WHERE " . $filterString . ")");
    return $properties->fetch_array()[0];
  }

  public function getPropertiesForGroup($groupId) {
    $properties = $this->db->query("SELECT * FROM mem_proplist WHERE groep = '" . (int) $groupId . "' ORDER BY name ASC");
    return $properties->fetch_all(MYSQLI_ASSOC);
  }

  public function getPropertyCountForGroup($groupId) {
    $properties = $this->db->query("SELECT COUNT(*) FROM mem_proplist WHERE groep = '" . (int) $groupId . "'");
    return $properties->fetch_array()[0];
  }

  public function getManualSelectionsForGroup($groupId) {
    $selections = $this->db->query("SELECT * FROM mem_mansel WHERE groep = '" . (int) $groupId . "' ORDER BY name ASC");
    return $selections->fetch_all(MYSQLI_ASSOC);
  }

  public function getManualSelectionCountForGroup($groupId) {
    $selections = $this->db->query("SELECT COUNT(*) FROM mem_mansel WHERE groep = '" . (int) $groupId . "'");
    return $selections->fetch_array()[0];
  }

  public function getChangeCountForGroup($groupId) {
    $change = $this->db->query("SELECT COUNT(*) FROM mem_mutlog WHERE groep = '" . (int) $groupId . "'");
    return $change->fetch_array()[0];
  }

}