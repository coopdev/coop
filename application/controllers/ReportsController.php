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
       
    }

    public function updateYearsAction()
    {

       $sem = new My_Model_Semester();
       $sem->updateYearColumn();

    }


}

