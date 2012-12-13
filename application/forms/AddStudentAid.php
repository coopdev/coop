<?php

class Application_Form_AddStudentAid extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $username = $elems->getCommonTbox('username', 'Enter username:');

       $fname = $elems->getCommonTbox('fname', 'Enter first name:');

       $lname = $elems->getCommonTbox('lname', 'Enter last name:');

       $email = $elems->getEmailTbox('email', "Enter email:");
       $email->setRequired(false);

       $phone = $elems->getCommonTbox('home_phone', "Enter phone number:");
       $phone->setRequired(false);

       $submit = $elems->getSubmit();

       $this->addElements(array($username, $fname, $lname, $email, $phone, $submit));

    }


}

