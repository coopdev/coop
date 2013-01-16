<?php

class Application_Form_NewUser extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $fileUploadToggle = new Zend_Form_Element_Button("fileUploadToggle");
       $fileUploadToggle->setLabel("Upload File Instead");

       $fileUpload = new Zend_Form_Element_File("fileUpload");

       $fname = $elems->getCommonTbox("fname", "Enter student's first name:");
       $fname->setRequired(false);
       
       $lname = $elems->getCommonTbox("lname", "Enter student's last name:");
       $lname->setRequired(false);

       $username = $elems->getCommonTbox("username", "Enter student's username:");

       $class = $elems->getClassChoiceSelect();
       $class->setLabel("Choose student's class:");

       $semester = $elems->getEnrollDateSelect();
       $semester->setLabel("Choose semester:");
       $sem = new My_Model_Semester();
       $semId = $sem->getCurrentSemId();
       $semester->setValue($semId);

       //$coord = new Zend_Form_Element_Select('coordinator');
       //$coord->setLabel('Choose coordinator:');

       //$user = new My_Model_User();
       //$coords = $user->getAllCoords();

       //foreach ($coords as $c) {
       //   $fname = $c['fname'];
       //   $lname = $c['lname'];
       //   $uname = $c['username'];
       //   $coord->addMultiOptions(array($uname => "$lname, $fname ($uname)"));
       //}
       $submitType = new Zend_Form_Element_Hidden("submitType");
       $submitType->setValue("manual");

       $submit = $elems->getSubmit();

       $this->addElements(array($fileUploadToggle, $fileUpload, $fname, $lname, $username, $class, $semester, $submitType, $submit));
    }


}

