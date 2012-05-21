<?php

class Application_Form_AddClass extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $class = $elems->getCommonTbox('name', 'Name:');

       $coord = $elems->getCoordsSelectOptional();

       $submit = $elems->getSubmit('Add');

       $this->addElements(array($class, $coord, $submit));
    }


}

