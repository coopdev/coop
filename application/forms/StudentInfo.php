<?php

class Application_Form_StudentInfo extends Application_Form_CommonForm
{

    public function init()
    {
       $this->setDecorators(array(array('ViewScript', 
                                  array('viewScript' => '/form/stuinfo-form-template.phtml'))));
       
       $this->personalInfoFields();
       $this->eduInfoFields();
       
       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }


    public function personalInfoFields()
    {
       $persInfoSubform = new Zend_Form_SubForm();
       $persInfoSubform->setElementsBelongTo('personalInfo');

       $elems = new My_FormElement();

       $fname = $elems->getCommonTbox('fname', 'First Name:');
       $lname = $elems->getCommonTbox('lname', 'Last Name:');
       $uuid = $elems->getCommonTbox('uuid', 'Student ID#:');
       $phone = $elems->getCommonTbox('phone', 'Telephone:');
       $mobile = $elems->getCommonTbox('mobile', 'Mobile:');
       $email = $elems->getCommonTbox('email', 'Email:');

       $persInfoSubform->addElements( array($fname, $lname, $uuid, $phone, $mobile, $email) );

       $this->addSubForm($persInfoSubform, 'personalInfo');

    }


    public function eduInfoFields()
    {
       $Class = new My_Model_Class();
       $classRow = $Class->getClass($this->classId);
       
       $eduInfoSubform = new Zend_Form_SubForm();
       $eduInfoSubform->setElementsBelongTo('eduInfo');
       
       $elems = new My_FormElement();

       $major = $elems->getMajorSelect();
       $semInMajor = $elems->getSemesterInMajorRadio();
       $gradDate = $elems->getSemesterDropdown('grad_date', 'Grad Date');
       
       $class = $elems->getCommonTbox('classes_id', 'COOP. ED. COURSE:');
       $class->setValue($classRow['name']);

       $coopCreds = $elems->getCommonTbox('coop_credits', 'NUMBER OF COOP. ED. CREDITS:');
       $totalCreds = $elems->getCommonTbox('total_credits', 'TOTAL NUMBER OF CREDITS THIS SEMESTER:');
       $coopSemYr = $elems->getCommonTbox('coop_sem_yr', 'SEMESTER AND YEAR IN COOP. ED. COURSE:');
       $coopJobTitle = $elems->getCommonTbox('coop_jobtitle', 'COOP. ED. JOBTITLE');
       $otherCourses = $elems->getCommonTarea('other_courses', 'OTHER COURSES ENROLLED IN 
          THIS SEMESTER AT HCC (Include course alpha/number and course reference number(CRN): Fire 111)');
       $otherCourses->setAttrib('style', 'width: 500px; height: 110px;');

       $eduInfoSubform->addElements( array($major, $semInMajor, $gradDate, $class, $coopCreds, 
           $totalCreds, $coopSemYr, $coopJobTitle, $otherCourses) );

       $this->addSubForm($eduInfoSubform, 'eduInfo');

    }


}

