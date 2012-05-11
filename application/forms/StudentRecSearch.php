<?php

class Application_Form_StudentRecSearch extends Zend_Form
{

    public function init()
    {

       $elems = new My_FormElement();

       $fname = $elems->getCommonTbox('fname', "Enter student's first name:");
       $fname->setRequired(false);
             

       $lname = $elems->getCommonTbox('lname', "Enter student's last name:");
       $lname->setRequired(false);

       $username = $elems->getCommonTbox('username', "Enter student's username:");
       $username->setRequired(false);

       $semester = new Zend_Form_Element_Select('semesters_id');
       $semester->setLabel('Select semester:');

       $sem = new My_Model_Semester();
       $sems = $sem->getAll();

       $semester->addMultiOptions(array('blank' => "--------------"));

       foreach ($sems as $s) {
          $semester->addMultiOptions(array($s['id'] => $s['semester']));
          if ($s['current']) {
             break;
          }
       }
       
       //$search->addValidator(new Zend_Validate_Db_RecordExists( array('table'=>'coop_users', 'field'=>'username')));

       $submit = $elems->getSubmit('Search');
       $submit->setDecorators(array('ViewHelper'
                                    ));

       $this->addElements(array($fname, $lname, $username, $semester, $submit));
    }


}

