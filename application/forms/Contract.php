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
        
        $elems = new My_FormElement();
        $fname = $elems->getNameTbox('fname','First name:');
        $lname = $elems->getNameTbox('lname','Last name:');
        $sdate = $elems->getStartDateTbox();
        $uuid = $elems->getUuidTbox();
        $semInMaj = $elems->getSemesterInMajorRadio();
        $this->addElements(array($fname, $lname, $uuid, $semInMaj, 
                               $sdate, $agree, $semester, $submit));


    }


}

