<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Creates ACLs for the coop application
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
      
      $this->add(new Zend_Acl_Resource('pages'));
      $this->add(new Zend_Acl_Resource('students'),'pages');
      $this->add(new Zend_Acl_Resource('teachers'), 'pages');
      $this->add(new Zend_Acl_Resource('home'), 'pages');
      
      $this->addRole(new Zend_Acl_Role('none'));
      $this->addRole(new Zend_Acl_Role('student'));
      $this->addRole(new Zend_Acl_Role('teacher'), 'student');
      
      
      $this->allow('none', 'auth');
      $this->deny('none', 'auth', 'logout');
      
      $this->allow('student','error');
      $this->allow('student','auth');
      $this->allow('student','index');
      $this->allow('student', 'pages', 'home');
      $this->deny('student', 'pages', 'students');
      $this->allow('student','pages','students');
      
      $this->allow('teacher', 'pages', 'teachers');
   }
}

?>
