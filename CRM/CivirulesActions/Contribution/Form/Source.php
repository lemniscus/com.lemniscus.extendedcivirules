<?php
/**
 * Form controller class
 */
class CRM_CivirulesActions_Contribution_Form_Source extends CRM_CivirulesActions_Form_Form {

  /**
   * Overridden parent method to build the form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');

    $this->add('text', 'source', ts('Source'));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $defaultValues['rule_action_id'] = $this->ruleActionId;
    if (!empty($this->ruleAction->action_params)) {
      $data = unserialize($this->ruleAction->action_params);
    }
    if (!empty($data['source'])) {
      $defaultValues['source'] = $data['source'];
    }
    return $defaultValues;
  }


  /**
   * Overridden parent method to process form data after submitting
   *
   * @access public
   */
  public function postProcess() {
    $data['source'] = $this->_submitValues['source'];
    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }
}
