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

         $role = $this->_role;
         $coopSess = new Zend_Session_Namespace('coop');

         $contStat = $coopSess->contractStatus;
         $prevCont = $coopSess->prevController;
         $prevAct = $coopSess->prevAction;
         $redirector = new Zend_Controller_Action_Helper_Redirector();

         // need this when executing wkhtmltopdf since it has a role of none and gets sent to 
         // the weblogin page.
         if($this->getRequest()->isGet()) {
            if (isset($_GET['role']) && $_GET['role'] === 'A592NXZ71680STWVR926' ) {
               return;
            }
         }
         
         if (!$this->_acl->isAllowed($role, $resource, $resource."_".$action)) {
            if ($role == 'none') {
               $redirector->direct('cas','auth');
               
            } else {
               $redirector->direct('access-denied', 'pages');
            }
            
         }
         
      }
   }
?>
