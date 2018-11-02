<?php
/**
 * This class manages the installation, upgrading, etc of the Extended CiviRules extension.
 */
class CRM_Extendedcivirules_Upgrader extends CRM_Extendedcivirules_Upgrader_Base {

  public function install() {
    civicrm_api3('CiviRuleTrigger', 'create', [
      'name' => 'new_membership_payment_extended',
      'label' => 'Membership Payment is added (extended version)',
      'class_name' => 'CRM_CivirulesPostTrigger_MembershipPayment',
      'is_active' => 1,
    ]);
    civicrm_api3('CiviRuleCondition', 'Create', [
      'name' => 'contribution_is_membership_payment',
      'label' => 'Is Contribution a membership payment?',
      'class_name' => 'CRM_CivirulesConditions_Contribution_IsMembershipPayment',
      'is_active' => 1
    ]);
    civicrm_api3('CiviRuleCondition', 'Create', [
      'name' => 'contribution_is_recurring',
      'label' => 'Is Contribution recurring?',
      'class_name' => 'CRM_CivirulesConditions_Contribution_IsRecurring',
      'is_active' => 1
    ]);
    civicrm_api3('CiviRuleCondition', 'Create', [
      'name' => 'contribution_position_in_recurring_series',
      'label' => 'Position of contribution in its recurring contribution series',
      'class_name' => 'CRM_CivirulesConditions_Contribution_PositionInRecurringSeries',
      'is_active' => 1
    ]);
    civicrm_api3('CiviRuleCondition', 'Create', [
      'name' => 'membership_payment_ordinal',
      'label' => 'Payment is the Xth one for its membership',
      'class_name' => 'CRM_CivirulesConditions_MembershipPayment_Ordinal',
      'is_active' => 1
    ]);
    civicrm_api3('CiviRuleAction', 'Create', [
      'name' => 'contribution_financial_type',
      'label' => 'Set the Financial Type for a Contribution',
      'class_name' => 'CRM_CivirulesActions_Contribution_FinancialType',
      'is_active' => 1
    ]);
    civicrm_api3('CiviRuleAction', 'Create', [
      'name' => 'contribution_source',
      'label' => 'Set the Source of a Contribution',
      'class_name' => 'CRM_CivirulesActions_Contribution_Source',
      'is_active' => 1
    ]);
  }

  public function uninstall() {
    civicrm_api3('CiviRuleTrigger', 'get', [
      'class_name' => 'CRM_CivirulesPostTrigger_MembershipPayment',
      'api.CiviRuleTrigger.delete' => [],
    ]);
    civicrm_api3('CiviRuleCondition', 'get', [
      'class_name' => 'CRM_CivirulesConditions_Contribution_IsMembershipPayment',
      'api.CiviRuleCondition.delete' => [],
    ]);
    civicrm_api3('CiviRuleCondition', 'get', [
      'class_name' => 'CRM_CivirulesConditions_Contribution_IsRecurring',
      'api.CiviRuleCondition.delete' => [],
    ]);
    civicrm_api3('CiviRuleCondition', 'get', [
      'class_name' => 'CRM_CivirulesConditions_Contribution_PositionInRecurringSeries',
      'api.CiviRuleCondition.delete' => [],
    ]);
    civicrm_api3('CiviRuleCondition', 'get', [
      'class_name' => 'CRM_CivirulesConditions_MembershipPayment_Ordinal',
      'api.CiviRuleCondition.delete' => [],
    ]);
    civicrm_api3('CiviRuleAction', 'get', [
      'class_name' => 'CRM_CivirulesActions_Contribution_FinancialType',
      'api.CiviRuleAction.delete' => [],
    ]);
    civicrm_api3('CiviRuleAction', 'get', [
      'class_name' => 'CRM_CivirulesActions_Contribution_Source',
      'api.CiviRuleAction.delete' => [],
    ]);
  }
}
