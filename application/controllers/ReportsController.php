<?php

class ReportsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $form = new Application_Form_Report();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          if ($form->isValid($data)) {

             die(var_dump($form->bySemester->isChecked()));
             // Set session indicating by year or by semester.
             
             // Check which type of report was selected, then redirect to
             // appropriate action.

          }
       }
       
    }

}