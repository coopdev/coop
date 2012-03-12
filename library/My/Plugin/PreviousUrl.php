<?php



/**
 * Saves the Url a user visits.
 *
 * @author joseph
 */
class My_Plugin_PreviousUrl extends Zend_Controller_Plugin_Abstract
{
   public function postDispatch(Zend_Controller_Request_Abstract $request)
   {
      $prevUrl = $request->getRequestUri();
      $prevController = $request->getControllerName();
      $prevAction = $request->getActionName();
      $coopSess = new Zend_Session_Namespace('coop');
      $coopSess->prevUrl = $prevUrl;
      $coopSess->prevController = $prevController;
      $coopSess->prevAction = $prevAction;
      //die($coopSess->prevUrl);
      
      //$coopSess->prevController = $request->getControllerName();
      //$coopSess->prevAction = $request->getActionName();
      
   }
}

?>
