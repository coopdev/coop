<?php

class Application_Form_StudentInfo extends Application_Form_StudentCommon
{

    public function init()
    {
        // Make common elements
        $this->makeElems();
        
        $elems = new My_FormElement();        
        $enrollDate = $elems->getEnrollDateSelect();
        $classChoice = $elems->getClassChoiceSelect();
        $mobile = $elems->getCommonTbox('mobile', 'Mobile phone:');
        $wantedJob = $elems->getCommonTbox('wanted_job', 'What job do you want for your co-op experience?');
        $city = $elems->getCommonTbox('city', 'City:');
        $state = $elems->getCommonTbox('state', 'State:');
        $zipcode = $elems->getZipcodeTbox();
        $creds = $elems->getCreditAmtTbox();

        $perInfo = new Zend_Form_Element_Hidden('perInfo');
        $perInfo->setLabel("PERSONAL INFORMATION");
        $perInfo->setDecorators(array('ViewHelper', 
                                      array('Label', array('tag' => 'p', 'style' => 'font-size: 14px;')),
                                      array('HtmlTag', array('tag' => 'br', 'placement' => 'PREPEND'))
                                ));
        $empInfo = new Zend_Form_Element_Hidden('empInfo');
        $empInfo->setLabel("EMPLOYMENT INFORMATION (If you are currently working at a job related to your major please describe below)");
        $empInfo->setDecorators(array('ViewHelper',
                                      array('Label', array('tag' => 'p', 'style' => 'font-size: 14px;border-width:1px;border-style:solid;padding:10px')),
                                      array('HtmlTag', array('tag' => 'br', 'placement' => 'PREPEND'))
                                ));

        $partAgreement = new Zend_Form_Element_Hidden('partAgreement');
        $partAgreement->setLabel("STUDENT PARTICIPATION AGREEMENT");
        $partAgreement->setDecorators(array('ViewHelper',
                                      array('Label', array('tag' => 'p', 'style' => 'font-size: 14px;border-width:1px;border-style:solid;padding:10px')),
                                      array('HtmlTag', array('tag' => 'br', 'placement' => 'PREPEND'))
                                   ));

        //$this->submit->setDecorators(array('ViewHelper',
                                           
            
                                           

        $subf1 = new Zend_Form_SubForm();
        $subf1->setAction('/form/student-info-show');
        $subf1->addElements(array($this->fname, $this->lname, $this->uuid, $this->address,
                           $city, $state, $zipcode, $wantedJob, $creds, $this->grad, $this->major, 
                           $this->semInMaj, $this->phone, $mobile, $this->email)); 

        $subf1->addDisplayGroup(array('fname', 'lname', 'uuid'), 'firstrow');
        $subf1->addDisplayGroup(array('address', 'city', 'state', 'zipcode'), 'secondrow');
        $subf1->addDisplayGroup(array('wanted_job', 'credits', 'grad_date', 'major'), 'thirdrow');
        $subf1->addDisplayGroup(array('semester_in_major', 'phone', 'mobile', 'email'), 'fourthrow');

        $subf1->setElementDecorators(array('ViewHelper',
                                           array('Label', array('tag' => 'br', 'placement' => 'PREPEND')),
                                           'Errors',
                                           array('HtmlTag', array('tag'=>'td'))
                                     ));
        $subf1->setDisplayGroupDecorators(array('FormElements',
                                                array('HtmlTag', array('tag' => 'tr'))
                                          ));

        $subf2 = new Zend_Form_SubForm();
        $subf2->addElements(array(
                              $this->curJob, $this->sdate, $this->edate, $this->payRate, 
                              $this->department, $this->jobAddr, 
                              $this->supervName, $this->supervTitle, $this->supervPhone, 
                              $this->supervEmail
                            ));
        $subf2->addDisplayGroup(array('current_job', 'start_date', 'end_date', 'rate_of_pay'), 'fifthrow');
        $subf2->addDisplayGroup(array('department', 'job_address', 'superv_name', 'superv_title'), 'sixthrow');
        $subf2->addDisplayGroup(array('superv_phone', 'superv_email'), 'seventhrow');
        //$subf2->addDisplayGroup(array('agreement'), 'eighthrow');
        //$subf2->addDisplayGroup(array('Submit'), 'ninthrow');

        $subf2->setElementDecorators(array('ViewHelper',
                                           array('Label', array('tag' => 'br', 'placement' => 'PREPEND')),
                                           'Errors',
                                           array('HtmlTag', array('tag'=>'td'))
                                     ));
        $subf2->setDisplayGroupDecorators(array('FormElements',
                                                array('HtmlTag', array('tag' => 'tr'))
                                         ));

        $this->addElement($perInfo);
        $this->addSubForm($subf1, 'subf1');
        $this->addElement($empInfo);
        $this->addSubForm($subf2, 'subf2');
        $this->addElements(array($partAgreement, $this->agree, $this->submit ));
        $this->setSubFormDecorators(array('FormElements',
                                          array('HtmlTag', array('tag' => 'table', 'class' => 'studentInfo'))
                                   ));

//        $this->addElements(array($this->fname, $this->lname, $this->uuid, $this->address,
//                           $city, $state, $zipcode, $wantedJob, $creds, $this->grad, $this->major, 
//                           $this->semInMaj, $this->phone, $mobile, $this->email, 
//                           $this->curJob, $this->sdate, $this->edate, $this->payRate, 
//                           $this->department, $this->jobAddr, 
//                           $this->supervName, $this->supervTitle, $this->supervPhone, 
//                           $this->supervEmail, $this->agree, $this->submit));


//        $this->addDisplayGroup(array('fname', 'lname', 'uuid'), 'firstrow');
//        $this->addDisplayGroup(array('address', 'city', 'state', 'zipcode'), 'secondrow');
//        $this->addDisplayGroup(array('wanted_job', 'credits', 'grad_date', 'major'), 'thirdrow');
//        $this->addDisplayGroup(array('semester_in_major', 'phone', 'mobile', 'email'), 'fourthrow');
//
//        //$this->addDisplayGroup(array('message'), 'message2');
//        $this->addDisplayGroup(array('cur_job', 'start_date', 'end_date', 'rate_of_pay'), 'fifthrow');
//        $this->addDisplayGroup(array('department', 'job_address', 'superv_name', 'superv_title'), 'sixthrow');
//        $this->addDisplayGroup(array('superv_phone', 'superv_email'), 'seventhrow');
//        $this->addDisplayGroup(array('agreement'), 'eighthrow');
//        $this->addDisplayGroup(array('Submit'), 'ninthrow');
//        //$this->addDisplayGroup(array('message'), 'fifthrow');
//
//        //$this->addDisplayGroups(array('firstrow','secondrow','thirdrow','fourthrow','fifthrow'), 'block1');
//
//
//        $this->setDisplayGroupDecorators(array('FormElements',
//                                               array('HtmlTag', array('tag'=>'tr')))
//                                        );
//
//        $this->setElementDecorators(array('ViewHelper',
//                                       array('Label', array('tag' => 'br', 'placement' => 'PREPEND')),
//                                       array('HtmlTag', array('tag'=>'td')))
//                                    );
//        $this->submit->setDecorators(array('ViewHelper',
//                                          array('HtmlTag', array('tag' => 'td')),
//                                          ));
//
//        //$message->setDecorators(array('ViewHelper',
//        //                              'Label',
//        //                              array('HtmlTag', 
//        //                                 array('tag'=>'br', 'placement'=>'APPEND')
//        //                              )));
//
////        $message->setDecorators(array('ViewHelper',
////                                      array('HtmlTag', 
////                                         array('tag'=>'tr')
////                                      )));
//
//        $this->addElement($message);
//        $message->setDecorators(array('ViewHelper',
//                                      array('Label', array('tag' => 'td', 'style' => 'width:100%;', 'id' => 'empInfo')),
//                                      //array('HtmlTag', array('tag' => 'td')),
//                                      array('HtmlTag', array('tag' => 'tr'))
//                               ));
//
//        $this->setDecorators(array('FormElements',
//                                   array('HtmlTag', array('tag'=>'table', 'id' => 'studentinfo')),
//                                   'Form'));


        
        //$this->addElements(array($this->fname, $this->lname, $enrollDate,
        //                         $this->agree, $this->submit));
    }


}

