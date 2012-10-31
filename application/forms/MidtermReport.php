<?php

class Application_Form_MidtermReport extends Zend_Form
{

    public function init()
    {
       $this->setAttrib('class', 'midtermReport');
       $as = new My_Model_Assignment();
       $id = $as->getMidtermId(); // Midterm Report's id.

       $questions = $as->getQuestions($id);

       // TEMPLATE
       $this->setDecorators( array( 
           array('ViewScript', array('viewScript' => '/assignment/forms/midterm-report.phtml'))));

       foreach ($questions as $q) {

          //$elem = "elem".$q['id'];


          $elem = new Zend_Form_Element_Textarea($q['id']);

          // String length validator
          $strLength = new Zend_Validate_StringLength(array('min'=>$q['answer_minlength']));
          $strLength->setMessage("Must be at least %min% characters long", 'stringLengthTooShort');


          $elem->setLabel($q['question_text'])
               ->setRequired(true)
               ->addValidator($strLength)
               ->setAttrib('class', 'answerText');

          $validator = $elem->getValidator('StringLength');
          $min = $validator->getMin();
          //die(var_dump($min));


          $this->addElement($elem);
       }

       $submit = new Zend_Form_Element_Submit("Submit");

       $this->addElement($submit);


       // CLEAR DECORATORS FOR TEMPLATE
       $this->setElementDecorators(array('ViewHelper',
                                         "Errors"));
    }


}

