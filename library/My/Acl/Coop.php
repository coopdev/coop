<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Creates ACLs for the coop application
 * 
 * @todo Must add priveleges to contract resources
 *
 * @author joseph
 */
class My_Acl_Coop extends Zend_Acl
{
   public function __construct() 
   {
      $this->add(new Zend_Acl_Resource('error'));
            
      $this->add(new Zend_Acl_Resource('index'));
      
      $this->add(new Zend_Acl_Resource('auth'));
      $this->add(new Zend_Acl_Resource('cas'), 'auth');
      $this->add(new Zend_Acl_Resource('logout'), 'auth');
      $this->add(new Zend_Acl_Resource('logoutPage'), 'auth');
      
      $this->add(new Zend_Acl_Resource('pages'));
      $this->add(new Zend_Acl_Resource('students'),'pages');
      $this->add(new Zend_Acl_Resource('teachers'), 'pages');
      $this->add(new Zend_Acl_Resource('home'), 'pages');
      
      $this->add(new Zend_Acl_Resource('person'));
      
      $this->add(new Zend_Acl_Resource('contract'));
      $this->add(new Zend_Acl_Resource('new'), 'contract');
      $this->add(new Zend_Acl_Resource('create'), 'contract');
      
      $this->addRole(new Zend_Acl_Role('none'));
      $this->addRole(new Zend_Acl_Role('guest'), 'none');
      
      $this->addRole(new Zend_Acl_Role('user'),'guest');
      $this->addRole(new Zend_Acl_Role('manager'), 'user');
      
      $this->addRole(new Zend_Acl_Role('contractNo'));
      
      
      /* PERMISSIONS */    
      $this->allow('none', 'auth');
      //$this->deny('none', 'auth', 'logout');
      
      $this->allow('guest','contract','new');
      $this->allow('guest','error');
      
      $this->allow('user','index');
      $this->allow('user', 'pages');
      $this->allow('user', 'contract');
      $this->allow('user', 'person');
      $this->deny('user','pages','teachers');
            
      $this->allow('manager', 'pages', 'teachers');
      
      /* Users who haven't filled out a contract can
       * only access certain things.
       */
      $this->allow('contractNo','contract');
      
      $this->allow('contractNo','auth','logout');
      $this->allow('contractNo','auth','cas');
      $this->allow('contractNo','error');
   }
}

?>
