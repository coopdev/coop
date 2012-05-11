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
       $sems = $sem->getUpToCurrent();

       $semester->addMultiOptions(array('' => "--------------"));

       foreach ($sems as $s) {
          $semester->addMultiOptions(array($s['id'] => $s['semester']));
       }

       $class = new Zend_Form_Element_Select('classes_id');
       $class->setLabel("Select class:");

       $cl = new My_Model_Class();
       $classes = $cl->getAll();

       $class->addMultiOptions(array('' => "--------------"));

       foreach ($classes as $c) {
          $class->addMultiOptions(array($c['id'] => $c['name']));
       }

       $coord = new Zend_Form_Element_Select('coordinator');
       $coord->setLabel("Select coordinator:");

       $user = new My_Model_User();
       $coords = $user->getAllCoords();

       $coord->addMultiOptions(array('' => "--------------"));
       
       foreach ($coords as $c) {
          $coord->addMultiOptions(array($c['username'] => $c['lname'].", ".$c['fname']." (".$c['username'].")"));
       }
       //$search->addValidator(new Zend_Validate_Db_RecordExists( array('table'=>'coop_users', 'field'=>'username')));

       //$search = $elems->getSubmit('Search');
       //$search->setDecorators(array('ViewHelper'
       //                            ))
       //       ->setAttrib('id', 'search');
       $search = new Zend_Form_Element_Button('search');
       $search->setLabel('Search')
              ->setAttrib('id', 'search');

      $this->setDecorators( array( 
          array('ViewScript', array('viewScript' => '/user/searchstudent.phtml'))));

       $this->addElements(array($fname, $lname, $username, $semester, $class, $coord, $search));

       $this->setElementDecorators(array('ViewHelper'));
    }


}

