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
          $incompletes = $_POST['incompletes'];

          if (empty($incompletes)) {
             $this->view->resultMessage = "<p class='error'> No students were selected. </p>";
             return;
          }

          $Inc = new My_Model_Incompletes();
          $Inc->removeMultipleIncompleteSatuses($incompletes);
          $this->view->resultMessage = "<p class='success'> Incomplete status removed on selected students. </p>";

          $this->view->result = $Inc->fetchAll();
       }

       //die(var_dump($result));
    }


    public function destroyMultipleAction()
    {
    }


}

