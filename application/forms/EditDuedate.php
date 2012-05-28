<?php

class Application_Form_EditDuedate extends Zend_Form
{

    /*
     * Creates the form to edit all assignment due dates. Has a separate text box for each assignment.
     * Takes the rows of assigments as a parameter since there can be a variable amount of 
     * assignments whose due dates need editing.
     * 
     * @param $data All assignment records
     */
    public function __construct($data, $options = null) {
       parent::__construct($options);

       $elems = new My_FormElement();

       $funcs = new My_Funcs();

       foreach ($data as $d) {

          $subf = new Zend_Form_SubForm();

          $elem = $elems->getDateTbox('due_date', $d['assignment']);

          $subf->addElement($elem);

          $d['due_date'] = $funcs->formatDateOut($d['due_date']);

          $subf->populate($d);

          $this->addSubForm($subf, $d['id']);
       }

       $submit = $elems->getSubmit();
       $this->addElement($submit);

    }


}

