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
          $id = $_GET['id'];

          $as = new My_Model_Assignment();

          $assign = $as->getAssignment($id);
          //die(var_dump($assign));

          $this->view->assign = $assign;
       }

    }

    public function editDuedateAction()
    {
       $form = new Application_Form_EditDuedate();
       $this->view->form = $form;
       $as = new My_Model_Assignment();

       if ($this->getRequest()->isGet()) {

          $id = $_GET['id'];
          $assign = $as->getAssignment($id);
          $funcs = new My_Funcs();
          $assign['due_date'] = $funcs->formatDateOut($assign['due_date']);
          $form->populate($assign);

       } else if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $as->edit($data);
          }
       }

    }

    public function editQuestionsAction()
    {
       if ($this->getRequest()->isGet()) {
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
       }
    }

    public function midtermReportCoordAction()
    {
       
    }


}



