<?php
/**
 * Class CRM_CivirulesConditions_Contribution_IsMembershipPayment
 *
 * This CiviRules condition will check whether a contribution is linked to a membership
 *
 * @author Noah Miller (Lemniscus) <nm@lemnisc.us>
 * @license AGPL-3.0
 */
class CRM_CivirulesConditions_Contribution_IsMembershipPayment extends CRM_Civirules_Condition {

  private $conditionParams = array();

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->conditionParams = array();
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * Checks if the condition is met
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contribution = $triggerData->getEntityData('Contribution');
    $apiParams = array('contribution_id' => (integer)$contribution['id']);
    $paymentsResult = civicrm_api3('MembershipPayment', 'Get', $apiParams);
    if ($this->conditionParams['test'] == 'is a membership payment') {
      return CRM_Utils_Array::value('count', $paymentsResult);
    } elseif ($this->conditionParams['test'] == 'is not a membership payment') {
      return !CRM_Utils_Array::value('count', $paymentsResult);
    } else {
      throw new Exception("Invalid operator in 'Is Membership Payment' Condition", 1);
    }
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution/is_membership_payment', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    return ts('Contribution ' . $this->conditionParams['test']);
  }

  /**
   * This function validates whether this condition works with the selected trigger.
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    return $trigger->doesProvideEntity('Contribution');
  }

}