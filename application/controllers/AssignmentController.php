<?php

class AssignmentController extends Zend_Controller_Action
{
    private $flashMessenger;

    public function init()
    {
        $this->flashMessenger = $this->_helper->FlashMessenger;
    }

    // form to choose class to see assignment status for each of the classe's student's
    public function listStatusByClassAction()
    {
       $form = new Zend_Form();

       $elems = new My_FormElement();
       $classDropdown = $elems->getClassChoiceSelect();
       $classDropdown->setLabel('Select class');
       //die(var_dump($classDropdown));
       $submit = new Zend_Form_Element_Button('Submit');
       //die(var_dump($submit));

       $form->addElements(array($classDropdown, $submit));

       $this->view->form = $form;
    }

/***********************************MAIN ASSIGNMENTS*************************************/

    public function midtermReportAction()
    {

       $assignment = new My_Model_Assignment();
       $assignId = $assignment->getMidtermId();

       if ($assignment->isDue($assignId)) {
          $this->view->message = "<p class=error> This assignment is past it's due date </p>";
          return;
       }

       $form = new Application_Form_MidtermReport();
       $form->checkSubmittedAnswers();

       $this->view->form = $form;

       $req = $this->getRequest();
       if ($req->isPost()) {
          $data = $_POST;
          //unset($data['Submit']);
          //die(var_dump($data));

          if ($form->isValid($data)) {

             $as = new My_Model_Assignment();
             $res = $as->submitMidtermReport($data);

             if ($res === "submitted") {
                $this->view->message = "<p class=error> Assignment has already been submitted </p>";
                //$this->flashMessenger->addMessage(array('error' => "Assignment has already been submitted"));
             } else if ($res === false) {
                $this->view->message = "<p class=error> Error occured </p>";
                //$this->flashMessenger->addMessage(array('error' => "Error occured"));
             } else if ($res === true) {
                $this->view->message = "<p class=success> Assignment has been submitted </p>";
                //$this->flashMessenger->addMessage(array('success' => "Assignment has been submitted"));
             }
             //$this->_helper->redirector('midterm-report');

          }
       }

    }

