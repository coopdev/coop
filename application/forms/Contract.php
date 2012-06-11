<?php

/*
 * Creates the Cooperative Education Agreement form.
 */
class Application_Form_Contract extends Application_Form_StudentCommon
{
   
      public function init()
      {  
         $this->setName('contract')
              ->setAction('/form/coop-agreement-pdf');

         
         // Create common elements
         $this->makeElems();
                 
         // Create elements unique to this form.
         $elems = new My_FormElement();
         $semester = new Zend_Form_Element_Hidden('semester');
         $curSem = new My_Semester();
         $curSem = $curSem->getRealSem();
         $semester->setValue($curSem);
         
         $coordPhone = $elems->getCommonTbox('coord_phone', 'Co-op coordinator\'s telephone:');

         $studentAgree = $elems->getAgreementRadio("Student's agreement", 'student_coopagreement');
         $supervAgree = $elems->getAgreementRadio("Supervisor's agreement", 'superv_coopagreement');

         $address = $elems->getCommonTarea('address', 'Address/City/State/ZIP:');
         $address->setAttrib('rows', '1')
                  ->setAttrib('class', 'textbox')
                  ->setAttrib('cols', '45');

         $employer = $elems->getCommonTarea('employer', 'Enter employer\'s name:');
         $employer->setAttrib('rows', '1')
                  ->setAttrib('cols', '45')
                  ->setAttrib('class', 'textbox');

         $curJob = $elems->getCommonTarea('current_job', 'Job title:');
         $curJob->setAttrib('rows', '1')
                  ->setAttrib('class', 'textbox')
                  ->setAttrib('cols', '45');

         $department = $elems->getCommonTarea('department', 'Department:');
         $department->setAttrib('rows', '1')
                    ->setAttrib('class', 'textbox')
                    ->setAttrib('cols', '45');

         $coordName = $elems->getCommonTarea('coord_name', 'Co-op coordinator\'s name:');
         $coordName->setAttrib('rows', '1')
                    ->setAttrib('class', 'textbox')
                    ->setAttrib('cols', '45');

         $supervTitle = $elems->getCommonTarea('superv_title', 'Supervisor title:');
         $supervTitle->setAttrib('rows', '1')
                    ->setAttrib('class', 'textbox')
                    ->setAttrib('cols', '45');

         $supervName = $elems->getCommonTarea('superv_name', 'Supervisor name:');
         $supervName->setAttrib('rows', '1')
                    ->setAttrib('class', 'textbox')
                    ->setAttrib('cols', '45');

         $this->submit->setLabel('Print PDF');
         $this->address->setAttrib('cols', '80');
         
                  
         $this->setDecorators(array(array('ViewScript', 
                                      array('viewScript' => '/form/coop-agreement-template.phtml'))));
         // Add elements. 
         $this->addElements(array($this->fname, $this->lname, $this->uuid, $employer,  
                                 $curJob, $department, $this->sdate, 
                                 $this->edate, $this->payRate, $address,
                                 $this->grad, $this->major, $this->semInMaj, 
                                 $this->phone, $this->email, $coordName, $coordPhone, 
                                 $supervName, $supervTitle, $this->supervEmail,
                                 $this->supervPhone, $studentAgree, $supervAgree, $semester, $this->submit));
         

         $this->setElementDecorators(array('ViewHelper',
                                           'Errors'
                                    ));
         //$this->addElements(array($this->fname, $this->agree, $this->submit));
      }
   
   
      
//    public function init()
//    {
//        $this->setName('contract');
//        //$this->setMethod('POST');
//        //$this->setAction('/coop/public/user/create');
//        $semester = new Zend_Form_Element_Hidden('semester');
//        $curSem = new My_Semester();
//        $curSem = $curSem->getRealSem();
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

