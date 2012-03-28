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
       if ($this->_request->isGet()) {
          // Cast the value of 'id' to an int. Returns zero if not an int.
          $id = (int)$this->_request->getQuery('id');
          //die(var_dump($id));
          if (!empty($id)) {
             $link = My_DbLink::connect();
             
             $course = $link->fetchRow("SELECT id, name, syllabus FROM coop_courses 
                                       WHERE id = $id");
             
             $this->view->course = $course;
          } else {
             $this->view->course = "No course seleced";
          }
          
       } 
    }


}