    public function studentEvalAction()
    {
       $assignment = new My_Model_Assignment();
       $assignId = $assignment->getStudentEvalId();

       if ($assignment->isDue($assignId)) {
          $this->view->message = "<p class=error> This assignment is past it's due date </p>";
          return;
       }

       $coopSess = new Zend_Session_Namespace('coop');
       $classId = $coopSess->currentClassId;
       $semId = $coopSess->currentSemId;
       $username = $coopSess->username;

       $form = new Application_Form_StudentEval(array('classId' => $classId, 
                                                      'semId' => $semId,
                                                      'username' => $username));

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;


          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();

             $res = $as->submitStudentEval($form);

             if ($res === true) {
                $message = "<p class=success> Success </p>";
             } else if ($res === 'submitted') {
                $message = "<p class=error> Evaluation has already been submitted </p>";
             } else {
                $message = "<p class=error> Error occured </p>";
             }

             //$this->view->form->reset();

             $this->view->message = $message;

          }
       }
    }

    public function supervisorEvalAction()
    {
       $as = new My_Model_Assignment();
       $coopSess = new Zend_Session_Namespace('coop');
       

       if ($coopSess->role === 'user') {
           $assignId = $as->getSupervisorEvalId();
           if ($as->isDue($assignId)) {
              $this->view->message = "<p class=error> This assignment is past it's due date </p>";
              return;
           }
           
           $classId = $coopSess->currentClassId;
           $username = $coopSess->username;
           $semId = $coopSess->currentSemId;
       } else {
           $subForStudentData = $coopSess->submitForStudentData;
           $classId = $subForStudentData['classes_id'];
           $username = $subForStudentData['username'];
           $semId = $subForStudentData['semesters_id'];
       }

                                                      
       $form = new Application_Form_SupervisorEval(array('username' => $username, 
                                                         'classId' => $classId,
                                                         'semId' => $semId));
       //die($form->static_tasks->address->getValue());

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {

          $data = $_POST;
          //$data = $form->getAllElementsIncludingNested();
          //die(var_dump($data));

          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();
             $res = $as->submitStudentEval($form);
             //die(var_dump($res));

             if ($res === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else if ($res === 'submitted') {
                $this->view->resultMessage = "<p class='error'> Assignment has already been submitted </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }
          } 

       }

       //die($classId);
    }

    public function timesheetAction()
    {
       $as = new My_Model_Assignment();
       $coopSess = new Zend_Session_Namespace('coop');

       if ($coopSess->role === 'user') {
           $assignId = $as->getTimeSheetId();
           if ($as->isDue($assignId)) {
              $this->view->message = "<p class=error> This assignment is past it's due date </p>";
              return;
           }
           
           $classId = $coopSess->currentClassId;
           $username = $coopSess->username;
           $semId = $coopSess->currentSemId;
       } else {
           $subForStudentData = $coopSess->submitForStudentData;
           $classId = $subForStudentData['classes_id'];
           $username = $subForStudentData['username'];
           $semId = $subForStudentData['semesters_id'];
       }
                                                      
       $form = new Application_Form_TimeSheet(array('username' => $username, 
                                                   'classId' => $classId,
                                                   'semId' => $semId));

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {

          $data = $_POST;
          //$data = $form->getAllElementsIncludingNested();
          //die(var_dump($data));

          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();
             $res = $as->submitStudentEval($form);

             if ($res === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else if ($res === 'submitted') {
                $this->view->resultMessage = "<p class='error'> Assignment has already been submitted </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }
          } else {
             $this->view->resultMessage = "<p class='error'> One or more fields have errors </p>";
          } 

       }

       //die($classId);
    }
    
    public function resumeAction()
    {
       $as = new My_Model_Assignment();
       $session = new Zend_Session_Namespace('coop');
       
       $assignId = $as->getResumeId();
       if ($as->isDue($assignId)) {
          $this->view->message = "<p class=error> This assignment is past it's due date </p>";
          return;
       }

       $form = new Application_Form_Resume(array('username' => $session->username, 
                                                 'classId' => $session->currentClassId,
                                                 'semId' => $session->currentSemId));

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {

          $data = $_POST;
          //$data = $form->getAllElementsIncludingNested();
          //die(var_dump($data['static_tasks']));

          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();
             $res = $as->submitStudentEval($form);

             if ($res === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else if ($res === 'submitted') {
                $this->view->resultMessage = "<p class='error'> Assignment has already been submitted </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }
          } else {
             $this->view->resultMessage = "<p class='error'> One or more fields have errors </p>";
          } 

       }

    }

    public function coverLetterAction()
    {
       $as = new My_Model_Assignment();
       $session = new Zend_Session_Namespace('coop');
       
       $assignId = $as->getCoverLetterId();
       if ($as->isDue($assignId)) {
          $this->view->message = "<p class=error> This assignment is past it's due date </p>";
          return;
       }

       $form = new Application_Form_CoverLetter(array('username' => $session->username, 
                                                 'classId' => $session->currentClassId,
                                                 'semId' => $session->currentSemId));
       //echo $form->static_tasks->coverLetter;
       $form->static_tasks->coverLetter->setValue($form->static_tasks->coverLetter->getValue());

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {

          $data = $_POST;
          //$data = $form->getAllElementsIncludingNested();
          //die(var_dump($data['static_tasks']));

          //die(var_dump($data));
          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();
             $res = $as->submitStudentEval($form);

             if ($res === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else if ($res === 'submitted') {
                $this->view->resultMessage = "<p class='error'> Assignment has already been submitted </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }
          } else {
             $this->view->resultMessage = "<p class='error'> One or more fields have errors </p>";
          } 

       }


    }
    
    
    public function supervisorEvalPdfAction()
    {
       if ($this->getRequest()->isPost()) {
          $classId = $_POST;

          $classId = rawurlencode(serialize($classId));

          $server = $_SERVER['SERVER_NAME'];

          $coopSess = new Zend_Session_Namespace('coop');
          $baseUrl = $coopSess->baseUrl;
          $classId = $coopSess->currentClassId;

          // Returns the rendered HTML as a string
          //$page = file_get_contents("http://$server$baseUrl/form/coop-agreement-pdf?data=".$data);

          exec(APPLICATION_PATH . "/../pdfs/wkhtmltopdf-i386  http://$server$baseUrl/assignment/supervisor-eval-pdf?role=A592NXZ71680STWVR926\&classId=$classId " . 
                  APPLICATION_PATH . '/../pdfs/supervisorEval.pdf');

          $pdfPath = APPLICATION_PATH . '/../pdfs/supervisorEval.pdf';
          $pdf = Zend_Pdf::load($pdfPath);
          header("Content-Disposition: attachment; filename=Supervisor Evaluation.pdf");
          header("Content-type: application/x-pdf");
          $pdfData = $pdf->render();

          echo $pdfData;

          $this->_helper->layout->disableLayout();
          $this->_helper->viewRenderer->setNoRender(true);

       } else if ($this->getRequest()->isGet()) {

          if (isset($_GET['classId'])) {
             $classId = $_GET['classId'];

             $as = new My_Model_Assignment();
             $assignId = $as->getSupervisorEvalId();

             //die($data);

             $form = new Application_Form_StudentEval(array('classId' => $classId, 
                                                            'assignId' => $assignId));
             $form->removeElement('Submit');
             //$form->populate($classId);

             $this->view->form = $form;

             $this->_helper->layout->disableLayout();
          }


       }

    }

    public function learningOutcomeAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       
       $form = new Application_Form_LearningOutcomeReport();
       $form->setUsername($coopSess->username);
       $form->setClassId($coopSess->currentClassId);
       $form->setSemId($coopSess->currentSemId);

       $as = new My_Model_Assignment();
       
       // Check if due.
       $assignId = $as->getLearningOutcomeId();
       if ($as->isDue($assignId)) {
          $this->view->message = "<p class=error> This assignment is past it's due date </p>";
          return;
       }

       //$form = $as->populateLearningOutcome($form, array('classes_id' => $coopSess->currentClassId,
       //                         'semesters_id' => $coopSess->currentSemId,
       //                         'username' => $coopSess->username));

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          // Use this data to validate form because the HTML tags in the rich text area
          // count as character, but we don't want them to.
          $dataNoHTML = $data;
          $dataNoHTML['report'] = trim(strip_tags($dataNoHTML['report']));


          if ($form->isValid($dataNoHTML)) {
             $form->submit();
             //$data['report'] = htmlspecialchars($data['report']);
             //$res = $as->submitLearningOutcome($data);

             if ($res === 'submitted') {
                $message = "<p class=error> Already submitted </p>";
             } else if ($res === false) {
                $message = "<p class=error> Error occured </p>";
             } else {
                $message = "<p class=success> Success </p>";
             }

             $this->view->message = $message;
          }

       }
    }

    public function learningOutcomeEditAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       
       $form = new Application_Form_LearningOutcomeReport();
       $form->setUsername($coopSess->username);
       $form->setClassId($coopSess->currentClassId);
       $form->setSemId($coopSess->currentSemId);

       $form->setSavedReports();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {

          $data = $_POST;
          
          // Use this data to validate form because the HTML tags in the rich text area
          // count as character, but we don't want them to.
          $dataNoHTML = $data;
          $dataNoHTML['report'] = trim(strip_tags($dataNoHTML['report']));

          if ($form->isValid($dataNoHTML)) {

          }
       }
       

    }


/***********************************END MAIN ASSIGNMENTS*********************************/


    // for submitting offline assignments
    public function submitAction()
    {
       $form = new Application_Form_SubmitAssignment();
       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          //die(var_dump($data['semesters_id']));

          if ($form->isValid($data)) {

             $coopSess = new Zend_Session_Namespace('coop');
             
             // Set session data for assignment submissions on other pages.
             $coopSess->submitForStudentData['classes_id'] = $data['classes_id'];
             $coopSess->submitForStudentData['assignments_id'] = $data['assignments_id'];
             $coopSess->submitForStudentData['username'] = $data['username'];
             $coopSess->submitForStudentData['semesters_id'] = $data['semesters_id'];

             // Get assignment number.
             $as = new My_Model_Assignment();
             $assignRow = $as->getAssignment($data['assignments_id']);
             $assignNum = $assignRow['assignment_num'];
             
             // Redirect to appropriate controller depending on assignment chosen.
             if ($assignNum === '3') {
                $this->_helper->redirector('coop-agreement', 'form');
             } else if ($assignNum === '6') {
                $this->_helper->redirector('supervisor-eval', 'assignment');
             } else if ($assignNum === '7') {
                $this->_helper->redirector('timesheet', 'assignment');
             }
             //die(var_dump($assignNum));

          }

       }

    }

