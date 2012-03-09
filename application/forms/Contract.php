<?php

class Application_Form_Contract extends Zend_Form
{

    public function init()
    {
        $this->setName('contract');
        $fname = new Zend_Form_Element_Text('fname');
        $fname->setLabel('Firstname')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim');
              

        $lname = new Zend_Form_Element_Text('lname');
        $lname->setLabel('Lastname')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim');
        
        $agree = new Zend_Form_Element_Radio('agree');
        //$agree->setLabel('Agree');
        
        $agree->setMultiOptions(array('agree' => 'Agree',
                                      'disagree' => 'Disagree'))
               ->setSeparator('')
               ->setRequired(true);
        
        //$disagree = new Zend_Form_Element_Radio('disagree');
        //$disagree->setLabel('Disagree');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $this->addElements(array($fname, $lname, $agree, $submit));


    }


}

