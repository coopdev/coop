<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
   protected function _initAutoload()
   {
      Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');   
   }

   protected function _initSession()
   {
      Zend_Session::start();
      $coopSess = new Zend_Session_Namespace('coop');
   }
}