/**********************************ASSIGNMENT LISTS**************************************/
    public function listAllAction()
    {
       $as = new My_Model_Assignment();

       $assignments = $as->getAll();

       $this->view->assignments = $assignments;

    }

    public function listAllForStudentAction()
    {
       $as = new My_Model_Assignment();

       $assignments = $as->getNonSubmitted();

       $this->view->assignments = $assignments;
    }

    public function listSubmittedAction()
    {
       $as = new My_Model_Assignment();

       $assignments = $as->getSubmitted();

       $this->view->assignments = $assignments;

    }
/**********************************END ASSIGNMENT LISTS**********************************/

    public function propertiesAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       if ($this->getRequest()->isGet()) {

          if (!isset($_GET['id'])) {
             $this->view->message = "<p class=error> Must select an assignment first </p>";
             return;
          }

          $id = $_GET['id'];

       } else if ($this->getRequest()->isPost()) {
          $classId = $_POST['classes_id'];
          $coopSess->stuEvalManagementData['classId'] = $classId;
          $id = $coopSess->stuEvalManagementData['assignId'];
          $class = new My_Model_Class();
          $this->view->class = $class->getClass($classId);
       }
       $as = new My_Model_Assignment();

       $assign = $as->getAssignment($id);
       //die(var_dump($assign));
       if ($assign['assignment_num'] === '4') {
          $this->_helper->redirector('manage-learning-outcome');
       }

       

       $funcs = new My_Funcs();
       $assign['due_date'] = $funcs->formatDateOut($assign['due_date']);

       $this->view->assign = $assign;
    }
    


