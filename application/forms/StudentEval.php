<?php

/*
 * Used in AssignmentController->studentEvalAction()
 *         AssignmentController->supervisorEvalAction()
 *         AssignmentController->supervisorEvalPdfAction()
 *         AsyncController->studentEvalAction()
 */
class Application_Form_StudentEval extends Zend_Form
{
    protected $classId;
    protected $assignId;

    // Determines whether the form should be populated through this class or not.
    // Form may still be populated from somewhere else.  Setting this to true just tells
    // it to do it from this class.
    protected $populateForm = true; 

    public function init()
    {
       $aq = new My_Model_AssignmentQuestions();
       $as = new My_Model_Assignment();

       if (isset($this->assignId)) {
          $asId = $this->assignId;
       } else {
          $this->assignId = $as->getStudentEvalId();
          $asId = $this->assignId;
       }


       $this->makeStaticTasks();


       //$questions = $aq->getChildParentQuestions(array('classId' => $this->classId, 'assignId' => $asId));
      $questions = $aq->getQuestions(array('classes_id' => $this->classId, 'assignments_id' => $asId));

       //die(var_dump($questions));


      $this->setDecorators(array(array('ViewScript', 
                                   array('viewScript' => '/assignment/student-eval-template.phtml'))));

      $options = $this->generateOptions();

      $dynamicTasks = new Zend_Form_SubForm('dynamic_tasks');
      $dynamicTasks->setDecorators(array('FormElements',
                                     array('HtmlTag', array('tag' => 'div'))
                               ));
      $dynamicTasks->setElementsBelongTo('dynamic_tasks');

      $qnum = 0;
      foreach ($questions as $q) {

         // Only include non parent type questions.
         if ($q['question_type'] !== 'parent') {
            $elem = new Zend_Form_Element_Radio($q['id']);
            $elem->setLabel(++$qnum . '. ' . $q['question_text'])
                 ->setRequired(true)
                 ->setAttrib('class', 'dynamic')
                 ->setSeparator('')
                 //->setMultiOptions($options);
                 ->setMultiOptions(array('4' => '4',
                                         '3' => '3',
                                         '2' => '2',
                                         '1' => '1',
                                         'NA' => 'NA'));
            
            $dynamicTasks->addElement($elem);
         }

         // Parent questions are not being used anymore since thsoe are static now.
         //} else {
            //$elem = new Zend_Form_Element_Hidden($q['id']);
            //$elem->setLabel($q['question_text']);
         //}

         //$this->addElements(array($elem));
      }

       //$dynamicTasks->setElementDecorators(array('ViewHelper',
       //                                          'Label',
       //                                          'Errors',
       //                                          array('HtmlTag', array('tag' => 'br', 'position' => 'after'))
       //    ));

       $this->addSubForm($dynamicTasks, 'dynamic_tasks');


       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');

       // Checks if there are submitted answers in order to populate the form with them.
       if ($this->populateForm === true) {
          $this->checkSubmittedAnswers(); 
       }


       // Only add submit buttons if there are questions and options.
       if (!empty($questions) && !empty($options)) {
          $this->addElements(array($saveSubmit,$finalSubmit));
       }


       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }



    /*
     * Creates the tasks and their ratings on the form which are static.
     */
    private function makeStaticTasks()
    {
       $staticTasks = new Zend_Form_SubForm('static_tasks');

       $staticTasks->setDecorators(array('FormElements',
                                         array('HtmlTag', array('tag' => 'div'))
                                   ));

       $staticTasks->setElementsBelongTo("static_tasks");
       $options = array('4' => '4', 
                        '3' => '3',
                        '2' => '2',
                        '1' => '1',
                        'NA' => 'NA');

       $task1 = new Zend_Form_Element_Radio('static_task1');
       $task1->setLabel("Relationship with Coop Coordinator");
       $task1->setMultiOptions($options);
       $task1->setSeparator("");
       $task1->setRequired(true);

       $task2 = new Zend_Form_Element_Radio('static_task2');
       $task2->setLabel("Quality of Work Assignments");
       $task2->setMultiOptions($options);
       $task2->setSeparator("");
       $task2->setRequired(true);

       $task3 = new Zend_Form_Element_Radio('static_task3');
       $task3->setLabel("Supervisor's Orientation to Job");
       $task3->setMultiOptions($options);
       $task3->setSeparator("");
       $task3->setRequired(true);

       $task4 = new Zend_Form_Element_Radio('static_task4');
       $task4->setLabel("Overall Value of Work Experience");
       $task4->setMultiOptions($options);
       $task4->setSeparator("");
       $task4->setRequired(true);

       
       $staticTasks->addElements(array($task1,$task2,$task3,$task4));

       $elems = $staticTasks->getElements();
       foreach ($elems as $e) {
          $e->setAttrib('class', 'static');
       }

       //die(var_dump($staticTasks));

       $this->addSubForm($staticTasks, 'static_tasks');


    }



    public function checkSubmittedAnswers()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       // If 
       if ($coopSess->role === 'user') {
          $where['username'] = $coopSess->username;
       } else if ($coopSess->role === 'coordinator') {
          $where['username'] = $coopSess->submitForStudentData['username'];
       }

       $where['classes_id'] = $this->classId;
       $where['assignments_id'] = $this->assignId;
       $where['semesters_id'] = $coopSess->currentSemId;

       $assign = new My_Model_Assignment();
       if ($assign->isSubmitted($where) || $assign->isSaveOnly($where)) {
          $assign->populateStudentEval($this, $where);
       }
    }


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

    public function setClassId($classId)
    {
       //die($classId);
       $this->classId = $classId;
    }

    public function setAssignId($assignId)
    {
       $this->assignId = $assignId;

    }

    public function setPopulateForm($flag)
    {
       $this->populateForm = $flag;
    }


}

