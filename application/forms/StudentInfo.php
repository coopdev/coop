<?php

class Application_Form_StudentInfo extends Application_Form_CommonForm
{

    public function init()
    {
       $this->setDecorators(array(array('ViewScript', 
                                  array('viewScript' => '/form/stuinfo-form-template.phtml'))));
       
       $this->personalInfoFields();
       $this->eduInfoFields();
       $this->empInfoFields();
       
       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');

       $this->addElements( array($saveSubmit, $finalSubmit));
       
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


    public function empInfoFields()
    {
       $empInfoSubform = new Zend_Form_SubForm();
       $empInfoSubform->setElementsBelongTo('empInfo');
       
       
       $elems = new My_FormElement();

       $jobTitle = $elems->getCommonTbox('job_title', "Job Title:");
       
       $coopJobTitle = $elems->getCommonTbox('coop_jobtitle', 'COOP. ED. JOBTITLE');

       $startDate = $elems->getDateTbox('start_date', 'Start Date');

       $endDate = $elems->getDateTbox('end_date', 'End Date:');

       $rateOfPay = $elems->getPayRateTbox();

       $employer = $elems->getCommonTbox('employer', 'Employer:');

       $department = $elems->getCommonTbox('department', 'Department:');
       
       $streetAddress = $elems->getCommonTbox('street_address', 'Street Address:');
       
       $cityStateZip = $elems->getCommonTbox('city_state_zip', 'City, State, Zip:');
       
       $supervName = $elems->getCommonTbox('superv_name', 'Supervisor Name:');

       $supervTitle = $elems->getCommonTbox('superv_title', 'Supervisor Title:');

       $supervPhone = $elems->getCommonTbox('superv_phone', 'Telephone:');
       
       $supervEmail = $elems->getEmailTbox('superv_email', 'E-Mail:');
       
       $fax = $elems->getCommonTbox('fax', 'Fax:');

       $empInfoSubform->addElements( array($jobTitle, $coopJobTitle, $startDate, $endDate,
           $rateOfPay, $employer, $department, $streetAddress, $cityStateZip, $supervName,
           $supervTitle, $supervPhone, $supervEmail, $fax) );

       $this->addSubForm($empInfoSubform, 'empInfo');
    }


}

