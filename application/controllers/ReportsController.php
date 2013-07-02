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
             if ($form->bySemester->isChecked()) {
                $_SESSION['reports']['by'] = 'semester';
                $_SESSION['reports']['semesters_id'] = $data['semesters_id'];
             } elseif ($form->byYear->isChecked()) {
                $_SESSION['reports']['by'] = 'year';
                $_SESSION['reports']['year'] = $data['year'];
             }

             if ($data['report'] === 'assignment') {
                $this->_helper->redirector('assignments');
             }

             // Set session indicating by year or by semester.
             
             // Check which type of report was selected, then redirect to
             // appropriate action.

          }
       }
       
    }

    public function assignmentsAction()
    {
       $reportsSession = $_SESSION['reports'];
       $_SESSION['reports'] = null;
       $Report = new My_Model_Report();
       $Report->by = $reportsSession['by'];
       if ($Report->by === "semester") {
          $Report->semId = $reportsSession['semesters_id'];
       } elseif ($Report->by === "year") {
          $Report->year = $reportsSession['year'];
       }

       $this->view->report = $Report->assignmentsReport();

    }

    // Just used to update years, not for actual application.
    public function updateYearsAction()
    {
       $sem = new My_Model_Semester();

       $sem->updateYearColumn();
    }

}