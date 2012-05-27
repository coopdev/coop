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

    public function listAllAction()
    {
       $as = new My_Model_Assignment();

       $assignments = $as->getAll();

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

    public function editQuestionsAction()
    {
       if ($this->getRequest()->isGet()) {
          if (!isset($_GET['id'])) {
             $this->view->result = "<p class=error> Must select an assignment first </p>";
             return;
          }

          $id = $_GET['id'];

          $as = new My_Model_Assignment();

          $questions = $as->getQuestions($id);
          //die(var_dump($questions));
          $form = new Application_Form_EditQuestions();
          foreach ($questions as $q) {

             $qNum = $q['question_number'];

             $subf = new Zend_Form_SubForm();

             $qText = new Zend_Form_Element_Textarea("question_text");
             $qText->setLabel("Question text:")
                   ->setRequired(true)
                   ->addFilter("StringTrim")
                   ->addFilter("StripTags");
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

    public function midtermReportCoordAction()
    {
       
    }


}



