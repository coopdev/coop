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
      $this->add(new Zend_Acl_Resource('pages_disclaimer'), 'pages');
      $this->add(new Zend_Acl_Resource('pages_access-denied'), 'pages');

      /* Form Controller */
      $this->add(new Zend_Acl_Resource('form'));
      $this->add(new Zend_Acl_Resource('form_student-info-show'), 'form');
      $this->add(new Zend_Acl_Resource('form_student-info-submit'), 'form');
      $this->add(new Zend_Acl_Resource('form_coop-agreement-show'), 'form');
      $this->add(new Zend_Acl_Resource('form_coop-agreement-pdf'), 'form');
      
      /* User Controller */
      $this->add(new Zend_Acl_Resource('user'));
      $this->add(new Zend_Acl_Resource('user_new'), 'user');
      $this->add(new Zend_Acl_Resource('user_create'), 'user');
      $this->add(new Zend_Acl_Resource('user_searchstudent'), 'user');
      $this->add(new Zend_Acl_Resource('user_list-coords'), 'user');
      $this->add(new Zend_Acl_Resource('user_edit-coord'), 'user');
      $this->add(new Zend_Acl_Resource('user_add-coord'), 'user');



      /* Class Controller */
      $this->add(new Zend_Acl_Resource('class'));
      $this->add(new Zend_Acl_Resource('class_change'), 'class');
      $this->add(new Zend_Acl_Resource('class_listall'), 'class');
      $this->add(new Zend_Acl_Resource('class_edit'), 'class');
      $this->add(new Zend_Acl_Resource('class_delete'), 'class');
      $this->add(new Zend_Acl_Resource('class_create'), 'class');
      
      
      /* Syllabus Controller */
      $this->add(new Zend_Acl_Resource('syllabus'));
      $this->add(new Zend_Acl_Resource('syllabus_listall'), 'syllabus');
      $this->add(new Zend_Acl_Resource('syllabus_view'), 'syllabus');
      $this->add(new Zend_Acl_Resource('syllabus_edit'), 'syllabus');
      $this->add(new Zend_Acl_Resource('syllabus_create'), 'syllabus');

      /* Assignment Controller */
      $this->add(new Zend_Acl_Resource('assignment'));
      $this->add(new Zend_Acl_Resource('asignment_submit'), 'assignment');


      /* Async Controller */
      $this->add(new Zend_Acl_Resource('async'));
      $this->add(new Zend_Acl_Resource('async_student-rec-search-result'), 'async');
      $this->add(new Zend_Acl_Resource('async_submission-recs'), 'async');
      $this->add(new Zend_Acl_Resource('async_view-stu-info-sheet'), 'async');
      $this->add(new Zend_Acl_Resource('async_class-roll-json'), 'async');

      /* Roles */

      $this->addRole(new Zend_Acl_Role('none'));
      //$this->addRole(new Zend_Acl_Role('guest'), 'none');
      
      $this->addRole(new Zend_Acl_Role('user'),'none');
      $this->addRole(new Zend_Acl_Role('coordinator'), 'user');
      $this->addRole(new Zend_Acl_Role('admin'), 'coordinator');
      $this->addRole(new Zend_Acl_Role('super-admin'), 'admin');
      $this->addRole(new Zend_Acl_Role('supervisor'), 'none');
      
      //$this->addRole(new Zend_Acl_Role('contractNo'));
      $this->addRole(new Zend_Acl_Role('notActive'));
      
      $this->addRole(new Zend_Acl_Role('notEnrolled'), 'none');
      
      /* PERMISSIONS */    
      

      $this->allow('none', 'index');
      $this->allow('none', 'pages', 'pages_login');
      $this->allow('none', 'pages', 'pages_access-denied');
      //$this->allow('none', 'auth', 'auth_cas');
      $this->allow('none', 'auth');
      //$this->deny('none', 'auth', 'logout');

      //$this->allow('guest','auth');
      //$this->allow('guest','pages');
      //$this->allow('guest','user','user_new');
      //$this->allow('guest','user','user_create');
      //$this->allow('guest','error');
      
      $this->allow('user','error');
      $this->allow('user','auth');
      $this->allow('user','pages');
      $this->allow('user','index');
      $this->allow('user', 'syllabus','syllabus_view');
      $this->allow('user', 'form');
      $this->allow('user', 'class', 'class_change');
      // Think about if a user needs to get to "user" actions if they are already a user 
      // (i.e. in the database as a student).
      //$this->allow('user', 'user', 'user_new');
      //$this->allow('user', 'user', 'user_create');
            
      $this->allow('coordinator', 'syllabus', 'syllabus_listall');
      $this->allow('coordinator', 'syllabus', 'syllabus_view');
      $this->allow('coordinator','user','user_new');
      $this->allow('coordinator','user','user_create');
      $this->allow('coordinator', 'user', 'user_searchstudent');
      $this->allow('coordinator', 'user', 'user_history-show');
      $this->allow('coordinator', 'user', 'user_list-coords');
      $this->allow('coordinator', 'user', 'user_delete-coord');
      $this->allow('coordinator', 'user', 'user_edit-coord');
      $this->allow('coordinator', 'user', 'user_add-coord');
      $this->allow('coordinator', 'class');
      $this->allow('coordinator', 'async');
      $this->allow('coordinator', 'assignment', 'assignment_submit');
      
      /* 
       * Users who haven't filled out a contract can
       * only access certain things.
       */
      $this->allow('notActive','pages','pages_login');
      $this->allow('notActive','pages', 'pages_disclaimer');
      $this->allow('notActive','auth','auth_logout');
      $this->allow('notActive','auth','auth_cas');
      $this->allow('notActive','error');
      //// delete bottom rule
      //$this->allow('notActive');

   }
}

?>
