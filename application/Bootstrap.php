<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
   protected function _initAutoload()
   {  
      // Allows me to use contents of files within the library/My/
      // directory without having to include() the files.
      Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');  
      
      /* Initialize user's role to 'none' */
      Zend_Session::start();
      $coopSess = new Zend_Session_Namespace('coop');
      if (!isset($coopSess->role)) {
          $coopSess->role = 'none';
      }
           
      $acl = new My_Acl_Coop();
      $auth = Zend_Auth::getInstance();
      $FrontController = Zend_Controller_Front::getInstance();
      
      $FrontController->registerPlugin(new My_Plugin_PreviousUrl());
//      $FrontController->registerPlugin(new My_Plugin_AccessCheck($acl, 
//                                                 $coopSess->role));
      
      // Set the base URL for the application in a session.
      $baseUrl = $FrontController->setBaseUrl("/acl/public")->getBaseUrl();
      $coopSess->baseUrl = $baseUrl;
                    
   }
      
   protected function _initRoutes()
   {
      /*
       * Routes are covered on page 64 of Zend book.
       */
      $FrontController = Zend_Controller_Front::getInstance();
      $router = $FrontController->getRouter();
      
   }
}

