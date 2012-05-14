<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
   protected function _initAutoload()
   {  
      // alskfalsjfsljf
      Zend_Session::start();
      // Allows me to use contents of files within the library/My/
      // directory without having to include() the files.
      Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');  
      
      // Initialize user's role to 'none' 
      //Zend_Session::start();
      $coopSess = new Zend_Session_Namespace('coop');
      if (!isset($coopSess->role)) {
          $coopSess->role = 'none';
      }
           
      $acl = new My_Acl_Coop();
      $auth = Zend_Auth::getInstance();
      $FrontController = Zend_Controller_Front::getInstance();
      
      // Register plugin to get previous URLs
      $FrontController->registerPlugin(new My_Plugin_PreviousUrl());
      
      // Register ACL plugin
      //$FrontController->registerPlugin(new My_Plugin_AccessCheck($acl, $coopSess->role));
      
      // Set the base URL for the application in a session.
      $baseUrl = $FrontController->setBaseUrl("")->getBaseUrl();
      //$baseUrl = $FrontController->getBaseUrl();
      $coopSess->baseUrl = $baseUrl;


      // Change some of the default validation messages
      $messages = array(

                      Zend_Validate_Digits::NOT_DIGITS => 'Must contain only digits',
                      Zend_Validate_Float::NOT_FLOAT => 'Must use decimal format'
                  
                  );
      $translator = new Zend_Translate('array', $messages);
       
      Zend_Validate_Abstract::setDefaultTranslator($translator);
      // end changing messages
   }
      
   protected function _initRoutes()
   {
      /*
       * Routes are covered on page 64 of Zend book.
       */
      $front = Zend_Controller_Front::getInstance();
      $router = $front->getRouter();
      //$router->addRoute('root', 
      //        new Zend_Controller_Router_Route('../../coop', 
      //                array('controller'=>'pages','action'=>'home')));
      $router->addRoute('login', 
              new Zend_Controller_Router_Route('login', 
                      array('controller'=>'auth','action'=>'cas')));
      
   }
}

