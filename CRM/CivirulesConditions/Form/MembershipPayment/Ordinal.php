<?php
/**
 * Form controller class
 */
class CRM_CivirulesConditions_Form_MembershipPayment_Ordinal extends CRM_CivirulesConditions_Form_Form {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    foreach ($this->conditionClass->operators() as $key => $label) {
      $operatorList[$key] = ts($label);
    }

    $this->add('hidden', 'rule_condition_id');
    $this->add('select', 'operator', ts('Operator'), $operatorList, true);
    $this->add('text', 'ordinal_number', ts('Position of payment in series of payments for a membership'), array(), true);
    $this->addRule('ordinal_number','Position must be a whole number','numeric');
    $this->addRule('ordinal_number','Position must be a whole number','nopunctuation');

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
    $data = unserialize($this->ruleCondition->condition_params);
    if (!empty($data['operator'])) {
      $defaultValues['operator'] = $data['operator'];
    }
    if (!empty($data['ordinal_number'])) {
      $defaultValues['ordinal_number'] = $data['ordinal_number'];
    }
    return $defaultValues;
  }


  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    $data['operator'] = $this->_submitValues['operator'];
    $data['ordinal_number'] = $this->_submitValues['ordinal_number'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }

}
