<?php

class Application_Form_AddCoord extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $username = $elems->getCommonTbox('username', "Enter coordinator's username:");

       $fname = $elems->getCommonTbox('fname', "Enter coordinator's first name:");

       $lname = $elems->getCommonTbox('lname', "Enter coordinator's last name:");

       $email = $elems->getEmailTbox('email', "Enter coordinator's email:");

       $phone = $elems->getCommonTbox('phonenumber', "Enter coordinator's phone number:");

       $submit = $elems->getSubmit('Add');

       $this->addElements(array($username, $fname, $lname, $email, $phone, $submit));
    }


}

