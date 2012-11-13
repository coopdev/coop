<?php

class Application_Form_AddRatedQuestion extends Zend_Form
{

    public function init()
    {
        $question = new Zend_Form_Element_Textarea('question_text');
        $question->setLabel('Enter Question:')
                 ->setRequired(true)
                 ->addFilter('StringTrim')
                 ->addFilter('StripTags');

        $submit = new Zend_Form_Element_Submit('Submit');

        //$addQuestion = new Zend_Form_Element_Button('addQuestion');
        //$addQuestion->setLabel("Add Question");

        $this->addElements(array($question, $submit));
    }


}

