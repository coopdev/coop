<?php

class Application_Form_DeleteStuAid extends Zend_Form
{
    public function init()
    {
       $this->setAttrib('id', 'delete-studentaid');
       $elems = new My_FormElement();

       $stuAid = $elems->getStuAidsSelect();

       $submit = $elems->getSubmit('Delete');

       $this->addElements(array($stuAid, $submit));
    }


}

