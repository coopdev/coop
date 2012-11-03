<?php

class Application_Form_LearningOutcomeMgmt extends Zend_Form
{

    public function init()
    {
       $minLen = new Zend_Form_Element_Text('answer_minlength');
       $validator = new Zend_Validate_Int();
       $minLen->addFilter("StringTrim")
              ->addFilter("StripTags")
              ->addValidator($validator)
              ->setAttrib("size", 11)
              ->setLabel("Enter minimum character length for this assignment:");

       $submit = new Zend_Form_Element_Submit('Submit');

       $this->addElements(array($minLen, $submit));
    }


}

