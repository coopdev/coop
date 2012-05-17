<?php

class Application_Form_ClassEdit extends Zend_Form
{

    public function init()
    {

       $elems = new My_FormElement();

       $class = $elems->getCommonTbox('name', 'Enter class name:');

       $coord = $elems->getCoordsSelectOptional();

       $id = new Zend_Form_Element_Hidden('id');

       $submit = $elems->getSubmit();

       $this->addElements(array($class, $coord, $id, $submit));

    }


}

