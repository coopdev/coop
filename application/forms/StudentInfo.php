<?php

class Application_Form_StudentInfo extends Application_Form_StudentCommon
{

    public function init()
    {
        // Make common elements
        $this->makeElems();
        $this->setAttrib('id', 'studentInfoSheet')
             ->setAttrib('name', 'studentInfoSheet');
        
        $elems = new My_FormElement();        
        
        $enrollDate = $elems->getEnrollDateSelect();
        $classChoice = $elems->getClassChoiceSelect();
        $mobile = $elems->getCommonTbox('mobile', 'Mobile phone:');
        $wantedJob = $elems->getCommonTbox('wanted_job', 'What job do you want for your co-op experience?');
        $coopClass = $elems->getCommonTbox('wanted_class', 'Which co-op class are you planning to enroll in?');
        $coopClass->setAttrib('disabled', true);
        $city = $elems->getCommonTbox('city', 'City:');
        $state = $elems->getCommonTbox('state', 'State:');
        $zipcode = $elems->getZipcodeTbox();
        $creds = $elems->getCreditAmtTbox();
        $addsubf = new Zend_Form_Element_Button('addsf');
        $addsubf->setAttrib('class', 'addsf')
                ->setLabel('Add a Site');
        $rmsubf = new Zend_Form_Element_Button('rmsf');
        $rmsubf->setAttrib('class', 'rmsf')
               ->setLabel('Remove a Site');

        $perInfo = new Zend_Form_Element_Hidden('perInfo');
        $perInfo->setLabel("PERSONAL INFORMATION");
        $this->textDeco($perInfo);

        $empinfoText = new Zend_Form_Element_Hidden('empinfoText0');
        $empinfoText->setLabel("EMPLOYMENT INFORMATION (If you are currently working at a 
                                job related to your major please describe below)");
        $this->textDeco($empinfoText);

        $partAgreement = new Zend_Form_Element_Hidden('partAgreement');
        $partAgreement->setLabel("STUDENT PARTICIPATION AGREEMENT");
        $this->textDeco($partAgreement);

        $this->submit->setAttrib('id', 'studentInfoSubmit');

                                           
            
        // Creates a personal information subform attached to the student information sheet
        $subf1 = $this->makePersSubf();

        
        // Creates an employment information subform attached to the student information sheet
        $subf2 = $this->makeEmpSubf();
        
        // FOR TEMPLATE
        $this->setDecorators( array( 
            array('ViewScript', array('viewScript' => '/form/stuinfo-form-template.phtml'))));

        // Add to the form
        //$this->addElement($perInfo);
        //$this->addSubForm($subf1, 'subf1');
        //$this->addElement($empinfoText);
        //$subf2->setElementsBelongTo("empinfo[0]");
        //$this->addSubForm($subf2, "empinfo[0]");
        //$this->addElements(array($addsubf, $rmsubf, $partAgreement, $this->agree, $this->submit ));


        // ORIGINAL ADD ELEMENTS WITHOUT TEMPLATE
        //$this->addElements(array($partAgreement, $this->agree, $this->submit ));

        $this->addElements(array($this->fname, $this->lname, $this->uuid, $this->address,
                           $wantedJob, $coopClass, $creds, $this->grad, $this->major, 
                           $this->semInMaj, $this->phone, $mobile, $this->email, 
                           $this->curJob, $this->sdate, $this->edate, $this->payRate, 
                           $this->employer, $this->department, $this->jobAddr, $this->supervEmail,
                           $this->supervName, $this->supervTitle, $this->supervPhone, 
                           $this->agree, $this->submit ));
                                                                                          
        
        // CLEAR DECORATORS FOR TEMPLATE
        $this->setElementDecorators(array('ViewHelper',
                                          "Errors"));


        //$this->setSubFormDecorators(array('FormElements',
        //                                  array('HtmlTag', array('tag' => 'table', 'class' => 'studentInfo'))
        //                           ));

        //// Add <span> to the form buttons
        //$this->setElementDecorators(array('ViewHelper',
        //                                   array('HtmlTag', array('tag' => 'span'))),
        //                                   array("addsf", "rmsf", "Submit")
        //                            );

        //// Add <br /> after agreement radio button
        //$this->setElementDecorators(array('ViewHelper',
        //                                  'Errors',
        //                                   array('HtmlTag', array('tag' => 'br', 'placement' => 'APPEND'))),
        //                                   array("agreement", "rmsf")
        //                            );
    }

