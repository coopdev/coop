<?php

class Application_Form_SubmitAssignment extends Zend_Form
{

    public function init()
    {
       $req = true;

       $classes = new Zend_Form_Element_Select('classes_id');
       $classes->setLabel("Select class:")
               ->setRequired($req);

       $class = new My_Model_Class();

       $rows = $class->getAll();

       $firstClass = $rows[0]['id'];
       //$firstClass = 2;
       foreach ($rows as $row) {
          $classes->addMultiOptions(array($row['id'] => $row['name']));
       }

       $assignments = new Zend_Form_Element_Select('assignments_id');
       $assignments->setLabel("Select assignment:")
             ->setRequired($req);

       $as = new My_Model_Assignment();
       $rows = $as->getAll();
       //$rows = $as->getOffLine();
       //die(var_dump($rows));

       foreach ($rows as $row) {
          $assignments->addMultiOptions(array($row['id'] => $row['assignment']));
       }

       $students = new Zend_Form_Element_Select('username');
       $students->setLabel("Select student:")
                ->setRequired($req)
                ->setRegisterInArrayValidator(false); 

       $showIncompletes = new Zend_Form_Element_Checkbox('showIncompletes');
       $showIncompletes->setLabel("Check to show students with incomplete status.");


       // Semester dropdown if needed.
       //$elems = new My_FormElement();
       //$semesters = $elems->getSemesterDropdown();

       $sem = new My_Model_Semester();
       $sems = $sem->getUpToCurrent(10);
       $semesters = new Zend_Form_Element_Select('semesters_id');
       $semesters->setLabel("Select semester");

       foreach ($sems as $s) {
          $semesters->addMultiOptions(array($s['id'] => $s['semester']));
       }


       $submit = new Zend_Form_Element_Submit('submit');
       $submit->setLabel('Submit');
       

       // Use this to specify a script to use as the template for the form. Must come before
       // elements are added to the form.
       //$this->setDecorators(array( array( 'ViewScript', array( 'viewScript' => 'assignment/submit.phtml'))));

       $this->addElements(array($classes, $assignments, $students, $showIncompletes, $semesters, $submit));

       // Use this when specifying a template to get rid of default decorators. Must come
       // after adding elements to form
       //$this->setElementDecorators(array('ViewHelper'));
             
    }


}

