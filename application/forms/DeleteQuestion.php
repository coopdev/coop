<?php

class Application_Form_DeleteQuestion extends Zend_Form
{

    public function init()
    {

    }

    public function __construct($assignId, $options = null) {
       parent::__construct($options);

       $as = new My_Model_Assignment();

       $quests = $as->getQuestions($assignId);

       //die(var_dump($quests));

      $this->setDecorators( array( 
          array('ViewScript', array('viewScript' => '/assignment/delete-question-template.phtml'))));

       $cb = new Zend_Form_Element_MultiCheckbox('questions');
       $cb->setRequired(true)
          ->addErrorMessage("Must select a question to delete");
       foreach ($quests as $q) {
          $cb->addMultiOption($q['question_number'], "Question ". $q['question_number'] . ": " . $q['question_text']);
               //->setLabel("Question ". $q['question_number'] . ": " . $q['question_text']);
          
       }

       $assignIdHidden = new Zend_Form_Element_Hidden('assignments_id');
       $assignIdHidden->setValue($assignId);

       $elems = new My_FormElement();
       $submit = $elems->getSubmit("Delete");
       $this->addElements(array($cb, $assignIdHidden, $submit));

       $this->setElementDecorators(array('ViewHelper',
                                         'Errors'
                                         //array('Label', array('placement' => 'APPEND'))
                                  ));
    }


}

