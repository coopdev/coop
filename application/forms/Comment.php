<?php

class Application_Form_Comment extends Zend_Form
{

    public function init()
    {
        $student = new Zend_Form_Element_Select('student');
        $student->setAttrib('size', 6);
        $student->setRequired(true)
                ->setRegisterInArrayValidator(false);

        $comment = new Zend_Form_Element_Textarea('comment');
        $comment->setRequired(true);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel("Create comment");

        $this->addElements(array($student, $comment, $submit));
        $this->setElementDecorators(array('ViewHelper', 'Errors'));
        
    }


}

