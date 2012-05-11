<?php

class AssignmentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function submitAction()
    {
       $form = new Application_Form_SubmitAssignment();
       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          $as = new My_Model_Assignment();
          $result = $as->submit($data);


          if ($result === "submitted") {
             //$this->view->submitted = true;
             $this->view->message = "<p class='error'> That assignment has already been submitted </p>";
          } else {
             //$this->view->submitted = false;
             $this->view->message = "<p class='success'> Assignment has successfully been submitted </p>";
          }

       }

    }


}



