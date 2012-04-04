<?php


/*
 * This class contains form elements that are common between two or more forms 
 * that a student fills out.
 */
class Application_Form_StudentCommon extends Zend_Form
{   
    protected $fname = null;
    protected $lname = null;
    protected $uuid = null;
    protected $employer = null;
    protected $jobTitle = null;
    protected $department = null;
    protected $sdate = null;
    protected $edate = null;
    protected $payRate = null;
    protected $address = null;
    protected $grad = null;
    protected $major = null;
    protected $semInMaj= null;
    protected $phone = null;
    protected $email = null;
    protected $superName= null;
    protected $superTitle= null;
    protected $superPhone= null;
    protected $superEmail= null;
    protected $submit = null;
    protected $agree = null;
    
    /*
     * Creates the common elements
     */
    protected function makeElems()
    {
       $elems = new My_FormElement();
       
       $this->fname = $elems->getCommonTbox('fname','First name:');
       $this->lname = $elems->getCommonTbox('lname','Last name:');
       $this->uuid = $elems->getUuidTbox();
       $this->employer = $elems->getCommonTbox('employer','Employer:');
       $this->jobTitle = $elems->getCommonTbox('jobTitle','Job title:');
       $this->department = $elems->getCommonTbox('department', 'Department:');
       $this->sdate = $elems->getDateTbox('sdate','Start date');
       $this->edate = $elems->getDateTbox('edate','End date');
       $this->payRate = $elems->getPayRateTbox();
       $this->address = $elems->getCommonTbox('jobAddress', 'Address/City/ZIP:');
       $this->grad = $elems->getDateTbox('gradDate', 'Grad date:');
       $this->major = $elems->getMajorSelect();
       $this->semInMaj = $elems->getSemesterInMajorRadio();
       $this->phone = $elems->getCommonTbox('phone', 'Telephone:');
       $this->email = $elems->getEmailTbox('email','E-mail:');
       $this->superName = $elems->getCommonTbox('supervName', 'Supervisor\'s name:');
       $this->superTitle = $elems->getCommonTbox('supervTitle', 'Supervisor\'s title:');
       $this->superPhone = $elems->getCommonTbox('supervPhone', 'Supervisor\'s telephone:');
       $this->superEmail = $elems->getEmailTbox('supervEmail', 'Supervisor\'s e-mail:');
       $this->agree = $elems->getAgreementRadio();
       $this->submit = $elems->getSubmit();
               
    }


}

