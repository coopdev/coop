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
      
      /* Auth Controller */
      $this->add(new Zend_Acl_Resource('auth'));
      $this->add(new Zend_Acl_Resource('auth_cas'), 'auth');
      $this->add(new Zend_Acl_Resource('auth_postCas'), 'auth');
      $this->add(new Zend_Acl_Resource('auth_logout'), 'auth');
      
      /* Pages Controller */
      $this->add(new Zend_Acl_Resource('pages'));
      $this->add(new Zend_Acl_Resource('pages_login'),'pages');
      $this->add(new Zend_Acl_Resource('pages_students'),'pages');
      $this->add(new Zend_Acl_Resource('pages_teachers'), 'pages');
      $this->add(new Zend_Acl_Resource('pages_home'), 'pages');
      
      /* User Controller */
      $this->add(new Zend_Acl_Resource('user'));
      $this->add(new Zend_Acl_Resource('user_new'), 'user');
      $this->add(new Zend_Acl_Resource('user_create'), 'user');
      
      /* Contract Controller */
      $this->add(new Zend_Acl_Resource('contract'));
      $this->add(new Zend_Acl_Resource('contract_new'), 'contract');
      $this->add(new Zend_Acl_Resource('contract_renew'), 'contract');
      $this->add(new Zend_Acl_Resource('contract_create'), 'contract');
      
      /* Syllabus Controller */
      $this->add(new Zend_Acl_Resource('syllabus'));
      $this->add(new Zend_Acl_Resource('syllabus_listall'), 'syllabus');
      $this->add(new Zend_Acl_Resource('syllabus_view'), 'syllabus');
      $this->add(new Zend_Acl_Resource('syllabus_edit'), 'syllabus');
      $this->add(new Zend_Acl_Resource('syllabus_create'), 'syllabus');

      /* Roles */

      $this->addRole(new Zend_Acl_Role('none'));
      $this->addRole(new Zend_Acl_Role('guest'), 'none');
      
      $this->addRole(new Zend_Acl_Role('user'),'guest');
      $this->addRole(new Zend_Acl_Role('coordinator'), 'user');
      $this->addRole(new Zend_Acl_Role('admin'), 'coordinator');
      $this->addRole(new Zend_Acl_Role('super-admin'), 'admin');
      
      $this->addRole(new Zend_Acl_Role('contractNo'));
      
      
      /* PERMISSIONS */    
      
      $this->allow('none', 'pages', 'pages_login');
      $this->allow('none', 'auth', 'auth_cas');
      //$this->deny('none', 'auth', 'logout');
      
      $this->allow('guest','auth');
      $this->allow('guest','pages');
      $this->allow('guest','user','user_new');
      $this->allow('guest','user','user_create');
      $this->allow('guest','error');
      
      $this->allow('user','index');
      $this->allow('user', 'contract');
      $this->allow('user', 'syllabus','syllabus_view');

      // Think about if a user needs to get to "user" actions if they are already a user 
      // (i.e. in the database as a student).
      $this->allow('user', 'user');
            
      $this->allow('coordinator', 'syllabus', 'syllabus_listall');
      $this->allow('coordinator', 'syllabus', 'syllabus_view');
      
      /* 
       * Users who haven't filled out a contract can
       * only access certain things.
       */
      $this->allow('contractNo','pages','pages_login');
      $this->allow('contractNo','contract');
      $this->allow('contractNo','auth','auth_logout');
      $this->allow('contractNo','auth','auth_cas');
      $this->allow('contractNo','error');
      // delete bottom rule
      $this->allow('contractNo');

   }
}

?>
