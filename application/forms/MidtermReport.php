<?php

class Application_Form_MidtermReport extends Zend_Form
{

    public function init()
    {
       $this->setAttrib('class', 'midtermReport');
       $as = new My_Model_Assignment();
       $id = $as->getMidtermId(); // Midterm Report's id.

       $questions = $as->getQuestions($id);

       foreach ($questions as $q) {

          //$elem = "elem".$q['id'];


          $elem = new Zend_Form_Element_Textarea($q['id']);

          // String length validator
          $strLength = new Zend_Validate_StringLength(array('min'=>$q['answer_minlength']));
          $strLength->setMessage("Must be at least %min% characters long", 'stringLengthTooShort');


          $elem->setLabel($q['question_text'])
               ->setRequired(true)
               ->addValidator($strLength);

          $this->addElement($elem);
       }

       $submit = new Zend_Form_Element_Submit("Submit");

       $this->addElement($submit);
    }


}

