<?php

class Application_Form_AddClass extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $class = $elems->getCommonTbox('name', 'Name:');

       $coord = $elems->getCoordsSelectOptional();

       $level = $this->levelSelect();

       $submit = $elems->getSubmit('Add');

       $this->addElements(array($class, $coord, $level, $submit));
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

