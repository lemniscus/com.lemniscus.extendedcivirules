<?php
/**
 * Class CRM_CivirulesConditions_MembershipPayment_Ordinal
 *
 * This CiviRules condition will check for the xth contribution linked to 
 * the same membership
 *
 * @author Noah Miller (Lemniscus) <nm@lemnisc.us>
 * @license AGPL-3.0
 */
class CRM_CivirulesConditions_MembershipPayment_Ordinal extends CRM_Civirules_Condition {

  private $conditionParams = array();

  /**
   * Returns an array of operators that can be used in this Condition
   *
   * @access public
   */
  public function operators() {
    return array(
      1 => 'equals',
      2 => 'does not equal',
      3 => 'is more than',
      4 => 'is more than or equal to',
      5 => 'is less than',
      6 => 'is less than or equal to'
    );
  }

  /**
   * Checks if the condition is met
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $ordinalNumber = $this->conditionParams['ordinal_number'];
    if (!is_integer($ordinal_number + 0)) {
      throw new Exception('X must be an integer for CiviRules condition "Payment is the Xth one for its membership"', 1);
    }

    $operator = CRM_Utils_Array::value($this->conditionParams['operator'], $this->operators());
    if (is_null($operator)) {
      throw new Exception('A valid operator must be specified for CiviRules condition "Payment is the Xth one for its membership"', 1);
    }

    $membershipPayment = $triggerData->getEntityData('MembershipPayment');
    $query = 'SELECT COUNT(*) AS paymentCount
              FROM civicrm_membership_payment mp1
              JOIN civicrm_contribution c1 ON (mp1.contribution_id = c1.id)
              JOIN civicrm_membership_payment mp2 ON (mp1.membership_id = mp2.membership_id)
              JOIN civicrm_contribution c2 ON (mp2.contribution_id = c2.id)
              WHERE mp1.id = %1
                AND c2.receive_date <= c1.receive_date';
    $params = array(1 => array($membershipPayment['id'], 'Positive'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    $dao->fetch();
    $paymentCount = $dao->paymentCount;

    switch ($operator) {
      case 'equals':
        return ($paymentCount == $ordinalNumber);
      case 'does not equal':
        return ($paymentCount != $ordinalNumber);
      case 'is more than':
        return ($paymentCount > $ordinalNumber);
      case 'is more than or equal to':
        return ($paymentCount >= $ordinalNumber);
      case 'is less than':
        return ($paymentCount < $ordinalNumber);
      case 'is less than or equal to':
        return ($paymentCount <= $ordinalNumber);
      default:
        throw new Exception('Invalid Operator', 1);
      break;
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/membership_payment/ordinal', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns user-friendly text explaining the condition params
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $operators = $this->operators();
    switch ($operators[$this->conditionParams['operator']]) {
      case 'equals':
        $phrase = 'is';
        break;
      case 'does not equal':
        $phrase = ' not';
        break;
      default:
        $phrase = $operators[$this->conditionParams['operator']];
    }
    return 'Payment ' . $phrase . ' number ' . $this->conditionParams['ordinal_number'] . ' of the payments for a membership';
  }

  /**
   * This function validates whether this condition works with the selected trigger.
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    return $trigger->doesProvideEntity('MembershipPayment');
  }

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
}