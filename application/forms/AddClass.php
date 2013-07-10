<?php

class Application_Form_AddClass extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $class = $elems->getCommonTbox('name', 'Name:');

       $major = new Zend_Form_Element_Select('major');
       $major->setLabel("Major");
       $Major = new My_Model_Major();
       $majors = $Major->fetchAll(null, "major");

       foreach ($majors as $m) {
          $major->addMultiOptions(array($m->major => $m->major));
       }
       
       $coord = $elems->getCoordsSelectOptional();

       //$level = $this->levelSelect();
       $level = $elems->levelSelect();

       $submit = $elems->getSubmit('Add');

       $this->addElements(array($class, $major, $coord, $level, $submit));
    }
}

