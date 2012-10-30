<?php

class AsyncController extends Zend_Controller_Action
{

    public function init()
    {
    }

    /**
     * Results of a student record search.
     * 
     * 
     */
    public function studentRecSearchResultAction()
    {
       if ($this->_request->isPost()) {
          $data = $_POST;
          //die(var_dump($data));

          $user = new My_Model_User();
          $results = $user->searchStudentRecs($data);
          //die(var_dump($results));

          $this->view->results = $results;

       } else {
          $this->_helper->viewRenderer->setNoRender();
       }

       $this->_helper->getHelper('layout')->disableLayout();
    }


    /**
     *  Displays a table of a specific student's assignment status (whether or not assignments have
     *  been submitted)
     */
    public function submissionRecsAction()
    {
       if ($this->getRequest()->isPost()) {
          $data = array();
          if (isset($_POST['data'])) {
             $data = $_POST['data'];
          }
          //die(var_dump($data));

          //die(var_dump($arr));

          $sub = new My_Model_SubmittedAssignment();
          $recs = $sub->getSubmissionRec($data);

          $user = new My_Model_User();

          $recText = $user->getSemesterInfo($data);
          $recText = $recText[0];
          $this->view->recText = $recText;

          $this->view->recs = $recs;

          //die(var_dump($recs));

       } else {
          $this->_helper->viewRenderer->setNoRender();
       }

       $this->_helper->getHelper('layout')->disableLayout();
    }

    
    /**
     * Displays a student's student info sheet for the coordinator to view.
     */
    public function viewStuInfoSheetAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       if ($this->getRequest()->isPost()) {
          $data = array();
          if (isset($_POST['data'])) {
             $data = $_POST['data'];
          }

          

          $user = new My_Model_User();

          $recText = $user->getSemesterInfo($data);

          //die(var_dump($recText));
          if (!empty($recText)) {
             $recText = $recText[0];
          }

          $this->view->recText = $recText;

          $form = new Application_Form_StudentInfo();

          $as = new My_Model_Assignment();

          $data['assignments_id'] = $as->getStuInfoId();
          // check if student info sheet has been submitted first
          $res = $as->isSubmitted($data);
          // if not submitted
          if ($res === false) {
             $this->view->submitted = false;
             return;
          }

          $form = $as->populateStuInfoSheet($form, array('username' => $data['username'],
                                                'semesters_id' => $data['semesters_id']));
          //die(var_dump($form));
          $form->removeElement('agreement');
          $elems = new My_FormElement();
          $uhuuid = $elems->getUuidTbox();
          $form->addElement($uhuuid, 'uhuuid', array('order' => 3));

          $empinfo = $user->getEmpInfo($data);

          $this->view->empinfo = $empinfo;
          $this->view->form = $form;

       } else {
          $this->_helper->viewRenderer->setNoRender();
       }

    }


    /**
     *  Displays all students for a specific class in JSON 
     */
    public function classRollJsonAction()
    {
       if ($this->getRequest()->isPost()) {

          $id = $_POST['id'];

          $class = new My_Model_Class();

          $rows = $class->getRollForCurrentSem($id);

          if (!is_array($rows)) {
             $rows = array();
          }

          //die(var_dump($rows));

          $json = Zend_Json_Encoder::encode($rows);
          //$json = json_encode($rows);

          echo $json;

       } 

       $this->_helper->viewRenderer->setNoRender();
       $this->_helper->getHelper('layout')->disableLayout();
    }

    /**
     * Displays a particular student's midterm report for a coordinator.
     * 
     * 
     * @return type 
     */
    public function midtermReportAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();

       if ($this->getRequest()->isPost()) {
          $form = new Application_Form_MidtermReport();

          $data = array();
          if (isset($_POST['data'])) {
             $data = $_POST['data'];
          }

          //die(var_dump($data));

          // To get text for the record being viewed (student's name, semester, class)
          $user = new My_Model_User();
          $recText = $user->getSemesterInfo($data);
          if (!empty($recText)) {
             $recText = $recText[0];
          }
          $this->view->recText = $recText;

          $as = new My_Model_Assignment();
          // Midterm Report's id
          $data['assignments_id'] = $as->getMidtermId();

          // check if midterm report has been submitted first
          $res = $as->isSubmitted($data);
          // if not submitted
          if ($res === false) {
             $this->view->submitted = false;
             return;
          }

          // Populate the form based on data
          $form = $as->populateMidTermReport($form, $data);

          // Take out submit button since it's for coordinator view
          $form->removeElement("Submit");
          $this->view->form = $form;
          
       } else {
          // If not a POST request, don't render the view
          $this->_helper->viewRenderer->setNoRender();
       } 
    }

    /**
     * Displays a particular student's learning outcome report for a coordinator.
     * 
     * 
     * @return type 
     */
    public function learningOutcomeAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();

       if ($this->getRequest()->isPost()) {
          $form = new Application_Form_LearningOutcomeReport();

          $data = array();
          if (isset($_POST['data'])) {
             $data = $_POST['data'];
          }

          //die(var_dump($data));

          // To get text for the record being viewed (student's name, semester, class)
          $user = new My_Model_User();
          $recText = $user->getSemesterInfo($data);
          if (!empty($recText)) {
             $recText = $recText[0];
          }
          $this->view->recText = $recText;

          $as = new My_Model_Assignment();
          // Learning Outcome Report's id
          $data['assignments_id'] = $as->getLearningOutcomeId();

          // check if learning outcome report has been submitted first
          $res = $as->isSubmitted($data);
          // if not submitted
          if ($res === false) {
             $this->view->submitted = false;
             return;
          }

          //die(var_dump($data));

          // Populate the form based on data
          $form = $as->populateLearningOutcome($form, $data);
          // the content of the textarea.
          $report = $form->getValue('report');

          // Take out submit button since it's for coordinator view
          $form->removeElement("Submit");
          $form->removeElement("SaveOnly");
          $this->view->form = $form;
          $this->view->report = $report;
          
       } else {
          // If not a POST request, don't render the view
          $this->_helper->viewRenderer->setNoRender();
       } 
    }

    /** 
     * Displays a particular student's student eval report for a coordinator.
     *
     * 
     * @return type 
     */
    public function studentEvalAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       if ($this->getRequest()->isPost()) {
          $data = array();
          if (isset($_POST['data'])) {
             $data = $_POST['data'];
          }

          //die(var_dump($data));
          
          // To get text for the record being viewed (student's name, semester, class)
          $user = new My_Model_User();
          $recText = $user->getSemesterInfo($data);
          if (!empty($recText)) {
             $recText = $recText[0];
          }
          $this->view->recText = $recText;


          // Set assignment id to student eval or supervisor eval depending on which button was clicked.
          $as = new My_Model_Assignment();
          if (isset($_POST['supervisorEval'])) {
             $data['assignments_id'] = $as->getSupervisorEvalId();
          } else {
             $data['assignments_id'] = $as->getStudentEvalId();
          }

          // check if student eval has been submitted first
          $res = $as->isSubmitted($data);
          // if not submitted
          if ($res === false) {
             $this->view->submitted = false;
             return;
          }



          $form = new Application_Form_StudentEval(array('classId' => $data['classes_id'], 
                                                         'assignId' => $data['assignments_id'],
                                                         'populateForm' => false));



          $form = $as->populateStudentEval($form, $data);
          //$rows = $as->populateStudentEval($form, $data);

          // Remove one of the submit buttons.
          $form->removeElement('saveOnly');
          $form->getElement('finalSubmit')
                ->setLabel("Submit");

          // Disable form elements.
          //foreach ($form as $f) {
          //   $f->setAttrib('disabled', true);
          //}

          $this->view->assign = $as->getAssignment($data['assignments_id']);

          $this->view->form = $form;
          //die(var_dump($rows));
          //var_dump($rows);
       } else {
          // If not a POST request, don't render the view
          $this->_helper->viewRenderer->setNoRender();
       }

    }

    /**
     * Displays the assignment status for all students in a particular class.
     * 
     * 
     * @return type 
     */
    public function assignmentStatusByClassAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       if ($this->getRequest()->isPost()) {
          //die(var_dump($_POST));
          $classId = $_POST['classId'];
          //die($classId);
          $sa = new My_Model_SubmittedAssignment();

          $recs = $sa->getAssignmentStatusByClass($classId);
          if($recs === "emptyClass") {
             $this->view->error = "<p class=error> Class is empty </p>";
             return;
          }

          $assign = new My_Model_Assignment();
          $assigns = $assign->getAll();

          $this->view->recs = $recs;
          $this->view->assigns = $assigns;

          //$this->_helper->viewRenderer->setRenderView(false);
          //echo "hello";
       } else {
          // If not a POST request, don't render the view
          $this->_helper->viewRenderer->setNoRender();
       }
       
    }

    /**
     * Displays student login records based on filter from form. 
     */
    public function viewLoginsAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       if ($this->getRequest()->isPost()) {

          $data = $_POST['data'];

          $login = new My_Model_Logins();
          $logins = $login->getLogins($data);

          //var_dump($logins);

          $this->view->logins = $logins;

       } else {
          $this->_helper->viewRenderer->setNoRender();
       }

    }

    /*
     * NOT BEING USED.
     */
    public function addStuevalOptionsAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $this->_helper->viewRenderer->setNoRender();
       $data = $_POST['data']['options'];

       var_dump($data);

       // Insert options.

    }


    public function undoSubmitAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $this->_helper->viewRenderer->setNoRender();
       $data = $_POST['data'];
       $assignId = $_POST['assignments_id'];

       $data['assignments_id'] = $assignId;

       $assign = new My_Model_Assignment();
       $assign->undoSubmit($data);
    }


    public function resubmitAssignmentAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $this->_helper->viewRenderer->setNoRender();
       $formData = $_POST['formData'];
       $data = $_POST['data'];
       $assignment = $_POST['assignment'];

       $assign = new My_Model_Assignment();

       if ($assignment === 'studentEval') {
          $data['assignments_id'] = $assign->getStudentEvalId();
       } else if ($assignment === 'supervisorEval') {
          $data['assignments_id'] = $assign->getSupervisorEvalId();
       }

       $assign->updateStudentEval($formData, $data);
       


    }
}
