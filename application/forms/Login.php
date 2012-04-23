<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $elems = new My_FormElement();

        $username = $elems->getCommonTbox('username', 'Username:');

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
                 ->setLabel('Password:')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');

        $submit = $elems->getSubmit('Login');

        $this->addElements(array($username, $password, $submit));
    }


}

