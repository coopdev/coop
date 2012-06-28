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
          if ($coopSess->role === 'user') {
             $classId = $coopSess->currentClassId;

             //die($classId);
             //$classId = (int)$link->getCol('coop_users_semesters', 'classes_id', 
             //                             array('student'=>$coopSess->username));
             //$id = (int)$link->fetchOne("SELECT classes_id FROM coop_users 
             //                       WHERE uuid = '$uuid'");
          }

          $syl = new My_Model_Syllabus();
          $sylText = $syl->getFinal($classId);
          //die(var_dump($sylText));
          
          if ($sylText !== false) {
             $this->view->sylText = $sylText;
             $this->view->classId = $classId;
          } else {
             $this->view->noclass = true;
          }
       } 
    }

    /*
     * Populates the textarea with the DRAFT syllabus of the chosen class for editing.
     * Updates the DRAFT syllabus every time it is submitted, and updates both the DRAFT
     * and FINAL syllabus when the "Submit as final" checkbox is checked.
     * 
     */
    public function editAction()
    {
       $syl = new My_Model_Syllabus();

       if ( $this->getRequest()->isPost() ) {
          $data = $_POST;

          //die(var_dump($data));

          // update draft everytime
          $res = $syl->editDraft($data);

          if ($res === false) {
             $message = "<p class=error> Error updating syllabus </p>";
             return;
          }

          // if the "Submit as final" checkbox was checked, then also update the final draft
          if (isset($data['isFinal'])) {
             $res = $syl->editFinal($data);

             if ($res === false) {
                $message = "<p class=error> Error updating syllabus </p>";
                return;
             }

          }

          $message = "<p class=success> Syllabus has been updated </p>";
          $this->view->message = $message;

          // define classId and sylText again so the view can set the values to the text
          // area and hidden classId incase more editing for the same class syllabus needs 
          // to be done.
          $this->view->classId = $data['classId'];
          $this->view->sylText = $data['syllabus'];


       } else if ( $this->getRequest()->isGet() ) {

          // make sure a class has been chosen first
          if (isset($_GET['id']) && !empty($_GET['id'])) {
             $classId = $_GET['id'];
             $this->view->classId = $classId;

             $sylText = $syl->getDraft($classId);
             //die(var_dump($sylText));

             $this->view->sylText = $sylText;

          } else {
             $this->view->classId = false; // classId used in view to give error message
          }

       }


    }
}