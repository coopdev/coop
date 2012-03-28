<?php

class SyllabusController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }

    public function listallAction()
    {
       $link = My_DbLink::connect();
       $courses = $link->fetchAll('SELECT * FROM coop_courses');
       $this->view->courses = $courses;
    }

    public function viewAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $link = My_DbLink::connect();
       if ($this->_request->isGet()) {
                   
          // Cast the value of 'id' to an int. Returns zero if not an int.
          $id = (int)$this->_request->getQuery('id');
             
          // If user is not a student and id is empty, redirect to the listall 
          // page so a proper course can be chosen.
          if ($coopSess->role != 'normal' && empty($id)) {
             $this->_helper->redirector('listall');
          }
                      
          // If user is a student with a normal role, overwrite the $id variable with
          // the course id stored in their record so they can only view the 
          // syllabus for their course, and not get to other ones through the url.
          if ($coopSess->role == 'normal') {
             $uuid = $coopSess->uhinfo['uhuuid'];
             $id = (int)$link->fetchOne("SELECT courses_id FROM coop_users 
                                    WHERE uuid = '$uuid'");
          }
          
          $course = $link->fetchRow("SELECT id, name, syllabus FROM coop_courses 
                                     WHERE id = $id");
      
          if ($course) {
             $this->view->course = $course;
          } else {
             $this->view->noCourse = true;
          }
       } 
    }
    
    private function getSession()
    {
       return new Zend_Session_Namespace('coop');
    }


}





