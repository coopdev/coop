<?php

class Application_Form_Comment extends Zend_Form
{

    public function init()
    {
        $subForm = new Zend_Form_SubForm('comment');
        $subForm->setElementsBelongTo('comment');

        $student = new Zend_Form_Element_Select('username');
        $student->setAttrib('size', 10);
        $student->setRequired(true);

        $comment = new Zend_Form_Element_Textarea('comment');
        $comment->setRequired(true);
        
        $subForm->addElements( array($student, $comment) );
        
        $subForm->setElementDecorators(array('ViewHelper', 'Errors'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel("Submit");

        $this->addElement($submit);
        $this->addSubForm($subForm, 'comment');

        $this->setDecorators(array('ViewHelper', 'Errors'));
        
    }


}

