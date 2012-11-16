<?php

class Application_Form_ClassEdit extends Zend_Form
{

    public function init()
    {

       $elems = new My_FormElement();

       $class = $elems->getCommonTbox('name', 'Enter class name:');

       $coord = $elems->getCoordsSelectOptional();

       $level = $this->levelSelect();
       
       $id = new Zend_Form_Element_Hidden('id');

       $submit = $elems->getSubmit();

       $this->addElements(array($class, $coord, $level, $id, $submit));

    }

    private function levelSelect()
    {
       $elem = new Zend_Form_Element_Select('level');

       $elem->setLabel("Level")
            ->addMultiOptions(array('lower' => 'lower',
                                    'upper' => 'upper'));

       return $elem;

    }
}

