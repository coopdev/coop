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
      //die("$prevAction, $prevController");
      $coopSess = new Zend_Session_Namespace('coop');
      $coopSess->prevUrl = $prevUrl;
      $coopSess->prevController = $prevController;
      $coopSess->prevAction = $prevAction;
      //die($coopSess->prevUrl);
      
      //$coopSess->prevController = $request->getControllerName();
      //$coopSess->prevAction = $request->getActionName();
      
   }

   public function preDispatch(Zend_Controller_Request_Abstract $request) 
   {
      parent::preDispatch($request);

      $cont = $request->getControllerName();
      $act = $request->getActionName();
      //die("$cont,$act");
   }
}

?>
