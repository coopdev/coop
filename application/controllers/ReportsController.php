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
             unset($_SESSION['reports']);
             if ($form->bySemester->isChecked()) {
                if (trim($data['semesters_id']) === "") {
                   $this->view->resultMessage = "<p class='error'> Must Select a Semester. </p>";
                   return;
                }

                $_SESSION['reports']['by'] = 'semester';
                $_SESSION['reports']['semesters_id'] = $data['semesters_id'];
             } elseif ($form->byYear->isChecked()) {
                if (trim($data['year']) === "") {
                   $this->view->resultMessage = "<p class='error'> Must Select Academic Year. </p>";
                   return;
                }
                $_SESSION['reports']['by'] = 'year';
                $_SESSION['reports']['year'] = $data['year'];
             }

             if ($data['report'] === 'assignment') {
                $this->_helper->redirector('assignments');
             } elseif ($data['report'] === 'empSatisfaction') {
                $this->_helper->redirector('employer-satisfaction');
             }
          }
       }
       
    }

    public function assignmentsAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();

       $Report = new My_Model_Report($_SESSION['reports']);
       $this->view->reportPeriod = $Report->reportPeriod;
       $this->view->reports = $Report->assignments();

       $Assign = new My_Model_Assignment();
       $this->view->assigns = $Assign->getAll();

    }


    public function employerSatisfactionAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();

       $Report = new My_Model_Report($_SESSION['reports']);
       $this->view->reportPeriod = $Report->reportPeriod;
       $this->view->results = $Report->employerSatisfaction();

    }

    // Just used to update years, not for actual application.
    public function updateYearsAction()
    {
       $sem = new My_Model_Semester();

       //$sem->updateYearColumn();
    }


}