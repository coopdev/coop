<?php

/*
 * Creates the Cooperative Education Agreement form.
 */
class Application_Form_Contract extends Application_Form_StudentCommon
{
   
      public function init()
      {  
         $this->setName('contract');
         
         // Create common elements
         $this->makeElems();
                 
         // Create elements unique to this form.
         $elems = new My_FormElement();
         $semester = new Zend_Form_Element_Hidden('semester');
         $curSem = new My_Semester();
         $curSem = $curSem->getCurrentSem();
         $semester->setValue($curSem);
         
         $coordName = $elems->getCommonTbox('coord_name', 'Co-op coordinator\'s name:');
         $coordPhone = $elems->getCommonTbox('coord_phone', 'Co-op coordinator\'s telephone:');

         $studentAgree = $elems->getAgreementRadio("Student's agreement", 'student_coopagreement');
         $supervAgree = $elems->getAgreementRadio("Supervisor's agreement", 'superv_coopagreement');
         
                  
         // Add elements. 
         $this->addElements(array($this->fname, $this->lname, $this->uuid, $this->employer,  
                                 $this->curJob, $this->department, $this->sdate, 
                                 $this->edate, $this->payRate, $this->address,
                                 $this->grad, $this->major, $this->semInMaj, 
                                 $this->phone, $this->email, $coordName, $coordPhone, 
                                 $this->supervName, $this->supervTitle, $this->supervEmail,
                                 $this->supervPhone, $studentAgree, $supervAgree, $semester, $this->submit));
         
         //$this->addElements(array($this->fname, $this->agree, $this->submit));
      }
   
   
      
//    public function init()
//    {
//        $this->setName('contract');
//        //$this->setMethod('POST');
//        //$this->setAction('/coop/public/user/create');
//        $semester = new Zend_Form_Element_Hidden('semester');
//        $curSem = new My_Semester();
//        $curSem = $curSem->getCurrentSem();
//        $semester->setValue($curSem);
//        
//        $elems = new My_FormElement();
//        $fname = $elems->getCommonTbox('fname','First name:');
//        
//        $lname = $elems->getCommonTbox('lname','Last name:');
//        $uuid = $elems->getUuidTbox();
//        $employer = $elems->getCommonTbox('employer','Employer:');
//        $jobTitle = $elems->getCommonTbox('jobTitle','Job title:');
//        $department = $elems->getCommonTbox('department', 'Department:');
//        $sdate = $elems->getDateTbox('sdate','Start date');
//        $edate = $elems->getDateTbox('edate','End date');
//        $payRate = $elems->getPayRateTbox();
//        $addr = $elems->getCommonTbox('jobAddress', 'Address/City/ZIP:');
//        $grad = $elems->getCommonTbox('gradDate', 'Grad date:');
//        $major = $elems->getMajorSelect();
//        $semInMaj = $elems->getSemesterInMajorRadio();
//        $phone = $elems->getCommonTbox('phone', 'Telephone:');
//        $email = $elems->getEmailTbox('email','E-mail:');
//        $coordName = $elems->getCommonTbox('coordName', 'Co-op coordinator\'s name:');
//        $coordPhone = $elems->getCommonTbox('coordPhone', 'Co-op coordinator\'s telephone:');
// 
//        $supervTitle = $elems->getCommonTbox('supervTitle', 'Supervisor\'s title:');
//        $supervName = $elems->getCommonTbox('supervName', 'Supervisor\'s name:');
//        $supervEmail = $elems->getEmailTbox('supervEmail', 'Supervisor\'s e-mail:');
//        $agree = $elems->getAgreementRadio();
//        $submit = $elems->getSubmit();
//               
//        $this->addElements(array($fname, $lname, $uuid, $employer,  
//                                 $jobTitle, $department, $sdate, $edate, $payRate, 
//                                 $addr, $grad, $major, $semInMaj, $phone, $email,
//                                 $coordName, $coordPhone, $supervTitle, $supervName,
//                                 $supervEmail, $agree, $semester, $submit,
//                                 ));
//
//
//    }
}

