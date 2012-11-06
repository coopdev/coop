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
    protected $wantedJob = null;
    protected $curJob = null;
    protected $department = null;
    protected $sdate = null;
    protected $edate = null;
    protected $payRate = null;
    protected $address = null;
    protected $jobAddr = null;
    protected $grad = null;
    protected $major = null;
    protected $semInMaj= null;
    protected $phone = null;
    protected $email = null;
    protected $supervName= null;
    protected $supervTitle= null;
    protected $supervPhone= null;
    protected $supervEmail= null;
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
       $this->wantedJob = $elems->getCommonTbox('wanted_job','What job do you want for your co-op experience?');
       $this->curJob = $elems->getCommonTbox('current_job', 'Job title:');
       $this->department = $elems->getCommonTbox('department', 'Department:');
       $this->sdate = $elems->getDateTbox('start_date','Start date');
       $this->edate = $elems->getDateTbox('end_date','End date');
       $this->payRate = $elems->getPayRateTbox();
       $this->address = $elems->getCommonTbox('address', 'Address/City/ZIP:');
       $this->jobAddr = $elems->getCommonTbox('job_address', 'Address/City/ZIP:');
       //$this->grad = $elems->getDateTbox('grad_date', 'Graduation date');
       $this->grad = $elems->getSemesterDropdown('grad_date', 'Graduation date');
       $this->major = $elems->getMajorSelect();
       $this->semInMaj = $elems->getSemesterInMajorRadio();
       $this->phone = $elems->getCommonTbox('phone', 'Telephone:');
       $this->email = $elems->getEmailTbox('email','E-mail:');
       $this->employer = $elems->getCommonTbox('employer', 'Employer:');
       $this->supervName = $elems->getCommonTbox('superv_name', 'Supervisor\'s name:');
       $this->supervTitle = $elems->getCommonTbox('superv_title', 'Supervisor\'s title:');
       $this->supervPhone = $elems->getCommonTbox('superv_phone', 'Supervisor\'s telephone:');
       $this->supervEmail = $elems->getEmailTbox('superv_email', 'Supervisor\'s e-mail:');
       $this->agree = $elems->getAgreementRadio('');
       $this->submit = $elems->getSubmit();
               
    }


}

