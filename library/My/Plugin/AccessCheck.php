<?php
   class My_Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract
   {
      private $_acl = null;
      private $_uhinfo = null;

      public function __construct(Zend_Acl $acl, array $uhinfo)
      {
         $this->_acl = $acl;   
         $this->uhinfo = $uhinfo
      }

      public function preDispatch(Zend_Controller_Request_Abstract $request)
      {
         $resource = $request->getControllerName();   
         $action = $request->getActionName();   

         $role = $this->uhinfo['role'];

         if (!$this->_acl->isAllowed($role, $resource, $action)) {
            $request->setControllerName('auth');
                    ->setActionName('cas');
         }
      }
   }
?>
