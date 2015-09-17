<?php

require_once 'CRM/Core/Form.php';

/**
 * Instellingen voor Memoria-migratie
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Memoriamigration_Form_Settings extends CRM_Core_Form {

  private $myFields = [
    ['type' => 'text', 'name' => 'memoriamigr_dbhost', 'label' => 'Hostnaam', 'attributes' => [], 'required' => TRUE],
    ['type' => 'text', 'name' => 'memoriamigr_dbuser', 'label' => 'Gebruikersnaam', 'attributes' => [], 'required' => TRUE],
    ['type' => 'text', 'name' => 'memoriamigr_dbpass', 'label' => 'Wachtwoord', 'attributes' => [], 'required' => TRUE],
    ['type' => 'text', 'name' => 'memoriamigr_dbname', 'label' => 'Database', 'attributes' => [], 'required' => TRUE],
  ];

  public function buildQuickForm() {

    foreach ($this->myFields as $field) {
      $this->add($field['type'], $field['name'], $field['label'], $field['attributes'], $field['required']);
    }

    $this->addButtons([['type' => 'submit', 'name' => 'Opslaan', 'isDefault' => TRUE]]);

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setDefaultValues() {

    parent::setDefaultValues();
    foreach ($this->myFields as $field) {
      $values[$field['name']] = CRM_Memoriamigration_Config::get($field['name']);
    }
    return $values;
  }

  public function postProcess() {
    $values = $this->exportValues();
    foreach ($this->myFields as $field) {
      CRM_Memoriamigration_Config::set($field['name'], $values[$field['name']]);
    }

    CRM_Core_Session::setStatus('Migratie-instellingen opgeslagen', 'Migratie-instellingen', 'success');

    try {
      $dbconn = CRM_Memoriamigration_Connector_Database::singleton();
      $dbconn->connect($values['memoriamigr_dbhost'], $values['memoriamigr_dbuser'], $values['memoriamigr_dbpass'], $values['memoriamigr_dbname']);
    } catch (\Exception $e) {
      CRM_Core_Session::setStatus('Kon niet verbinden met de database. Weet je zeker dat je de goede instellingen hebt ingevuld? (' . $e->getMessage() . ')', 'Migratie-instellingen', 'warning');
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
}
