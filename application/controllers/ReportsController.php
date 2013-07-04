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
       $reportsSession = $_SESSION['reports'];
       $Report = new My_Model_Report();
       $Report->by = $reportsSession['by'];
       $reportPeriod = "Report Period: ";
       if ($Report->by === "semester") {
          $Report->semId = $reportsSession['semesters_id'];
          $Semester = new My_Model_Semester();
          //$this->view->semester = $Semester->fetchRow("id = " . $Report->semId);
          $reportPeriod .= $Semester->fetchRow("id = " . $Report->semId)->semester;
       } elseif ($Report->by === "year") {
          $Report->year = $reportsSession['year'];
          $this->view->academicYear = $Report->year;
          $reportPeriod .= $Report->year;
       }

       $this->view->reportPeriod = $reportPeriod;

       $Assign = new My_Model_Assignment();
       $this->view->assigns = $Assign->getAll();
       $this->view->reports = $Report->assignments();

    }


    public function employerSatisfactionAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $reportsSession = $_SESSION['reports'];
       $Report = new My_Model_Report();
       $Report->by = $reportsSession['by'];
       $reportPeriod = "Report Period: ";
       if ($Report->by === "semester") {
          $Report->semId = $reportsSession['semesters_id'];
          $Semester = new My_Model_Semester();
          $reportPeriod .= $Semester->fetchRow("id = " . $Report->semId)->semester;
       } elseif ($Report->by === "year") {
          $Report->year = $reportsSession['year'];
          $reportPeriod .= $Report->year;
       }

       $this->view->reportPeriod = $reportPeriod;
       $this->view->results = $Report->employerSatisfaction();

       //die(var_dump($results));

    }

    // Just used to update years, not for actual application.
    public function updateYearsAction()
    {
       $sem = new My_Model_Semester();

       //$sem->updateYearColumn();
    }


}