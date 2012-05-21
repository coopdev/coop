<?php

class Application_Form_ClassDelete extends Zend_Form
{

    public function init()
    {
       $this->setAttrib('id', 'classDelete');

       $elems = new My_FormElement();

       $class = $elems->getClassChoiceSelect();
       $class->setLabel('Select class:');

       $submit = $elems->getSubmit('Delete');

       $this->addElements(array($class, $submit));
    }


}

