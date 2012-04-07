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
        $job = $elems->getCommonTbox('jobTitle', 'What job do you want for your co-op experience?');
        $creds = $elems->getCreditAmtTbox();
        
//        $this->addElements(array($this->fname, $this->lname, $this->uuid, $this->homeAddr,
//                           $enrollDate, $this->jobTitle, $classChoice, $creds, 
//                           $this->grad, $this->major, $this->semInMaj, $this->phone,
//                           $mobile, $this->email, $this->superTitle, $this->sdate,
//                           $this->edate, $this->payRate, $this->employer, $this->department,
//                           $this->jobAddr, $this->superName, $this->superTitle, 
//                           $this->superPhone, $this->superEmail, $this->agree,
//                           $this->submit));
        
        $this->addElements(array($this->fname, $this->lname, $this->agree, $this->submit));
    }


}

