<?php

class SyllabusController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /*
     * Lists all class syllabuses. Only teachers should be able to view this, and
     * maybe admins.
     */
    public function listallAction()
    {
       $link = My_DbLink::connect();
       $classes = $link->fetchAll('SELECT * FROM coop_classes');
       $this->view->classes = $classes;
    }
    
    /* Displays an individual class syllabus. The syllabus displayed is based
     * on a value passed through the url for non-students. For students, the
     * syllabus is retrieved from the database based on the students class.
     */
    public function viewAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $link = new My_Db();
       if ($this->_request->isGet()) {
                   
          // Cast the value of 'id' to an int. Returns zero if not an int.
          $classId = (int)$this->_request->getQuery('id');
             
          // If user is not a student and id is empty, redirect to the listall 
          // page so a proper class can be chosen.
          if ($coopSess->role != 'user' && empty($classId)) {
             $this->_helper->redirector('listall');
          }
                      
          // If user is a student with a "user" role, overwrite the $id variable with
          // the class id stored in their record so they can only view the 
          // syllabus for their class, and not get to other ones through the url.
          if ($coopSess->role == 'user') {
             $classId = (int)$link->getCol('coop_users_semesters', 'classes_id', 
                                          array('student'=>$coopSess->username));
             //$id = (int)$link->fetchOne("SELECT classes_id FROM coop_users 
             //                       WHERE uuid = '$uuid'");
          }
          
          $class = $link->fetchRow("SELECT id, name, syllabus FROM coop_classes 
                                     WHERE id = $classId");
      
          if ($class) {
             $this->view->class = $class;
          } else {
             $this->view->noclass = true;
          }
       } 
    }
}





