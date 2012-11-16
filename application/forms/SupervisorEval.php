<?php

class Application_Form_SupervisorEval extends Zend_Form
{
    protected $classId;
    protected $assignId;
    protected $options = array('4' => '4',
                               '3' => '3',
                               '2' => '2',
                               '1' => '1',
                               'NA' => 'NA');

    protected $populateForm = true;

    public function init()
    {
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
       $static_tasks = new Zend_Form_SubForm();
       $elems = new My_FormElement();

       $position = $elems->getCommonTbox('position', 'Position:');
       //die(var_dump($position->getId()));
       //die(var_dump($position));

       $company = $elems->getCommonTbox('company', 'Company:');

       $hours = $elems->getCommonTbox('hrs_per_week', 'Hrs/Week:');
       
       $semesters = $elems->getCommonTbox('semester_dates', 'Semester Dates:');
       
       $superv = $elems->getCommonTbox('superv', 'Supervisor:');
       
       $phone = $elems->getCommonTbox('phone', 'Telephone:');
       
       

       // Only Add learning outcomes if class is upper level.
       $Class = new My_Model_Class();
       $classRow = $Class->getClass($this->classId);
       // Add learning outcomes.
       if ($classRow['level'] === 'upper') {
          $lrnObjectives = $this->makeLearningObjectives();
          $static_tasks->addElements($lrnObjectives);
       }


       $avgHrs = $elems->getCommonTbox('avg_hrs', 'Average hours student worked per week during evaluation period:');
       //$avgHrs = $elems->getCommonTbox('avg_hrs','');
       $hrlyWage = $elems->getCommonTbox('hrly_wage', 'Hourly wage:');

       $comments = $elems->getCommonTarea('comments', '');
       //$comments->get

       $overallEval = new Zend_Form_Element_Radio('overall_eval');
       $overallEval->setRequired(true)
                   ->setSeparator("")
                   ->setMultiOptions( array('1' => 'Excelent',
                                            '2' => 'Above Average',
                                            '3' => 'Satisfactory',
                                            '4' => 'Unsatisfactory'));
       
       
       
       $static_tasks->addElements(array($position, $company, $hours, $semesters, $superv, 
                    $phone, $avgHrs, $hrlyWage, $comments, $overallEval));
       
       $static_tasks->setElementsBelongTo('static_tasks');

       // Make all elements required except 'comments'.
       $temp = $static_tasks->getElements();
       foreach ($temp as $k => $v) {
          $v->setAttrib('class', 'static');
          if ($k !== 'comments') {
             $v->setRequired(true);
          }
       }

       //$static_tasks = array($position, $company, $hours, $semesters, $superv, $phone);
       $static_tasks->setElementDecorators(array('ViewHelper',
                                           'Errors',
                                           'Label'
                                     ));

       $this->addSubForm($static_tasks, 'static_tasks');

    }

    private function makeDynamics()
    {
       $aq = new My_Model_AssignmentQuestions();
       $Assignment = new My_Model_Assignment();
       $stuEvalId = $Assignment->getStudentEvalId();
       
       
       $questions = $aq->getQuestions(array('classes_id' => $this->classId, 'assignments_id' => $stuEvalId));
       //die(var_dump($questions));
       
       $dynamic_tasks = new Zend_Form_SubForm('dynamic_tasks');
       $dynamic_tasks->setDecorators(array('FormElements',
                                      array('HtmlTag', array('tag' => 'div'))
                                ));
       $dynamic_tasks->setElementsBelongTo('dynamic_tasks');

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
                  ->setMultiOptions($this->options);
             
             $dynamic_tasks->addElement($elem);
          }

       }

       $this->addSubForm($dynamic_tasks, 'dynamic_tasks');
       


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

       foreach ($elems as $t) {
          $t->setDecorators( array('ViewHelper',
                                   'Errors',
                                   'Label'
                          ));
       }

       return $elems;

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

       //die(var_dump($this->assignId));
       $assign = new My_Model_Assignment();
       if ($assign->isSubmitted($where) || $assign->isSaveOnly($where)) {
          $assign->populateStudentEval($this, $where);
       }
    }
    

    public function setClassId($classId)
    {
       $this->classId = $classId;

    }


    public function setAssignId($assignId)
    {
       //die($assignId);
       $this->assignId = $assignId;

    }
}

