<?php

class Application_Form_EditDuedate extends Zend_Form
{

    //public function init()
    //{
    //   $elems = new My_FormElement();
    //   $duedate = $elems->getDateTbox('due_date', 'Enter due date:');

    //   $id = new Zend_Form_Element_Hidden('id');

    //   $submit = $elems->getSubmit();

    //   $this->addElements(array($duedate, $id, $submit));
    //}

    /*
     * Creates the form to edit assignments' due dates. Takes the assignments as a parameter 
     * since there can be a variable amount of assignments whose due dates need editing.
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

