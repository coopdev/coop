<?php
   class My_Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract
   {
      private $_acl = null;
      private $_role = null;
      public $test = null;

      public function __construct(Zend_Acl $acl, $role)
      {
         $this->_acl = $acl;   
         $this->_role = $role;
      }

      public function preDispatch(Zend_Controller_Request_Abstract $request)
      {
         $resource = $request->getControllerName();   
         $action = $request->getActionName();   
         //die("$resource,$action");

         $role = $this->_role;
         $coopSess = new Zend_Session_Namespace('coop');
         //die($resource.' : '.$action);

         $contStat = $coopSess->contractStatus;
         $prevCont = $coopSess->prevController;
         $prevAct = $coopSess->prevAction;
         //die("$prevCont: $prevAct");
         $redirector = new Zend_Controller_Action_Helper_Redirector();
         
         if (!$this->_acl->isAllowed($role, $resource, $resource."_".$action)) {
            if ($role == 'none') {
               //$request->setControllerName('auth')
               //        ->setActionName('cas');
               $redirector->direct('cas','auth');
               
            } else {
               $redirector->direct('access-denied', 'pages');
               //$redirector->direct($prevAct, $prevCont);
               //$request->setControllerName('error');
               //$request->setActionName('error');
            }
            
            //die("hello");
         }
         
      }
      
      public function postDispatch(Zend_Controller_Request_Abstract $request) 
      {
         //$prevUrl = $request->getRequestUrl();
         //$coopSess = new Zend_Session_Namespace('coop');
         //$coopSess->prevUrl = $prevUrl;
         //die($coopSess->prevUrl);
      }
   }
?>
