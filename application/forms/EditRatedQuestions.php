<?php

class Application_Form_EditRatedQuestions extends Zend_Form
{
    protected $classId;

    public function init()
    {
       $this->generateQuestions();

       $submit = new Zend_Form_Element_Submit('Submit');
       $this->addElement($submit);
    }



    private function generateQuestions()
    {
       $Assign = new My_Model_Assignment();

       $assignId = $Assign->getStudentEvalId();
       $classId = $this->classId;

       $questions = $Assign->getQuestions($assignId, array('classes_id' => $classId));
       //die('hi');

       $qnum = 0;
       foreach ($questions as $q) {
          $elem = new Zend_Form_Element_Textarea($q['id']);

          $elem->setLabel("Question " . ++$qnum)
               ->setRequired(true)
               ->addFilter('StringTrim')
               ->addFilter('StripTags')
               ->setValue($q['question_text']);
          
          $this->addElement($elem);
               
       }

    }




    public function setClassId($classId)
    {
       $this->classId = $classId;

    }


}

