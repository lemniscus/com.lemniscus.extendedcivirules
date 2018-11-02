<?php
/**
 * Class CRM_CivirulesConditions_Contribution_PositionInRecurringSeries
 *
 * This CiviRules condition will check for the xth contribution resulting from 
 * the same recurring contribution
 *
 * @author Noah Miller (Lemniscus) <nm@lemnisc.us>
 * @license AGPL-3.0
 */
class CRM_CivirulesConditions_Contribution_PositionInRecurringSeries extends CRM_Civirules_Condition {

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
   * Method to determine if the condition is valid
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $ordinalNumber = $this->conditionParams['ordinal_number'];
    if (!is_integer($ordinal_number + 0)) {
      throw new Exception('Reference position must be an integer for CiviRules condition "Position of contribution in its recurring contribution series". Reference position is "' . $ordinal_number . '"', 1);
    }

    $operator = CRM_Utils_Array::value($this->conditionParams['operator'], $this->operators());
    if (is_null($operator)) {
      throw new Exception('A valid operator must be specified for CiviRules condition "Contribution is the xth payment for a membership"', 1);
    }

    $contribution = $triggerData->getEntityData('Contribution');
    /*
     * retrieve count of contributions (any status) for this donor and this 
     * recurring_contribution_id
     */
    $recurID = CRM_Utils_Array::value('contribution_recur_id', $contribution, FALSE);
    $recurCount = 0;
    if ($recurID) {
      $query = 'SELECT COUNT(*) AS recurringContributions FROM civicrm_contribution
                  WHERE civicrm_contribution.contribution_recur_id = %1
                    AND civicrm_contribution.receive_date <= %2';
      $params = array(
        1 => array($recurID, 'Positive'),
        2 => array($contribution['receive_date'], 'String'));
      $dao = CRM_Core_DAO::executeQuery($query, $params);
      if ($dao->fetch()) {
        $recurCount = (integer)$dao->recurringContributions;
      }
    }

    switch ($operator) {
      case 'equals':
        return ($recurCount == $ordinalNumber);
      case 'does not equal':
        return ($recurCount != $ordinalNumber);
      case 'is more than':
        return ($recurCount > $ordinalNumber);
      case 'is more than or equal to':
        return ($recurCount >= $ordinalNumber);
      case 'is less than':
        return ($recurCount < $ordinalNumber);
      case 'is less than or equal to':
        return ($recurCount <= $ordinalNumber);
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution/position_in_recurring_series/', 'rule_condition_id='.$ruleConditionId);
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
    return 'Contribution ' . $phrase . ' number ' . $this->conditionParams['ordinal_number'] . ' in its recurring contribution series';
  }

  /**
   * This function validates whether this condition works with the selected trigger.
   *
   * This function could be overriden in child classes to provide additional validation
   * whether a condition is possible in the current setup. E.g. we could have a condition
   * which works on contribution or on contributionRecur then this function could do
   * this kind of validation and return false/true
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    return $trigger->doesProvideEntity('Contribution');
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