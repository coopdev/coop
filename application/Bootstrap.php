<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
   protected function _initAutoload()
   {
      Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');  
      
      /* Session for uhinfo passed back from CAS server */
      Zend_Session::start();
      $coopSess = new Zend_Session_Namespace('coop');
      if (!isset($coopSess->uhinfo)) {
          $coopSess->uhinfo['role'] = 'none';
      }
     
      
      $acl = new My_Acl_Coop();
      $auth = Zend_Auth::getInstance();
      $FrontController = Zend_Controller_Front::getInstance();
      $FrontController->registerPlugin(new My_Plugin_AccessCheck($acl, 
                                                 $coopSess->uhinfo));
   }

   protected function _initSession()
   {
      //Zend_Session::start();
      //$coopSess = new Zend_Session_Namespace('coop');
      //$coopSess->uhinfo = array();
      //$coopSess->uhinfo['role'] = 'none';
   }
}

