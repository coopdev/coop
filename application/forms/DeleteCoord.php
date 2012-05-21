<?php

class Application_Form_DeleteCoord extends Zend_Form
{

    public function init()
    {
       $this->setAttrib('id', 'delete-coord');
       $elems = new My_FormElement();

       $coord = $elems->getCoordsSelect();

       $submit = $elems->getSubmit('Delete');

       $this->addElements(array($coord, $submit));
    }


}

