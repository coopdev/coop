<?php

class Application_Form_SupervisorEval extends Application_Form_CommonForm
{

    public function init()
    {
       $Assign = new My_Model_Assignment();
       $this->assignId = $Assign->getSupervisorEvalId();
       
       
       $this->setDecorators(array(array('ViewScript', 
                                  array('viewScript' => '/assignment/forms/supervisor-eval.phtml'))));

       //die(var_dump($this->username, $this->classId, $this->semId));
       $this->makeStatics();

       $this->makeDynamics();

       // Do this in order to get the right question number on th
       $temp = $this->getSubForm('dynamic_tasks');
       $this->getSubForm('static_tasks')->getElement('other')->setLabel(count($temp) + 1 . '. Other (please specify):');

       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');
       $pdfSubmit = $elems->getSubmit('pdfSubmit');
       $pdfSubmit->setLabel('PDF');

       $this->addElements( array($saveSubmit, $finalSubmit, $pdfSubmit));

       // Checks if there are submitted answers in order to populate the form with them.
       if ($this->populateForm === true) {
          $this->checkSubmittedAnswers(); 
          $this->populateJobsiteFields();
          $this->populateCoordFields();
       }

       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }

    
    
    



    public function makeStatics()
    {
       $staticTasks = new Zend_Form_SubForm();
       $staticTasks->setElementsBelongTo('static_tasks');
       $elems = new My_FormElement();

       
       //$jobSiteSubform = $this->makeJobsiteSubform();
       //$staticTasks->addSubForm($jobSiteSubform, 'jobsite');

       $jobSiteFields = $this->makeJobsiteFields();

       $staticTasks->addElements($jobSiteFields);
       

       // Only Add learning outcomes if class is upper level.
       $Class = new My_Model_Class();
       $classRow = $Class->getClass($this->classId);
       // Add learning outcomes.
       if ($classRow['level'] === 'upper') {
          $lrnObjectives = $this->makeLearningObjectives();
          $staticTasks->addElements($lrnObjectives);
       }


       $avgHrs = $elems->getCommonTbox('avg_hrs', 'Average hours student worked per week during evaluation period:');
       //$avgHrs = new Zend_Form_Element_Text('avg_hrs');
       //$avgHrs->setLabel('foo');
              //->addFilter('StringTrim')
              //->addFilter('StripTags');
       
       $hrlyWage = $elems->getCommonTbox('hrly_wage', 'Hourly wage:');

       $comments = $elems->getCommonTarea('comments', '');
       $comments->setRequired(false);
       //$comments->get

       $overallEval = new Zend_Form_Element_Radio('overall_eval');
       $overallEval->setRequired(true)
                   ->setSeparator("")
                   ->setMultiOptions( array('1' => 'Excellent',
                                            '2' => 'Above Average',
                                            '3' => 'Satisfactory',
                                            '4' => 'Unsatisfactory'));
       
       $staticTasks->addElements($this->makeCoordFields());

       $staticTasks->addElements($this->makeOther());
       
       
       $staticTasks->addElements(array($avgHrs, $hrlyWage, $comments, $overallEval));

       // Make all elements required except 'comments'.
       $temp = $staticTasks->getElements();
       foreach ($temp as $k => $v) {
          $v->setAttrib('class', 'static');
       }

       //$static_tasks = array($position, $company, $hours, $semesters, $superv, $phone);
       //$staticTasks->setElementDecorators(array('ViewHelper',
       //                                    'Errors',
       //                                    'Label'
       //                              ));

       $this->addSubForm($staticTasks, 'static_tasks');

    }

    public function populateCoordFields()
    {
        $Class = new My_Model_Class();
        $classRow = $Class->getClassInfo(array('id' => $this->classId));
        $staticTasks = $this->static_tasks;

        $staticTasks->coordinator->setValue($classRow->fname . ' ' . $classRow->lname);
        
        $staticTasks->coord_phone->setValue($classRow->home_phone);

        $staticTasks->college->setValue('Honolulu Community College');
        
        $staticTasks->address->setValue("874 Dillingham Blvd., Honolulu, HI, 96817");
    }

    public function makeCoordFields()
    {
       $Class = new My_Model_Class();
       $classRow = $Class->getClassInfo(array('id' => $this->classId));
       $elems = new My_FormElement();

       $coord = $elems->getCommonTbox('coordinator', 'Coordinator:');
       $coord->setValue($classRow->fname . ' ' . $classRow->lname);
       
       $coordPhone = $elems->getCommonTbox('coord_phone', 'Telephone:');
       $coordPhone->setValue($classRow->home_phone);
       
       $college = $elems->getCommonTbox('college', 'College:');
       $college->setValue('Honolulu Community College');
       
       $coordEmail = $elems->getCommonTbox('coord_email', 'Email:');
       $coordEmail->setValue($classRow->email);
       
       $address = $elems->getCommonTbox('address', 'Address:');
       $address->setValue("874 Dillingham Blvd., Honolulu, HI, 96817");
       
       $fax = $elems->getCommonTbox('fax', 'Fax:');

       $elems = array($coord, $coordPhone, $college, $coordEmail, $address, $fax);

       foreach($elems as $e) {
          $e->setAttrib('size', '33');
       }

       return $elems;

    }

    

    public function makeLearningObjectives()
    {
       $objective1 = new Zend_Form_Element_Text('lrnObjective1');
       $objective1->setRequired(true)
                ->setLabel('1.')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('placeholder', 'Enter Learning Objective')
                ->setAttrib('getValFrom', 'agreementForm')
                ->setAttrib('size', 85);

       $objective2 = new Zend_Form_Element_Text('lrnObjective2');
       $objective2->setRequired(true)
                ->setLabel('2.')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('placeholder', 'Enter Learning Objective')
                ->setAttrib('getValFrom', 'agreementForm')
                ->setAttrib('size', 85);

       $rating1 = new Zend_Form_Element_Radio('lrnObjectiveRating1');
       $rating1->setRequired(true)
               ->setLabel("Rate student's performance on this learning objective")
               ->setMultiOptions($this->options)
               ->setSeparator("");

       $rating2 = new Zend_Form_Element_Radio('lrnObjectiveRating2');
       $rating2->setRequired(true)
               ->setLabel("Rate student's performance on this learning objective")
               ->setMultiOptions($this->options)
               ->setSeparator("");

       $elems =  array($objective1, $objective2, $rating1, $rating2);


       return $elems;

    }

}

