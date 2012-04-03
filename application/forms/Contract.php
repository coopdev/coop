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
         
         $coordName = $elems->getCommonTbox('coordName', 'Co-op coordinator\'s name:');
         $coordPhone = $elems->getCommonTbox('coordPhone', 'Co-op coordinator\'s telephone:');
         $enrollDate = $elems->getEnrollDateSelect();
                  
         // Add elements. TAKE OUT $enrollDate BECAUSE IT DOESN'T BELONG IN THIS
         // FORM. IT IS JUST BEING USED TO TEST THE SEMESTER RANGE.
         $this->addElements(array($this->fname, $this->lname, $this->uuid, $this->employer,  
                                 $this->jobTitle, $this->department, $this->sdate, 
                                 $this->edate, $this->payRate, $this->address,
                                 $this->grad, $this->major, $this->semInMaj, 
                                 $this->phone, $this->email, $coordName, $coordPhone, 
                                 $this->superName, $this->superTitle, $this->superEmail,
                                 $this->superPhone, $enrollDate, $this->agree, $semester, $this->submit));
      }
   
   
      
//    public function init()
//    {
//        $this->setName('contract');
//        //$this->setMethod('POST');
//        //$this->setAction('/acl/public/user/create');
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
//        $superTitle = $elems->getCommonTbox('supervTitle', 'Supervisor\'s title:');
//        $superName = $elems->getCommonTbox('supervName', 'Supervisor\'s name:');
//        $superEmail = $elems->getEmailTbox('supervEmail', 'Supervisor\'s e-mail:');
//        $agree = $elems->getAgreementRadio();
//        $submit = $elems->getSubmit();
//               
//        $this->addElements(array($fname, $lname, $uuid, $employer,  
//                                 $jobTitle, $department, $sdate, $edate, $payRate, 
//                                 $addr, $grad, $major, $semInMaj, $phone, $email,
//                                 $coordName, $coordPhone, $superTitle, $superName,
//                                 $superEmail, $agree, $semester, $submit,
//                                 ));
//
//
//    }
}

