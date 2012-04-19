<?php

class Application_Form_NewUser extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $username = $elems->getCommonTbox("username", "Enter student's username:");

       $class = $elems->getClassChoiceSelect();
       $class->setLabel("Choose student's class:");

       $semester = $elems->getEnrollDateSelect();
       $semester->setLabel("Choose semester:");

       $submit = $elems->getSubmit();

       $this->addElements(array($username, $class, $semester, $submit));
    }


}

