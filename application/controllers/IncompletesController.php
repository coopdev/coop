<?php

class IncompletesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $Inc = new My_Model_Incompletes();

       $this->view->result = $Inc->fetchAll();

       if ($this->getRequest()->isPost()) {
          $incompletes = $_POST['students'];

          if (empty($incompletes)) {
             $this->view->resultMessage = "<p class='error'> No students were selected. </p>";
             return;
          }

          $Inc = new My_Model_Incompletes();
          $Inc->setMultipleIncompleteSatuses($incompletes, false);
          $this->view->resultMessage = "<p class='success'> Incomplete status removed on selected students. </p>";

          $this->view->result = $Inc->fetchAll();
       }

       //die(var_dump($result));
    }

    public function createMultipleAction()
    {
       $searchForm = new Application_Form_StudentRecSearch();
       
       $this->view->searchForm = $searchForm;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          if ($data['submittedForm'] === 'searchForm') {
             unset($data['submittedForm']);
             $Inc = new My_Model_Incompletes();
             $result = $Inc->searchCompletes($data);
             $this->view->students = $result;
             $this->view->searchClassId = $data['classes_id'];
             $this->view->searchSemId = $data['semesters_id'];
          } elseif ($data['submittedForm'] === 'manageIncompletes') {
             $students = $data['students'];
             $Inc = new My_Model_Incompletes();
             $Inc->setMultipleIncompleteSatuses($students);

             $this->view->students = $Inc->searchCompletes(
                     array('classes_id' => $data['searchClassId'], 
                           'semesters_id' => $data['searchSemId']));
          }

       }
    }


    public function destroyMultipleAction()
    {
    }


}