/**********************************ASSIGNMENT MANAGEMENT*********************************/

    public function editDuedateAction()
    {

       $as = new My_Model_Assignment();

       $assigns = $as->getAll();
       $this->view->assignments = $assigns;

       $form = new Application_Form_EditDuedate($assigns);
       $this->view->form = $form;

          //$id = $_GET['id'];
          //$assign = $as->getAssignment($id);
          //$funcs = new My_Funcs();
          //$assign['due_date'] = $funcs->formatDateOut($assign['due_date']);
          //$form->populate($assign);

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $res = $as->updateDuedates($data);

             if ($res === true) {
                $this->view->result = "<p class=success> Updated Due Dates </p>";
             } else if ($res ===false) {
                $this->view->result = "<p class=error> Failed to Update </p>";
             }
             
          }
       }

    }

    public function extendDuedateAction()
    {
       $form = new Application_Form_ExtendDuedates();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          //die(var_dump($data));
          $this->view->lastStudent = $data['username'];

          if ($form->isValid($data)) {
             unset($data['classes_id']);
             $as = new My_Model_Assignment();
             $res = $as->extendDuedate($data);
             if ($res) {
                $this->view->message = "<p class=success> Due date has been extended </p>";
             } else {
                $this->view->message = "<p class=error> Failed to extend due date </p>";
             }
          }
       }

    }

    /*
     * View that displays links to Add, Edit, Delete the questions that are rated 
     * (i.e. the questions that are rated from 1-4 or NA). These questions appear on the
     * Student Eval, Supervisor Eval, Coop Agreement forms.
     */
    public function manageRatedQuestionsAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');

       $elems = new My_FormElement();

       $this->view->classes = $elems->getClassChoiceSelect()->setLabel("Select Class");


    }


    public function addRatedQuestionAction()
    {
       $form = new Application_Form_AddRatedQuestion();
       $this->view->form = $form;
       $coopSess = new Zend_Session_Namespace('coop');
       
       // If request is a GET, must set the classId to what was chosen from previous view.
       if ($this->getRequest()->isGet()) {
          $coopSess->addRatedQuestionClassId = $_GET['classId'];

       } else if ($this->getRequest()->isPost()) {

          $data = $_POST;

          if ($form->isValid($data)) {
             $Assign = new My_Model_Assignment();
             $data['classes_id'] = $coopSess->addRatedQuestionClassId;
             $data['assignments_id'] = $Assign->getStudentEvalId();

             $res = $Assign->addRatedQuestion($data);

             if ($res === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }

          }
       }
    }


    public function editRatedQuestionAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');

       if ($this->getRequest()->isGet()) {
          $coopSess->editRatedQuestionClassId = $_GET['classId'];
       }

       $form = new Application_Form_EditRatedQuestions(array(
                                'classId' => $coopSess->editRatedQuestionClassId));

       $this->view->form = $form;
       $this->view->formName = $form->formName;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          unset($data['Submit']);

          if ($form->isValid($data)) {
             $Assign = new My_Model_Assignment();
             $where['classes_id'] = $coopSess->editRatedQuestionClassId;
             $where['assignments_id'] = $Assign->getStudentEvalId();

             $res = $Assign->updateRatedQuestions($data, $where);

             if ($res === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }
          }
       }
    }

    public function deleteRatedQuestionAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');

       if ($this->getRequest()->isGet()) {
          $coopSess->deleteRatedQuestionClassId = $_GET['classId'];
       }

       $form = new Application_Form_DeleteRatedQuestions(array(
                                'classId' => $coopSess->deleteRatedQuestionClassId));

       $this->view->form = $form;

       $Class = new My_Model_Class();

       if ($this->getRequest()->isPost()) {
          $questionIds = $_POST['questions'];
          $Assign = new My_Model_Assignment();

          $res = $Assign->deleteRatedQuestions($questionIds);

          if ($res === true) {
             $this->view->resultMessage = "<p class='success'> Success </p>";
             
             $form = new Application_Form_DeleteRatedQuestions(array(
                                      'classId' => $coopSess->deleteRatedQuestionClassId));

             $this->view->form = $form;
          } else {
             $this->view->resultMessage = "<p class='error'> Error </p>";
          }

       }

    }
    

    public function addQuestionAction()
    {
       $form = new Application_Form_AddQuestion();
       $this->view->form = $form;

       if ($this->getRequest()->isGet()) {
          if (!isset($_GET['id'])) {
             $this->view->noAssignment = true;
             return;
          }

          $id = $_GET['id'];
          $this->view->assignId = $id;
          $temp['assignId'] = $id;
          $form->populate($temp);

       } else if ($this->getRequest()->isPost()) {

          $data = $_POST;

          $id = $_GET['id'];
          $this->view->assignId = $id;
          $temp['assignId'] = $id;
          $form->populate($temp);

          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();
             $res = $as->addQuestion($data);
             if ($res === true) {
                $this->view->message = "<p class=success> Question added </p>";
             } else if ($res === false) {
                $this->view->message = "<p class=error> Failed to add question </p>";
             }

          }
          //die(var_dump($data));

       }
       
    }

    public function editQuestionsAction()
    {
       if ($this->getRequest()->isGet()) {
          if (!isset($_GET['id'])) {
             $this->view->result = "<p class=error> Must select an assignment first </p>";
             return;
          }

          $id = $_GET['id'];

          $this->view->assignId = $id;

          $as = new My_Model_Assignment();

          $questions = $as->getQuestions($id);
          //die(var_dump($questions));
          $form = new Application_Form_EditQuestions();
          $form->setAttrib('class', 'editQuestions');
          foreach ($questions as $q) {

             $qNum = $q['question_number'];

             $subf = new Zend_Form_SubForm();

             $qText = new Zend_Form_Element_Textarea("question_text");
             $qText->setLabel("Question # ". $q['question_number'])
                   ->setRequired(true)
                   ->addFilter("StringTrim")
                   ->addFilter("StripTags")
                   ->setAttrib('rows', '10');
             $minLen = new Zend_Form_Element_Text("answer_minlength");
             $minLen->setRequired(true)
                    ->setLabel("Answer's minimum length:")
                    ->addValidator(new Zend_Validate_Digits())
                    ->addFilter("StringTrim")
                    ->addFilter("StripTags");

             $subf->addElements(array($qText, $minLen));
             $subf->populate($q);


             $form->addSubForm($subf, "$qNum");

          }

          $submit = new Zend_Form_Element_Submit("submit");
          $submit->setLabel("Submit");
          $hiddenId = new Zend_Form_Element_Hidden('assignId');
          $hiddenId->setValue($id);
          $form->addElements(array($hiddenId,$submit));
          $this->view->form = $form;
       } else if ($this->getRequest()->isPost()) {
       //if ($this->getRequest()->isPost()) {
          $data = $_POST;
          $this->view->assignId = $data['assignId'];
          //die(var_dump($data));

          $as = new My_Model_Assignment();

          $res = $as->updateQuestions($data);

          if ($res === false) {
             $this->view->result = "<p class=error> Unable to update </p>";
          } else if ($res === true) {
             $this->view->result = "<p class=success> Updated successfully </p>";
          }

       }
    }

    public function deleteQuestionAction()
    {
       $req = $this->getRequest();
       if ($req->isGet()) {

          $assignId = $req->getParam('id');
          $params = $req->getParams();
          if ($req->getParam('result') === 'success') {
             $this->view->message = "<p class=success> Question has been deleted </p>";
          } else if ($req->getParam('result') === 'fail') {
             $this->view->message = "<p class=error> Failed to delete question </p>";
          }

          
          $form = new Application_Form_DeleteQuestion($assignId);
          $this->view->form = $form;
          $this->view->assignId = $assignId;

       } else if ($req->isPost()) {

          $data = $_POST;

          $assignId = $data['assignments_id'];
          $form = new Application_Form_DeleteQuestion($assignId);
          $this->view->form = $form;

          if ($form->isValid($data)) {
             $questions = $data['questions'];
             $as = new My_Model_Assignment();
             $res = $as->deleteQuestion($questions, $assignId);

             if ($res === true) {
                $result = 'success';
             } else {
                $result = 'fail';
             }

             $this->_helper->redirector('delete-question', 'assignment', null, array('id' => $assignId, 'result' => $result));

          }

          //die(var_dump($data));
       }

    }

    public function manageLearningOutcomeAction()
    {
       $assign = new My_Model_Assignment();
       $learnOutcome = $assign->fetchRow("assignment_num = '4'");
       $this->view->assign = $learnOutcome;

       $form = new Application_Form_LearningOutcomeMgmt();
       $form->populate($learnOutcome->toArray());
       $this->view->form = $form;

       $request = $this->getRequest();
       if ($request->isPost()) {
          $data = $_POST;
          if ($form->isValid($data)) {
             $learnOutcome->answer_minlength = $data['answer_minlength'];
             try {
                $learnOutcome->save();
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } catch (Exception $e) {
                $this->view->resultMessage = "<p class='error'> Error Occured </p>";
             }
             //var_dump($data);
          }
       }

    }


    public function studentEvalChooseClassAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');

       $form = new Zend_Form;
       $form->setAction('properties');
       $elems = new My_FormElement();
       $classes = $elems->getClassChoiceSelect();
       $classes->setLabel("Choose class:");
       $submit = $elems->getSubmit();
       $form->addElements(array($classes, $submit));

       $this->view->form = $form;

       $assign = new My_Model_Assignment();


       if ($this->getRequest()->isGet()) {
          $id = $_GET['id'];


          $coopSess->stuEvalManagementData['assignId'] = $id;
          $optionForm = new Application_Form_SurveyOptions(array('surveyName' => 'Student Eval'));

       } else if ($this->getRequest()->isPost()) {
          $optionForm = new Application_Form_SurveyOptions(array('surveyName' => 'Student Eval'));
          $data = $_POST;
          $data['assignments_id'] = $coopSess->stuEvalManagementData['assignId'];
          //die(var_dump($data));
          if ($optionForm->isValid($data)) {
             $result = $assign->setSurveyGlobalOptionAmount($data);

             if ($result === true) {
                $this->view->resultMessage = "<p class='success'> Success </p>";
             } else {
                $this->view->resultMessage = "<p class='error'> Error </p>";
             }
          } 


       }

       $this->view->optionForm = $optionForm;
       $this->view->assign = $assign->getAssignment($coopSess->stuEvalManagementData['assignId']);
       
    }

    public function addQuestionStudentEvalAction()
    {
       $form = new Application_Form_AddQuestionStudentEval();
       $this->view->form = $form;

       if ($this->getRequest()->isGet()) {
          $coopSess = new Zend_Session_Namespace('coop');

       } else if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();
             $res = $as->addQuestionStudentEval($data);

             if ($res === true) {
                $message = "<p class=success> Question has been added </p>";
             } else {
                $message = "<p class=error> Error occured </p>";
             }

             $form = new Application_Form_AddQuestionStudentEval();
             $this->view->form = $form;
             $this->view->message = $message;
          }
       }
    }

    public function editQuestionStudentEvalAction()
    {

       $form = new Application_Form_EditQuestionStudentEval();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $as = new My_Model_Assignment();

             $res = $as->updateQuestionsStuEval($data);

             if ($res === true) {
                $message = "<p class=success> Question has been updated </p>";
             } else {
                $message = "<p class=error> Error occured </p>";
             }

             $this->view->message = $message;
          }

          //die(var_dump($data));

       }

    }

    public function deleteQuestionStudentEvalAction()
    {
       $aq = new My_Model_AssignmentQuestions();

       $form = new Application_Form_DeleteQuestionStudentEval();
       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {

             $as = new My_Model_Assignment();
             $res = $as->deleteQuestionsStudentEval($data);

             if ($res === true) {
                $message = "<p class=success> Question has been deleted </p>";
             } else {
                $message = "<p class=error> Error occured </p>";
             }

             $this->view->message = $message;
             
             $form = new Application_Form_DeleteQuestionStudentEval();
             $this->view->form = $form;
             //die(var_dump($data));

          }
       }

    }


    /*
     * Form to set options amount for a specific classes survey type form.
     * Supervisor eval also uses this action.
     */
    public function setStuEvalOptionAmountAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $classId = $coopSess->stuEvalManagementData['classId'];
       $assignId = $coopSess->stuEvalManagementData['assignId'];

       $form = new Application_Form_SurveyOptions(array('surveyName' => 'Student Eval', 'isClass' => true));

       $class = new My_Model_Class();
       // used to display class name in page header.
       $this->view->class = $class->getClass($classId);
       $assign = new My_Model_Assignment();
       // used to display assignment name in page.
       $this->view->assign = $assign->getAssignment($assignId);

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $data['classes_id'] = $classId;
             $data['assignments_id'] = $assignId;

             $result = $assign->setSurveyClassOptionAmount($data);

             if ($result === true) {
                $this->view->resultMessage = "<p class=success> Success </p>";
             } else if ($result === false) {
                $this->view->resultMessage = "<p class=error> Error </p>";
             }

          }

       }

    }
/*************************************END ASSIGNMENT MANAGEMENT**************************/

    public function midtermReportCoordAction()
    {
       
    }


}



