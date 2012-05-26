<?php

class Application_Form_EditDuedate extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();
       $duedate = $elems->getDateTbox('due_date', 'Enter due date:');

       $id = new Zend_Form_Element_Hidden('id');

       $submit = $elems->getSubmit();

       $this->addElements(array($duedate, $id, $submit));
    }


}

