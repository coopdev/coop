<?php

class AssignmentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
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

    public function midtermReportAction()
    {
       $form = new Application_Form_MidtermReport();

       $this->view->form = $form;

       $req = $this->getRequest();
       if ($req->isPost()) {
          $data = $_POST;
          unset($data['Submit']);

          if ($form->isValid($data)) {

             $as = new My_Model_Assignment();
             $res = $as->submitMidtermReport($data);

             if ($res === "submitted") {
                $this->view->message = "<p class=error> Assignment has already been submitted </p>";
             } else if ($res === false) {
                $this->view->message = "<p class=error> Error occured </p>";
             } else if ($res === true) {
                $this->view->message = "<p class=success> Assignment has been submitted </p>";
             }

          }
       }

    }

    public function submitAction()
    {
       $form = new Application_Form_SubmitAssignment();
       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
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

    public function propertiesAction()
    {
       if ($this->getRequest()->isGet()) {

          if (!isset($_GET['id'])) {
             $this->view->message = "<p class=error> Must select an assignment first </p>";
             return;
          }

          $id = $_GET['id'];

          $as = new My_Model_Assignment();

          $assign = $as->getAssignment($id);
          //die(var_dump($assign));

          $funcs = new My_Funcs();
          $assign['due_date'] = $funcs->formatDateOut($assign['due_date']);

          $this->view->assign = $assign;
       }

    }

    public function editDuedateAction()
    {

       $as = new My_Model_Assignment();

       $assigns = $as->getAll();

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

          if ($form->isValid($data)) {
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

    public function midtermReportCoordAction()
    {
       
    }


}



