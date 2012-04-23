<?php

class Application_Form_Disclaimer extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $agree = $elems->getAgreementRadio('');

       $submit = $elems->getSubmit();

       $this->addElements(array($agree, $submit));
    }


}

