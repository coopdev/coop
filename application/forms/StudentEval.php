<?php

/*
 * Used in AssignmentController->studentEvalAction()
 *         AssignmentController->supervisorEvalPdfAction()
 *         AsyncController->studentEvalAction()
 */
class Application_Form_StudentEval extends Application_Form_CommonForm
{

    public function init()
    {
       $as = new My_Model_Assignment();
       $this->assignId = $as->getStudentEvalId();

       $this->setDecorators(array(array('ViewScript', 
                                 array('viewScript' => '/assignment/student-eval-template.phtml'))));


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

       $this->addElements(array($saveSubmit,$finalSubmit));
       
       
       // Checks if there are submitted answers in order to populate the form with them.
       if ($this->populateForm === true) {
          $this->checkSubmittedAnswers(); 
       }

       $this->setElementDecorators(array('ViewHelper',
                                         'Errors'
                                  ));
    }



    /*
     * Creates the tasks and their ratings on the form which are static.
     */
    private function makeStatics()
    {
       $staticTasks = new Zend_Form_SubForm('static_tasks');

       $staticTasks->setDecorators(array('FormElements',
                                         array('HtmlTag', array('tag' => 'div'))
                                   ));

       $staticTasks->setElementsBelongTo("static_tasks");

       $task1 = new Zend_Form_Element_Radio('static_task1');
       $task1->setLabel("Relationship with Coop Coordinator");
       $task1->setMultiOptions($this->options);
       $task1->setSeparator("");
       $task1->setRequired(true);

       $task2 = new Zend_Form_Element_Radio('static_task2');
       $task2->setLabel("Quality of Work Assignments");
       $task2->setMultiOptions($this->options);
       $task2->setSeparator("");
       $task2->setRequired(true);

       $task3 = new Zend_Form_Element_Radio('static_task3');
       $task3->setLabel("Supervisor's Orientation to Job");
       $task3->setMultiOptions($this->options);
       $task3->setSeparator("");
       $task3->setRequired(true);

       $task4 = new Zend_Form_Element_Radio('static_task4');
       $task4->setLabel("Overall Value of Work Experience");
       $task4->setMultiOptions($this->options);
       $task4->setSeparator("");
       $task4->setRequired(true);

       $staticTasks->addElements($this->makeOther());
       
       $staticTasks->addElements(array($task1,$task2,$task3,$task4));


       // Only Add learning outcomes if class is upper level.
       $Class = new My_Model_Class();
       $classRow = $Class->getClass($this->classId);
       // Add learning outcomes.
       if ($classRow['level'] === 'upper') {
          $lrnOutcomes = $this->makeLearningOutcomes();
          $staticTasks->addElements($lrnOutcomes);
       }


       $staticTasks->addElement($this->makeComment());

       $elems = $staticTasks->getElements();
       foreach ($elems as $k => $v) {
          $v->setAttrib('class', 'static');
       }

       $this->addSubForm($staticTasks, 'static_tasks');


    }


    public function makeLearningOutcomes()
    {
       $outcome1 = new Zend_Form_Element_Text('lrnOutcome1');
       $outcome1->setRequired(true)
                ->setLabel('1.')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('placeholder', 'Enter Learning Outcome')
                ->setAttrib('size', 85);

       $outcome2 = new Zend_Form_Element_Text('lrnOutcome2');
       $outcome2->setRequired(true)
                ->setLabel('2.')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->setAttrib('placeholder', 'Enter Learning Outcome')
                ->setAttrib('size', 85);

       $rating1 = new Zend_Form_Element_Radio('lrnOutcomeRating1');
       $rating1->setRequired(true)
               ->setLabel("Rate yourself on this learning outcome")
               ->setMultiOptions($this->options)
               ->setSeparator("");

       $rating2 = new Zend_Form_Element_Radio('lrnOutcomeRating2');
       $rating2->setRequired(true)
               ->setLabel("Rate yourself on this learning outcome")
               ->setMultiOptions($this->options)
               ->setSeparator("");
       

       $elems =  array($outcome1, $outcome2, $rating1, $rating2);
       
       $Class = new My_Model_Class();
       if ($Class->isFire193V($this->classId)) {
           $objective3 = new Zend_Form_Element_Text('lrnOutcome3');
           $objective3->setRequired(true)
                    ->setLabel('3.')
                    ->addFilter('StringTrim')
                    ->addFilter('StripTags')
                    ->setAttrib('placeholder', 'Enter Learning Outcome')
                    ->setAttrib('size', 85);

           $rating3 = new Zend_Form_Element_Radio('lrnOutcomeRating3');
           $rating3->setRequired(true)
                   ->setLabel("Rate yourself on this learning outcome")
                   ->setMultiOptions($this->options)
                   ->setSeparator("");

           $elems[] = $objective3;
           $elems[] = $rating3;

       }

       foreach ($elems as $t) {
          $t->setDecorators( array('ViewHelper',
                                   'Errors',
                                   'Label'
                          ));
       }

       return $elems;

    }

    public function makeComment()
    {
       $comment = new Zend_Form_Element_Textarea('comment');

       $comment->addFilter('StringTrim')
               ->addFilter('StripTags')
               ->setRequired(true)
               ->setLabel('Comments:');

       return $comment;
    }


    // NOT USED ANYMORE.
    public function generateOptions()
    {
       $assign = new My_Model_Assignment();
       $data['assignments_id'] = $this->assignId;
       $data['classes_id'] = $this->classId;

       $amount = $assign->getSurveyOptionAmount($data);

       $options = array();
       for ($i = 1; $i <= $amount; $i++) {
          $options[$i] = $i;
       }
       //die(var_dump($options));

       return $options;
       
    }
    
    /*
    // Changes they way validation of the form is checks depending on whether the save only
    // button was clicked or the final submit button.
    public function isValidIncludeSaveonly($formData)
    {
       // for some reason, isValid() is needed before using isChecked().
       $this->isValid($formData);
       
       // if user clicked the save only button, then use isValidPartial();
       if ($this->saveOnly->isChecked()) {
          if ($this->isValidPartial($formData)) {
             return true;
          }
       // else if user clicked final submit button then use isValid();
       } else if ($this->finalSubmit->isChecked()) {
          if ($this->isValid($formData)) {
             die('foo');
             return true;
          }
       }
       return false;
    }
     * 
     */


}

