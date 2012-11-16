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

    public function init()
    {
       $this->setDecorators(array(array('ViewScript', 
                                  array('viewScript' => '/assignment/forms/supervisor-eval.phtml'))));

       $this->makeStatics();

       $this->makeDynamics();

       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }

    
    
    



    private function makeStatics()
    {
       $statics = new Zend_Form_SubForm();
       $elems = new My_FormElement();

       $position = $elems->getCommonTbox('position', 'Position:');

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
          $statics->addElements($lrnObjectives);
       }


       $avgHrs = $elems->getCommonTbox('avg_hrs', 'Average hours student worked per week during evaluation period:');
       //$avgHrs = $elems->getCommonTbox('avg_hrs','');
       $hrlyWage = $elems->getCommonTbox('hrly_wage', 'Hourly wage:');

       $comments = $elems->getCommonTarea('comments', '');
       
       
       
       $statics->addElements(array($position, $company, $hours, $semesters, $superv, $phone, $avgHrs, $hrlyWage, $comments));
       
       $statics->setElementsBelongTo('statics');

       //$statics = array($position, $company, $hours, $semesters, $superv, $phone);
       $statics->setElementDecorators(array('ViewHelper',
                                           'Errors',
                                           'Label'
                                     ));

       $this->addSubForm($statics, 'statics');

    }

    private function makeDynamics()
    {
       $aq = new My_Model_AssignmentQuestions();
       $Assignment = new My_Model_Assignment();
       $stuEvalId = $Assignment->getStudentEvalId();
       
       
       $questions = $aq->getQuestions(array('classes_id' => $this->classId, 'assignments_id' => $stuEvalId));
       //die(var_dump($questions));
       
       $dynamics = new Zend_Form_SubForm('dynamics');
       $dynamics->setDecorators(array('FormElements',
                                      array('HtmlTag', array('tag' => 'div'))
                                ));
       $dynamics->setElementsBelongTo('dynamics');

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
             
             $dynamics->addElement($elem);
          }

       }

       $this->addSubForm($dynamics, 'dynamics');
       


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
    

    public function setClassId($classId)
    {
       $this->classId = $classId;

    }


    public function setAssignId($assignId)
    {
       $this->classId = $assignId;

    }
}

