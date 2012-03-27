<?php

class Application_Form_Contract extends Zend_Form
{

    public function init()
    {
        $this->setName('contract');
        //$this->setMethod('POST');
        //$this->setAction('/acl/public/user/create');
        $semester = new Zend_Form_Element_Hidden('semester');
        $curSem = new My_Semester();
        $curSem = $curSem->getCurrentSem();
        $semester->setValue($curSem);
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
        
        $agree = new Zend_Form_Element_Radio('agreement');
        //$agree->setLabel('Agree');
        
        $agree->setMultiOptions(array('agree' => 'Agree',
                                      'disagree' => 'Disagree'))
               ->setSeparator('')
               ->setRequired(true);
        
        //$disagree = new Zend_Form_Element_Radio('disagree');
        //$disagree->setLabel('Disagree');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $this->addElements(array($fname, $lname, $agree, $semester, $submit));


    }


}

