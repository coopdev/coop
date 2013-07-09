<?php

class Application_Form_ClassEdit extends Zend_Form
{

    public function init()
    {

       $elems = new My_FormElement();

       $class = $elems->getCommonTbox('name', 'Enter class name:');

       $major = new Zend_Form_Element_Select('major');
       $major->setLabel("Major");
       $Major = new My_Model_Major();
       $majors = $Major->fetchAll(null, "major");

       foreach ($majors as $m) {
          $major->addMultiOptions(array($m->major => $m->major));
       }

       $coord = $elems->getCoordsSelectOptional();

       $level = $this->levelSelect();
       
       $id = new Zend_Form_Element_Hidden('id');

       $submit = $elems->getSubmit();

       $this->addElements(array($class, $major, $coord, $level, $id, $submit));

    }

    private function levelSelect()
    {
       $elem = new Zend_Form_Element_Select('level');

       $elem->setLabel("Type")
            ->addMultiOptions(array('lower' => 'Non-Transferable',
                                    'upper' => 'Transferable'));

       return $elem;

    }
}

