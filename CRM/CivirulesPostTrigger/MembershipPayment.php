<?php
/**
 * Class CRM_CivirulesPostTrigger_MembershipPayment
 *
 * This CiviRules trigger reacts to the creation/update of a Membership Payment entity
 *
 * @author Noah Miller (Lemniscus) <nm@lemnisc.us>
 * @license AGPL-3.0
 */
class CRM_CivirulesPostTrigger_MembershipPayment extends CRM_Civirules_Trigger_Post {

  /**
   * Returns a definiton of which entity the trigger reacts to
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    return new CRM_Civirules_TriggerData_EntityDefinition($this->objectName, $this->objectName, $this->getDaoClassName(), 'MembershipPayment');
  }

  /**
   * Return the name of the DAO Class. If a dao class does not exist return an empty value
   *
   * @return string
   */
  protected function getDaoClassName() {
    return 'CRM_Member_DAO_MembershipPayment';
  }

  /**
   * Override getTriggerDataFromPost() so that we can append the Contribution
   * entity to the trigger data.
   */
  protected function getTriggerDataFromPost($op, $objectName, $objectId, $objectRef) {
    $triggerData = parent::getTriggerDataFromPost($op, $objectName, $objectId, $objectRef);
    $membershipPayment = $triggerData->getEntityData('MembershipPayment');
    $params = array('id' => $membershipPayment['contribution_id']);
    $contribution = civicrm_api3('Contribution', 'getsingle', $params);
    $triggerData->setEntityData('Contribution', $contribution);
    return $triggerData;
  }

  /**
   * Returns an array of additional entities provided in this trigger
   *
   * @return array of CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function getAdditionalEntities() {
    $entities = parent::getAdditionalEntities();
    $entities[] = new CRM_Civirules_TriggerData_EntityDefinition('Contribution', 'Contribution', 'CRM_Contribution_DAO_Contribution', 'Contribution');
    return $entities;
  }


}