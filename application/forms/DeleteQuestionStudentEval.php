<?php

class Application_Form_DeleteQuestionStudentEval extends Zend_Form
{

    public function init()
    {
       $this->setAttrib('id', 'deleteQuestionStudentEval');
       $cb = new Zend_Form_Element_MultiCheckbox('questions');

       $cb->setRequired(true);

       $aq = new My_Model_AssignmentQuestions();
       $questions = $aq->getChildParentQuestions();
       //die('hi');

       foreach ($questions as $q) {
          if ($q['question_type'] === 'parent' && $q['question_number'] === '1') {

          } else {
             $cb->addMultiOption($q['id'], $q['question_text']);
          }
       }

       $elems = new My_FormElement();
       $submit = $elems->getSubmit();
       
       $this->addElements(array($cb, $submit));
    }


}