    // Creates an employment information subform attached to the student information sheet
    public function makeEmpSubf()
    {
        $this->makeElems();
        $empSubf = new Zend_Form_SubForm();
        $empSubf->addElements(array(
                              $this->curJob, $this->sdate, $this->edate, $this->payRate, 
                              $this->department, $this->jobAddr, $this->supervEmail,
                              $this->supervName, $this->supervTitle, $this->supervPhone, 
                           ));
        $empSubf->addDisplayGroup(array('current_job', 'start_date', 'end_date', 'rate_of_pay'), 'fifthrow');
        $empSubf->addDisplayGroup(array('department', 'job_address', 'superv_name', 'superv_title'), 'sixthrow');
        $empSubf->addDisplayGroup(array('superv_phone', 'superv_email'), 'seventhrow');
        //$subf2->addDisplayGroup(array('agreement'), 'eighthrow');
        //$subf2->addDisplayGroup(array('Submit'), 'ninthrow');

        //$empSubf->setElementDecorators(array('ViewHelper',
        //                                   array('Label', array('tag' => 'br', 'placement' => 'PREPEND')),
        //                                   'Errors',
        //                                   array('HtmlTag', array('tag'=>'td'))
        //                             ));
        //$empSubf->setDisplayGroupDecorators(array('FormElements',
        //                                        array('HtmlTag', array('tag' => 'tr'))
        //                                 ));

        return $empSubf;
    }

    // Creates a personal information subform attached to the student information sheet
    public function makePersSubf()
    {
        $elems = new My_FormElement();        
        $mobile = $elems->getCommonTbox('mobile', 'Mobile phone:');
        $wantedJob = $elems->getCommonTbox('wanted_job', 'What job do you want for your co-op experience?');
        $coopClass = $elems->getCommonTbox('wanted_class', 'Which co-op class are you planning to enroll in?');
        $coopClass->setAttrib('disabled', true);
        $city = $elems->getCommonTbox('city', 'City:');
        $state = $elems->getCommonTbox('state', 'State:');
        $zipcode = $elems->getZipcodeTbox();
        $creds = $elems->getCreditAmtTbox();

        $subf1 = new Zend_Form_SubForm();
        $subf1->addElements(array($this->fname, $this->lname, $this->uuid, $this->address,
                           $wantedJob, $coopClass, $creds, $this->grad, $this->major, 
                           $this->semInMaj, $this->phone, $mobile, $this->email)); 

        $subf1->addDisplayGroup(array('fname', 'lname', 'uuid', 'address'), 'firstrow');
        $subf1->addDisplayGroup(array('wanted_job','wanted_class', 'credits', 'grad_date'), 'secondrow');
        $subf1->addDisplayGroup(array('majors_id', 'semester_in_major', 'phone', 'mobile'), 'thirdrow');
        $subf1->addDisplayGroup(array('email'), 'fourthrow');

        //$subf1->setElementDecorators(array('ViewHelper',
        //                                   array('Label', array('tag' => 'br', 'placement' => 'PREPEND')),
        //                                   'Errors',
        //                                   array('HtmlTag', array('tag'=>'td'))
        //                             ));
        //$subf1->setDisplayGroupDecorators(array('FormElements',
        //                                        array('HtmlTag', array('tag' => 'tr'))
        //                                  ));

        return $subf1;
    }

    public function textDeco($elem)
    {
        //$elem->setDecorators(array('ViewHelper',
        //                           array('Label', array('tag' => 'p', 'style' => 'font-size: 16px;')),//border-width:1px;border-style:solid;padding:10px')),
        //                           array('HtmlTag', array('tag' => 'br', 'placement' => 'PREPEND'))
        //                     ));
       
    }

    public function makeDynaForm($flag, $data)
    {
       $coopSess = new Zend_Session_Namespace('coop');

       if ($flag == "addsf") {
          //die('hi');
          $coopSess->subfcount++;
       } else if ($flag == "rmsf") {
          $coopSess->subfcount--;
          if ($coopSess->subfcount < 0) {
             $coopSess->subfcount = 0;
          }
       }

       $subfcount = $coopSess->subfcount;

       for ($i = 0; $i < $subfcount; $i++) {

          $sfnum = $i + 1;
          $sfname = "empinfo";
          $empInfoText = new Zend_Form_Element_Hidden("empinfoText$sfnum");
          //die(var_dump($empInfo));
          $empInfoText->setLabel("EMPLOYMENT INFORMATION (If you are currently working at a job related to your major please describe below)");
          $this->textDeco($empInfoText);
          //$empInfoText->setDecorators(array('ViewHelper',
          //                               array('Label', array('tag' => 'p', 'style' => 'font-size: 14px;border-width:1px;border-style:solid;padding:10px')),
          //                               array('HtmlTag', array('tag' => 'br', 'placement' => 'PREPEND'))
          //                         ));
          $this->addElement($empInfoText);
          $empSubf = $this->makeEmpSubf();
          $empSubf->setElementsBelongTo("$sfname\[$sfnum]");
          
          $this->addSubForm($empSubf, "$sfname\[$sfnum]");
          $addsf = $this->getElement('addsf');
          $this->removeElement('addsf');
          $rmsf = $this->getElement('rmsf');
          $this->removeElement('rmsf');
          $agreeLabel = $this->getElement('partAgreement');
          $this->removeElement('partAgreement');
          $agree = $this->getElement('agreement');
          $this->removeElement('agreement');
          $submit = $this->getElement('Submit');
          $this->removeElement('Submit');
          $this->addElements(array($addsf, $rmsf, $agreeLabel, $agree, $submit));

       }
       $this->populate($data);

       //return $this;

    }


}

