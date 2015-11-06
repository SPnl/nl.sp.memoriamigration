<?php

require_once 'CRM/Core/Form.php';

/**
 * Class CRM_Memoriamigration_Form_Migrate
 * Formulier om migratie te configureren en in te plannen.
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Memoriamigration_Form_Migrate extends CRM_Core_Form {

  private $groupId;

  public function preProcess() {

    $this->groupId = (int) $_REQUEST['id'];

    $memoria = CRM_Memoriamigration_Connector_Memoria::singleton();
    $migrationData = CRM_Memoriamigration_Connector_MigrationData::singleton();

    $group = $migrationData->getMigrationGroup($this->groupId);
    $this->assign('group', $group);

    if (!$group) {
      CRM_Core_Session::setStatus('Groep bestaat niet: ' . $this->groupId, '', 'alert');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/memoria'));
    }

    CRM_Utils_System::setTitle(ts('Migreer ' . $group->usergroup_name));

    $this->assign('detail', $memoria->getDetailPageInfo($group->usergroup_id, $group->usergroup_filter));

    parent::preProcess();
  }

  public function buildQuickForm() {

    // Veld 0. De groeps-ID
    $this->add('hidden', 'id', NULL, NULL, TRUE);

    // Veld 1. Kies type migratie
    $choices = ['test' => 'Test', 'live' => 'Live'];
    $this->add('select', 'migration_type', 'Type migratie', $choices, TRUE);

    // Veld 2. Kies afdeling/regio/provincie waar groepen onder vallen
    $this->add('select', 'migration_spgeo', 'Geo-niveau', $this->getSPOrganisationChoices(), FALSE);

    // Veld 3. Kies of je gebruikers wilt importeren -> misschien later
    // $this->add('select', 'migration_users', 'Gebruikers importeren', [0 => 'nee', 1 => 'ja']);

    $this->addButtons([['type' => 'submit', 'name' => 'Migratie plannen', 'isDefault' => TRUE]]);

    $this->assign('elementNames', $this->getRenderableElementNames());

    parent::buildQuickForm();
  }

  public function setDefaultValues() {

    parent::setDefaultValues();
    $values['id'] = $this->groupId;
    $values['migration_type'] = 'test';
    return $values;
  }

  public function postProcess() {
    $values = $this->exportValues();

    if (isset($values['id'])) {

      $migrationData = CRM_Memoriamigration_Connector_MigrationData::singleton();
      $group = $migrationData->getMigrationGroup($values['id']);

      $ret = $migrationData->setMigrationGroupData($values['id'], [
        'migration_type'  => $values['migration_type'],
        'migration_spgeo' => $values['migration_spgeo'],
        'migration_users' => $values['migration_users'],
        'status'          => 'queued',
      ]);

      if($ret) {
        drupal_set_message('De migratie van de groep <strong>' . $group->usergroup_name . '</strong> is ingepland.');
      }
    }

    parent::postProcess();
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/memoria'));
  }

  private function getRenderableElementNames() {
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  private function getSPOrganisationChoices() {
    $choices = [
      '' => ' - geen - ',
    ];

    // Dit wil weer eens niet. Tuurlijk niet.
    // 'contact_sub_type' => ['IN' => ['SP_Afdeling', 'SP_Werkgroep', 'SP_Regio', 'SP_Provincie']]

    foreach (['SP_Afdeling', 'SP_Werkgroep', 'SP_Regio', 'SP_Provincie'] as $sporg) {
      $res = civicrm_api3('Contact', 'get', [
        'contact_sub_type' => $sporg,
        'return'           => 'contact_id,display_name',
        'option.limit'     => '1000',
      ]);
      foreach ($res['values'] as $r) {
        $choices[$r['contact_id']] = $r['display_name'];
      }
    }

    return $choices;
  }
}
