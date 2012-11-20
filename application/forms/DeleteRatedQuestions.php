<?php

class Application_Form_DeleteRatedQuestions extends Zend_Form
{
    protected $classId;
    public $formName;

    public function init()
    {
       //$this->setDecorators(array(array('ViewScript', 
       //                           array('viewScript' => '/assignment/forms/delete-rated-questions.phtml'))));
       $this->formName();
       
       $this->generateQuestions();

       $submit = new Zend_Form_Element_Submit('Submit');
       $this->addElement($submit);
       
       //$this->setElementDecorators(array('ViewHelper',
       //                                 'Errors'
       //                           ));
    }


    private function generateQuestions()
    {
       $Assign = new My_Model_Assignment();

       $assignId = $Assign->getStudentEvalId();
       $classId = $this->classId;

       $questions = $Assign->getQuestions($assignId, array('classes_id' => $classId));

       $checkbox = new Zend_Form_Element_MultiCheckbox('questions');
       $qnum = 0;
       foreach ($questions as $q) {
          $checkbox->addMultiOption($q['id'], ++$qnum . '. ' . $q['question_text']);
       }
       
       $this->addElement($checkbox);

    }


    private function formName()
    {
       $this->formName = "Delete Questions For ";

       $Class = new My_Model_Class();
       $classRow = $Class->getClass($this->classId);
       $this->formName .= $classRow['name'];

    }

    public function setClassId($classId)
    {
       $this->classId = $classId;

    }
}

