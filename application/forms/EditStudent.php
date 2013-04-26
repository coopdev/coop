<?php

class Application_Form_EditStudent extends Zend_Form
{
    private $username;

    public function init()
    {
       $elems = new My_FormElement();

       $fname = $elems->getCommonTbox("fname", "First name:");
       $fname->setRequired(false);
       
       $lname = $elems->getCommonTbox("lname", "Last name:");
       $lname->setRequired(false);

       $username = new Zend_Form_Element_Hidden('username');
       $username->setValue($this->username);
       
       $submit = new Zend_Form_Element_Submit('submit');
       
       $this->addElements( array($fname, $lname, $username, $submit) );
       $this->fillOut();
    }


    public function setUsername($username)
    {
       $this->username = $username;
    }

    // Populates the form.
    private function fillOut()
    {
       $User = new My_Model_User();
       $username = $User->getAdapter()->quote($this->username);
       $user = $User->fetchRow("username = $username");

       $this->populate($user->toArray());

    }
}

