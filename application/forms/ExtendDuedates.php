<?php

class Application_Form_ExtendDuedates extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $classes = $elems->getClassChoiceSelect();
       $classes->setLabel("Select class:");


       //die(var_dump($firstClass));

       $assigns = $elems->getAssignmentSelect();

       $students = new Zend_Form_Element_Select('username');
       $students->setLabel("Select student:")
                ->setRequired(true)
                ->setRegisterInArrayValidator(false);

       $dueDate = $elems->getDateTbox('due_date', 'Enter due date');
       $dueDate->setRequired(true);


       $class = new My_Model_Class();
       //$stuRecs = $class->getRollForCurrentSem($firstClass);

       //foreach ($stuRecs as $s) {
       //   $username = $s['username'];
       //   $lname = $s['lname'];
       //   $fname = $s['fname'];
       //   $students->addMultiOptions(array($username => "$lname, $fname ($username)"));
       //}

       $submit = $elems->getSubmit();

       $this->addElements(array($classes, $assigns, $students, $dueDate, $submit));

    }


}

