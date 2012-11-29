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
          $form->removeElement('saveOnly');
          $form->getElement('finalSubmit')->setLabel('Submit') ;

          if (!isset($_POST['resubmit'])) {

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
             //die(var_dump($data));



             // Populate the form based on data
             $form->setClassId($data['classes_id']);
             $form->setSemId($data['semesters_id']);
             $form->setUsername($data['username']);
             $form->checkSubmittedAnswers();
             //$form = $as->populateMidTermReport($form, $data);

             $this->view->form = $form;

          // If Post is coming from a re-submission by the coordinator.
          } else {
             $formData = $_POST['formData'];
             $userData = $_POST['userData'];
             //die(var_dump($formData, $userData));

             if ($form->isValid($formData)) {
                $assign = new My_Model_Assignment();
                $userData['assignments_id'] = $assign->getMidtermId();
                $submittedAssign = $assign->fetchSubmittedAssignment($userData);
                
                $where['submittedassignments_id'] = $submittedAssign->id;
                $assign->updateAnswers($formData, $where);

             } else {
                echo "<input type=hidden id='isInvalid' />";

                $this->view->form = $form;
             }

          }
             
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


          $Assignment = new My_Model_Assignment();

          //die(var_dump($data));
          
          
          
          if (isset($_POST['supervisorEval'])) {
             $data['assignments_id'] = $Assignment->getSupervisorEvalId();
          } else if (isset($_POST['coopAgreement'])) { 
             $data['assignments_id'] = $Assignment->getCoopAgreementId();
          } else if (isset($_POST['timesheet'])) {
             $data['assignments_id'] = $Assignment->getTimeSheetId();
          } else {
             $data['assignments_id'] = $Assignment->getStudentEvalId();
          }
             
          // check if student eval has been submitted first
          $res = $Assignment->isSubmitted($data);
          // if not submitted
          if ($res === false) {
             $this->view->submitted = false;
             return;
          }

          
          // Instantiate correct form.
          $formData = array('classId' => $data['classes_id'], 
                            'semId' => $data['semesters_id'],
                            'username' => $data['username']);
          if (isset($_POST['supervisorEval'])) {
             $form = new Application_Form_SupervisorEval($formData);
             
          }  else if (isset($_POST['coopAgreement'])) { 
             $form = new Application_Form_Agreement($formData);
             
          }  else if (isset($_POST['timesheet'])) { 
             $form = new Application_Form_TimeSheet($formData);
             
          } else {
             $form = new Application_Form_StudentEval($formData);
          }


          // Remove one of the submit buttons.
          $form->removeElement('saveOnly');
          $form->getElement('finalSubmit')
                ->setLabel("Resubmit");
          

          // Disable form elements.
          //foreach ($form as $f) {
          //   $f->setAttrib('disabled', true);
          //}

          $this->view->assign = $Assignment->getAssignment($data['assignments_id']);

          $this->view->form = $form;
       } else {
          // If not a POST request, don't render the view
          $this->_helper->viewRenderer->setNoRender();
       }

    }

    // Resubmits student or supervisor eval.
    public function resubmitAssignmentAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $this->_helper->viewRenderer->setNoRender();
       //$formData = $_POST['formDynamics'];
       if (isset($_POST['formStatics'])) {
          $statics = $_POST['formStatics'];
       } else {
          $statics = array();
       }

       if (isset($_POST['formDynamics'])) {
          $dynamics = $_POST['formDynamics'];
       } else {
          $dynamics = array();
       }
       
       $data = $_POST['data'];
       $assignment = $_POST['assignment'];

       //die(var_dump($formData, $data, $assignment));

       $assign = new My_Model_Assignment();

       if ($assignment === 'studentEval') {
          $data['assignments_id'] = $assign->getStudentEvalId();
       } else if ($assignment === 'supervisorEval') {
          $data['assignments_id'] = $assign->getSupervisorEvalId();
       } else if ($assignment === 'coopAgreement') {
          $data['assignments_id'] = $assign->getCoopAgreementId();
       } else if ($assignment === 'timesheet') {
          $data['assignments_id'] = $assign->getTimeSheetId();
       }

       $subAssign = $assign->fetchSubmittedAssignment($data);
       $where['submittedassignments_id'] = $subAssign->id;


       $assign->updateAnswers($statics, $where, array('static' => true));
       $assign->updateAnswers($dynamics, $where);


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
       $subAssignId = $_POST['submittedassignments_id'];
       //$assignId = $_POST['assignments_id'];

       //$data['assignments_id'] = $assignId;

       $assign = new My_Model_Assignment();
       $assign->undoSubmit($subAssignId);
    }



    // To set a students semester status, such as Incomplete.
    public function setStudentStatusAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $this->_helper->viewRenderer->setNoRender();

       if ($this->getRequest()->isPost()) {
          $status = $_POST['status'];
          $data = $_POST['data'];
          //die(var_dump($data));

          if ($status === 'Complete') {
             $status = "";
          }

          $semester = new My_Model_Semester();
          $semester->setStudentStatus($status, $data);
       }


    }


    public function getIncompletesAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
       $this->_helper->viewRenderer->setNoRender();

       if ($this->getRequest()->isPost()) {
          $classId = $_POST['classes_id'];
          //die(var_dump($classId));

          $user = new My_Model_User();
          $rows = $user->getIncompletes($classId);

          $json = Zend_Json_Encoder::encode($rows);

          echo $json;
       }


       //$json = json_encode($rows);


    }
}
