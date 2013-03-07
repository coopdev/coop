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
      $this->add(new Zend_Acl_Resource('form_student-info-edit'), 'form');
      $this->add(new Zend_Acl_Resource('form_student-info-submit'), 'form');
      $this->add(new Zend_Acl_Resource('form_coop-agreement'), 'form');
      $this->add(new Zend_Acl_Resource('form_coop-agreement-pdf'), 'form');
      
      /* User Controller */
      $this->add(new Zend_Acl_Resource('user'));
      $this->add(new Zend_Acl_Resource('user_new'), 'user');
      $this->add(new Zend_Acl_Resource('user_create'), 'user');
      $this->add(new Zend_Acl_Resource('user_searchstudent'), 'user');
      $this->add(new Zend_Acl_Resource('user_list-coords'), 'user');
      $this->add(new Zend_Acl_Resource('user_edit-coord'), 'user');
      $this->add(new Zend_Acl_Resource('user_add-coord'), 'user');
      $this->add(new Zend_Acl_Resource('user_list-studentaids'), 'user');



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
      $this->add(new Zend_Acl_Resource('asignment_list-all'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_list-all-for-student'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_edit-duedate'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_properties'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_edit-questions'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_add-question'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_delete-question'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_list-status-by-class'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_midterm-report'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_resume'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_cover-letter'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_list-submitted'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_learning-outcome'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_set-stu-eval-option-amount'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_manage-rated-questions'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_add-rated-question'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_edit-rated-question'), 'assignment');
      $this->add(new Zend_Acl_Resource('asignment_delete-rated-question'), 'assignment');


      /* Async Controller */
      $this->add(new Zend_Acl_Resource('async'));
      $this->add(new Zend_Acl_Resource('async_student-rec-search-result'), 'async');
      $this->add(new Zend_Acl_Resource('async_submission-recs'), 'async');
      $this->add(new Zend_Acl_Resource('async_view-stu-info-sheet'), 'async');
      $this->add(new Zend_Acl_Resource('async_class-roll-json'), 'async');
      $this->add(new Zend_Acl_Resource('async_fetch-students-as-json'), 'async');

      /* Backup Controller */
      $this->add(new Zend_Acl_Resource('backup'));
      $this->add(new Zend_Acl_Resource('backup_index'), 'backup');


      /* Semester Controller */
      $this->add(new Zend_Acl_Resource('semester'));
      
      
      /* PDF Controller */
      $this->add(new Zend_Acl_Resource('pdf'));
      $this->add(new Zend_Acl_Resource('pdf_timesheet'), 'pdf');
      $this->add(new Zend_Acl_Resource('pdf_generate-pdf'), 'pdf');
      
      /* HelpPage Controller */
      $this->add(new Zend_Acl_Resource('help-page'));
      
      /* Comments Controller */
      $this->add(new Zend_Acl_Resource('comments'));
      $this->add(new Zend_Acl_Resource('new'), 'comments');
      $this->add(new Zend_Acl_Resource('json-list'), 'comments');

      /* Roles */

      $this->addRole(new Zend_Acl_Role('none'));
      //$this->addRole(new Zend_Acl_Role('guest'), 'none');
      
      $this->addRole(new Zend_Acl_Role('user'),'none');
      $this->addRole(new Zend_Acl_Role('coordinator'), 'user');
      $this->addRole(new Zend_Acl_Role('studentAid'),'coordinator');

      // for students who may be in the database but are not enrolled in the current semester.
      $this->addRole(new Zend_Acl_Role('notEnrolled'), 'none');       
      // for users who have not agreed to disclaimer yet.
      $this->addRole(new Zend_Acl_Role('notActive')); 
      
      
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
      $this->deny('user', 'form', 'form_coop-agreement');
      $this->deny('user', 'form', 'form_edit-disclaimer');
      $this->allow('user', 'class', 'class_change');
      $this->allow('user', 'assignment', 'assignment_list-all-for-student');
      $this->allow('user', 'assignment', 'assignment_list-submitted');
      $this->allow('user', 'assignment', 'assignment_midterm-report');
      $this->allow('user', 'assignment', 'assignment_resume');
      $this->allow('user', 'assignment', 'assignment_learning-outcome');
      $this->allow('user', 'assignment', 'assignment_learning-outcome-edit');
      $this->allow('user', 'assignment', 'assignment_student-eval');
      $this->allow('user', 'assignment', 'assignment_cover-letter');
      $this->allow('user', 'assignment', 'assignment_supervisor-eval');
      $this->allow('user', 'assignment', 'assignment_timesheet');
      $this->allow('user', 'pdf');
      //$this->allow('user', 'user', 'user_new');
      //$this->allow('user', 'user', 'user_create');
            
      $this->allow('coordinator', 'syllabus', 'syllabus_listall');
      $this->allow('coordinator', 'syllabus', 'syllabus_view');
      $this->allow('coordinator', 'syllabus', 'syllabus_edit');
      $this->allow('coordinator','user');
      $this->allow('coordinator', 'class');
      $this->deny('coordinator', 'class', 'class_change');
      $this->allow('coordinator', 'async');
      $this->allow('coordinator', 'assignment');
      $this->deny('coordinator', 'assignment', 'assignment_midterm-report');
      $this->deny('coordinator', 'assignment', 'assignment_list-submitted');
      $this->deny('coordinator', 'assignment', 'assignment_list-all-for-student');
      $this->deny('coordinator', 'form');
      $this->allow('coordinator', 'form', 'form_coop-agreement');
      $this->allow('coordinator', 'form', 'form_edit-disclaimer');
      $this->allow('coordinator', 'backup');
      $this->allow('coordinator', 'semester');
      $this->allow('coordinator', 'pdf');
      $this->allow('coordinator', 'help-page');
      $this->allow('coordinator', 'comments');
      
      $this->deny('studentAid', 'syllabus');
      $this->deny('studentAid', 'user');
      $this->allow('studentAid', 'user', 'user_new');
      $this->allow('studentAid', 'user', 'user_view-logins');
      $this->allow('studentAid', 'user', 'user_searchstudent');
      $this->allow('studentAid', 'user', 'user_view-extended-duedates');
      $this->allow('studentAid', 'user', 'user_delete-extended-duedate');
      $this->deny('studentAid', 'class');
      $this->deny('studentAid', 'assignment');
      $this->allow('studentAid', 'assignment', 'assignment_submit');
      $this->allow('studentAid', 'assignment', 'assignment_supervisor-eval');
      $this->allow('studentAid', 'assignment', 'assignment_timesheet');
      $this->allow('studentAid', 'assignment', 'assignment_list-status-by-class');
      $this->deny('studentAid', 'semester');
      $this->deny('studentAid', 'form', 'form_edit-disclaimer');
      $this->deny('studentAid', 'backup');
      $this->deny('studentAid', 'comments');
      
      //$this->allow('studentAid', 'assignment', 'assignment_submit');
      //$this->allow('studentAid', 'async', 'async_class-roll-json');
      //$this->allow('studentAid', 'pages', 'pages_home');

      /* 
       * Users who haven't agreed to disclaimer can
       * only access certain things.
       */
      $this->allow('notActive','pages','pages_login');
      $this->allow('notActive','pages', 'pages_disclaimer');
      $this->allow('notActive','auth','auth_logout');
      $this->allow('notActive','auth','auth_cas');
      $this->allow('notActive', 'pages', 'pages_access-denied');
      $this->allow('notActive','error');
      //// delete bottom rule
      //$this->allow('notActive');

   }
}

?>
