<?php

class Application_Form_StudentInfo extends Application_Form_StudentCommon
{

    public function init()
    {
        // Make common elems
        $this->makeElems();
        
        $elems = new My_FormElement();        
        $enrollDate = $elems->getEnrollDateSelect();
        $classChoice = $elems->getClassChoiceSelect();
        $mobile = $elems->getCommonTbox('mobile', 'Mobile phone:');
        $wantedJob = $elems->getCommonTbox('wanted_job', 'What job do you want for your co-op experience?');
        $creds = $elems->getCreditAmtTbox();
        
        $this->addElements(array($this->fname, $this->lname, $this->uuid, $this->address,
                           $enrollDate, $wantedJob, $classChoice, $creds, 
                           $this->grad, $this->major, $this->semInMaj, $this->phone,
                           $mobile, $this->email, $this->curJob, $this->sdate,
                           $this->edate, $this->payRate, $this->employer, $this->department,
                           $this->jobAddr, $this->supervName, $this->supervTitle, 
                           $this->supervPhone, $this->supervEmail, $this->agree,
                           $this->submit));
        
        //$this->addElements(array($this->fname, $this->lname, $this->agree, $this->submit));
    }


}

