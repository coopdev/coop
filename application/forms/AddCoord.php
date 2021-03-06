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
       $email->setRequired(false);

       $phone = $elems->getCommonTbox('home_phone', "Enter coordinator's phone number:");
       $phone->setRequired(false);

       $fax = $elems->getCommonTbox('fax', "Enter coordinator's fax:");
       $fax->setRequired(false);
       
       $submit = $elems->getSubmit('Add');

       $this->addElements(array($username, $fname, $lname, $email, $phone, $fax, $submit));
    }


}

