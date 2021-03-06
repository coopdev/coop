<?php

class Application_Form_StudentInfo extends Application_Form_CommonForm
{
    // An array of student info sheets that have been submitted.
    public $submissions = array();

    public $submissionType = 'new';

    public $empInfoId;

    public function init()
    {
       $Assign = new My_Model_Assignment();
       $this->assignId = $Assign->getStuInfoId();
       
       $this->setDecorators(array(array('ViewScript', 
                                  array('viewScript' => '/form/stuinfo-form-template.phtml'))));
       
       $this->personalInfoFields();
       $this->eduInfoFields();
       $this->empInfoFields();
       
       //$empInfoId = new Zend_Form_Element_Hidden('empInfoId');
       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'submit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'submit');

       $this->addElements( array($saveSubmit, $finalSubmit));

       
       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }

    /* 
     * This will create a cloned form for each submitted student info sheet and store
     * it in $this->submissions.
     * 
     * @param $status Takes the value that should be used for is_final in coop_employmentinfo.
     * 
     */
    public function setSubmissions($status='0')
    {
       $Class = new My_Model_Class();
       $User = new My_Model_User();

       $userRow = $User->getRow( array('username' => $this->username) );
       foreach ($userRow as $key => $val) {
          $userRow[$key] = $val;
       }
       //$dbExpr = new Zend_Db_Expr("AES_DECRYPT(uuid, 'alqpwoifjch') AS uuid");
       //$sel = $User->select()->from($User, array($dbExpr))
       //                         ->where("username = '" . $this->username . "'");
       //$uuid = $User->fetchRow($sel);
       //die(var_dump($uuid->uuid));

       $stuInfoRow = $User->getStudentInfo( array('username' => $this->username, 
                                                  'semesters_id' => $this->semId) );
       foreach ($stuInfoRow as $key => $val) {
          $stuInfoRow[$key] = $val;
       }
       
       $empInfoRows = $User->getEmpInfo( array('username' => $this->username, 
           'classes_id' => $this->classId,
           'semesters_id' => $this->semId,
           'is_final' => $status) );

       $this->submissions = array();
       foreach ($empInfoRows as $row) {
          foreach ($row as $key => $val) {
             $row[$key] = $val;
          }
          //$row['start_date'] = date('m/d/Y', strtotime($row['start_date']));
          //$row['end_date'] = date('m/d/Y', strtotime($row['end_date']));
          
          $this->setAttrib('empinfoid', $row['id']);

          $this->personalInfo->populate($userRow);
          $this->eduInfo->populate($stuInfoRow->toArray());
          $classRow = $Class->getClass($this->classId);
          //die($form->eduInfo->classes_id->getValue());
          $this->eduInfo->classes_id->setValue($classRow['name']);
          $this->empInfo->populate($row);
          $this->empInfo->getElement('empInfoId')->setValue($row['id']);
          $this->submissions[$this->getAttrib('empinfoid')] = clone $this; 
          
       }

       //die(var_dump(count($this->submissions)));

    }


    public function personalInfoFields()
    {
       $User = new My_Model_User();

       $userRow = $User->getRow( array('username' => $this->username) );

       $persInfoSubform = new Zend_Form_SubForm();
       $persInfoSubform->setElementsBelongTo('personalInfo');

       $elems = new My_FormElement();

       $fname = $elems->getCommonTbox('fname', 'First Name:');
       $lname = $elems->getCommonTbox('lname', 'Last Name:');
       $uuid = $elems->getCommonTbox('uuid', 'Student ID#:');
       $phone = $elems->getCommonTbox('home_phone', 'Telephone:');
       $mobile = $elems->getCommonTbox('mobile_phone', 'Mobile:');
       $email = $elems->getCommonTbox('email', 'Email:');

       $persInfoSubform->addElements( array($fname, $lname, $uuid, $phone, $mobile, $email) );
       foreach ($persInfoSubform->getElements() as $e) {
          $e->setRequired(false);
       }
       //die(var_dump($persInfoSubform->getElements()));
       $persInfoSubform->populate($userRow);

       $this->addSubForm($persInfoSubform, 'personalInfo');

    }


    public function eduInfoFields()
    {
       $User = new My_Model_User();

       $stuInfoRow = $User->getStudentInfo( array('username' => $this->username, 
                                                  'semesters_id' => $this->semId) );
       
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
       $otherCourses = $elems->getCommonTarea('other_courses', 'OTHER COURSES ENROLLED IN 
          THIS SEMESTER AT HCC (Include course alpha/number and course reference number(CRN): Fire 111)');
       $otherCourses->setAttrib('style', 'width: 500px; height: 110px;');

       $eduInfoSubform->addElements( array($major, $semInMajor, $gradDate, $class, $coopCreds, 
           $totalCreds, $coopSemYr, $otherCourses) );
       foreach ($eduInfoSubform->getElements() as $e) {
          $e->setRequired(false);
       }

       $eduInfoSubform->populate($stuInfoRow->toArray());

       $this->addSubForm($eduInfoSubform, 'eduInfo');

    }


    public function empInfoFields()
    {
       $empInfoSubform = new Zend_Form_SubForm();
       $empInfoSubform->setElementsBelongTo('empInfo');
       $empInfoId = new Zend_Form_Element_Hidden('empInfoId');
       
       $elems = new My_FormElement();

       $jobTitle = $elems->getCommonTbox('job_title', "Job Title:");
       $coopJobTitle = $elems->getCommonTbox('coop_jobtitle', 'COOP. ED. JOBTITLE');
       $startDate = $elems->getCommonTbox('start_date', 'Start Date');
       $endDate = $elems->getCommonTbox('end_date', 'End Date:');
       $rateOfPay = $elems->getPayRateTbox();
       $employer = $elems->getCommonTbox('employer', 'Employer:');
       $department = $elems->getCommonTbox('department', 'Department:');
       $streetAddress = $elems->getCommonTbox('street_address', 'Street Address:');
       $cityStateZip = $elems->getCommonTbox('city_state_zip', 'City, State, Zip:');
       $supervName = $elems->getCommonTbox('superv_name', 'Supervisor Name:');
       $supervTitle = $elems->getCommonTbox('superv_title', 'Supervisor Title:');
       $supervPhone = $elems->getCommonTbox('superv_phone', 'Telephone:');
       $supervEmail = $elems->getEmailTbox('superv_email', 'E-Mail:');
       $supervEmail->setRequired(false);
       $fax = $elems->getCommonTbox('fax', 'Fax:');
       $fax->setRequired(false);

       $empInfoSubform->addElements( array($empInfoId, $jobTitle, $coopJobTitle, $startDate, $endDate,
           $rateOfPay, $employer, $department, $streetAddress, $cityStateZip, $supervName,
           $supervTitle, $supervPhone, $supervEmail, $fax) );

       foreach ($empInfoSubform->getElements() as $e) {
          $e->setRequired(false);
       }

       $this->addSubForm($empInfoSubform, 'empInfo');
    }

    public function setSubmissionTypeToUpdate()
    {
       $this->submissionType = 'update';
    }
    
    public function setSubmissionTypeToNew()
    {
       $this->submissionType = 'new';
    }

    public function setSubmissionTypeToResubmit()
    {
       $this->submissionType = 'resubmit';
    }


}

