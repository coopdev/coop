<?php

class Application_Form_SupervisorEval extends Application_Form_CommonForm
{

    public function init()
    {
       $Assign = new My_Model_Assignment();
       $this->assignId = $Assign->getSupervisorEvalId();
       
       
       $this->setDecorators(array(array('ViewScript', 
                                  array('viewScript' => '/assignment/forms/supervisor-eval.phtml'))));

       $this->makeStatics();

       $this->makeDynamics();

       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');

       $this->addElements( array($saveSubmit, $finalSubmit));

       // Checks if there are submitted answers in order to populate the form with them.
       if ($this->populateForm === true) {
          $this->checkSubmittedAnswers(); 
       }

       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }

    
    
    



    private function makeStatics()
    {
       $staticTasks = new Zend_Form_SubForm();
       $staticTasks->setElementsBelongTo('static_tasks');
       $elems = new My_FormElement();

       
       $commonFields = $this->makeJobsiteFields();

       $staticTasks->addElements($commonFields);
       

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
       //$comments->get

       $overallEval = new Zend_Form_Element_Radio('overall_eval');
       $overallEval->setRequired(true)
                   ->setSeparator("")
                   ->setMultiOptions( array('1' => 'Excellent',
                                            '2' => 'Above Average',
                                            '3' => 'Satisfactory',
                                            '4' => 'Unsatisfactory'));
       
       
       
       //$static_tasks->addElements(array($position, $company, $hours, $semesters, $superv, 
       //             $phone, $avgHrs, $hrlyWage, $comments, $overallEval));
       
       $staticTasks->addElements(array($avgHrs, $hrlyWage, $comments, $overallEval));

       // Make all elements required except 'comments'.
       $temp = $staticTasks->getElements();
       foreach ($temp as $k => $v) {
          $v->setAttrib('class', 'static');
          if ($k !== 'comments') {
             $v->setRequired(true);
          }
       }

       //$static_tasks = array($position, $company, $hours, $semesters, $superv, $phone);
       //$staticTasks->setElementDecorators(array('ViewHelper',
       //                                    'Errors',
       //                                    'Label'
       //                              ));

       $this->addSubForm($staticTasks, 'static_tasks');

    }

    

    public function makeLearningObjectives()
    {
       $objective1 = new Zend_Form_Element_Text('lrnObjective1');
       $objective1->setRequired(true)
                ->setLabel('1.')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('placeholder', 'Enter Learning Objective')
                ->setAttrib('size', 85);

       $objective2 = new Zend_Form_Element_Text('lrnObjective2');
       $objective2->setRequired(true)
                ->setLabel('2.')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('placeholder', 'Enter Learning Objective')
                ->setAttrib('size', 85);

       $rating1 = new Zend_Form_Element_Radio('lrnObjectiveRating1');
       $rating1->setRequired(true)
               ->setLabel("Rate yourself on this learning objective")
               ->setMultiOptions($this->options)
               ->setSeparator("");

       $rating2 = new Zend_Form_Element_Radio('lrnObjectiveRating2');
       $rating2->setRequired(true)
               ->setLabel("Rate yourself on this learning objective")
               ->setMultiOptions($this->options)
               ->setSeparator("");

       $elems =  array($objective1, $objective2, $rating1, $rating2);


       return $elems;

    }

}

