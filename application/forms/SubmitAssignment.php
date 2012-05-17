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
       $rows = $as->getOffLine();
       //die(var_dump($rows));

       foreach ($rows as $row) {
          $assignments->addMultiOptions(array($row['id'] => $row['assignment']));
       }

       $students = new Zend_Form_Element_Select('username');
       $students->setLabel("Select student:")
                ->setRequired($req);

       //$user = new My_Model_User(); 
       //$rows = $user->getAllStudents();

       $class = new My_Model_Class();
       $rows = $class->getRollForCurrentSem($firstClass);
       foreach ($rows as $row) {
          $students->addMultiOptions(array($row['username'] => "".$row['lname'].", ".$row['fname']." (".$row['username'].")"));
       }


       $submit = new Zend_Form_Element_Submit('submit');
       $submit->setLabel('Submit');
       

       // Use this to specify a script to use as the template for the form. Must come before
       // elements are added to the form.
       //$this->setDecorators(array( array( 'ViewScript', array( 'viewScript' => 'assignment/submit.phtml'))));

       $this->addElements(array($classes, $assignments, $students, $submit));

       // Use this when specifying a template to get rid of default decorators. Must come
       // after adding elements to form
       //$this->setElementDecorators(array('ViewHelper'));
             
    }


}

