<?php
   class My_Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract
   {
      private $_acl = null;
      private $_uhinfo = null;

      public function __construct(Zend_Acl $acl, array $uhinfo)
      {
         $this->_acl = $acl;   
         $this->_uhinfo = $uhinfo;
      }

      public function preDispatch(Zend_Controller_Request_Abstract $request)
      {
         $resource = $request->getControllerName();   
         $action = $request->getActionName();   

         $role = $this->_uhinfo['role'];

         if (!$this->_acl->isAllowed($role, $resource, $action)) {
            if ($role == 'none') {
               $request->setControllerName('auth')
                       ->setActionName('cas');
            
            } else {
               $coopSess = new Zend_Session_Namespace('coop');
               Zend_OpenId::redirect($coopSess->prevUri);
            }
         }
         
      }
      
      public function postDispatch(Zend_Controller_Request_Abstract $request) 
      {
         $prevUri = $request->getRequestUri();
         $coopSess = new Zend_Session_Namespace('coop');
         $coopSess->prevUri = $prevUri;
      }
   }
?>
